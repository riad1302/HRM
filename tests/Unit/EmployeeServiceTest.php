<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Skill;
use App\Services\EmployeeService;

class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeService $employeeService;
    protected Department $department;
    protected Skill $skill;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeService = new EmployeeService();
        $this->department = Department::factory()->create();
        $this->skill = Skill::factory()->create();
    }

    public function test_can_get_all_employees(): void
    {
        Employee::factory()->count(3)->create(['department_id' => $this->department->id]);

        $employees = $this->employeeService->getEmployeesList();

        $this->assertCount(3, $employees);
        $this->assertTrue($employees->first()->relationLoaded('department'));
        $this->assertTrue($employees->first()->relationLoaded('skills'));
    }

    public function test_can_filter_employees_by_department(): void
    {
        $department2 = Department::factory()->create();
        
        Employee::factory()->count(2)->create(['department_id' => $this->department->id]);
        Employee::factory()->count(3)->create(['department_id' => $department2->id]);

        $employees = $this->employeeService->getEmployeesList($this->department->id);

        $this->assertCount(2, $employees);
        $this->assertTrue($employees->every(fn($employee) => $employee->department_id === $this->department->id));
    }

    public function test_can_create_employee_without_skills(): void
    {
        $employeeData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'department_id' => $this->department->id,
        ];

        $employee = $this->employeeService->createEmployee($employeeData);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('John', $employee->first_name);
        $this->assertEquals('Doe', $employee->last_name);
        $this->assertEquals('john@example.com', $employee->email);
        $this->assertEquals($this->department->id, $employee->department_id);
        $this->assertCount(0, $employee->skills);
    }

    public function test_can_create_employee_with_skills(): void
    {
        $skill2 = Skill::factory()->create();
        
        $employeeData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'department_id' => $this->department->id,
            'skills' => [$this->skill->id, $skill2->id],
        ];

        $employee = $this->employeeService->createEmployee($employeeData);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertCount(2, $employee->skills);
        $this->assertTrue($employee->skills->contains($this->skill));
        $this->assertTrue($employee->skills->contains($skill2));
    }

    public function test_can_update_employee_and_sync_skills(): void
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);
        $skill2 = Skill::factory()->create();
        
        $updateData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'department_id' => $this->department->id,
            'skills' => [$this->skill->id, $skill2->id],
        ];

        $updatedEmployee = $this->employeeService->updateEmployee($employee, $updateData);

        $this->assertEquals('Updated', $updatedEmployee->first_name);
        $this->assertEquals('Name', $updatedEmployee->last_name);
        $this->assertEquals('updated@example.com', $updatedEmployee->email);
        $this->assertCount(2, $updatedEmployee->skills);
    }

    public function test_can_delete_employee(): void
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);
        $employeeId = $employee->id;

        $result = $this->employeeService->deleteEmployee($employee);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('employees', ['id' => $employeeId]);
    }

    public function test_check_email_uniqueness(): void
    {
        $employee = Employee::factory()->create(['email' => 'test@example.com']);

        // Test existing email
        $exists = $this->employeeService->checkEmailUniqueness('test@example.com');
        $this->assertTrue($exists);

        // Test non-existing email
        $exists = $this->employeeService->checkEmailUniqueness('new@example.com');
        $this->assertFalse($exists);

        // Test existing email but excluding current employee
        $exists = $this->employeeService->checkEmailUniqueness('test@example.com', $employee->id);
        $this->assertFalse($exists);
    }

    public function test_get_employee_create_data_returns_departments_and_skills(): void
    {
        $data = $this->employeeService->getEmployeeCreateData();

        $this->assertArrayHasKey('departments', $data);
        $this->assertArrayHasKey('skills', $data);
        $this->assertNotEmpty($data['departments']);
        $this->assertNotEmpty($data['skills']);
    }

    public function test_get_employee_edit_data_returns_proper_structure(): void
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $data = $this->employeeService->getEmployeeEditData($employee);

        $this->assertArrayHasKey('employee', $data);
        $this->assertArrayHasKey('departments', $data);
        $this->assertArrayHasKey('skills', $data);
        $this->assertEquals($employee->id, $data['employee']->id);
        $this->assertTrue($data['employee']->relationLoaded('skills'));
    }

    public function test_prepare_employee_for_show_loads_relations(): void
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $preparedEmployee = $this->employeeService->prepareEmployeeForShow($employee);

        $this->assertTrue($preparedEmployee->relationLoaded('department'));
        $this->assertTrue($preparedEmployee->relationLoaded('skills'));
    }
}
