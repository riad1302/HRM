<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentService;

class DepartmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DepartmentService $departmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->departmentService = new DepartmentService();
    }

    public function test_can_get_all_departments_with_employee_count(): void
    {
        Department::factory()->count(3)->create();

        $departments = $this->departmentService->getAllDepartmentsWithEmployeeCount();

        $this->assertCount(3, $departments);
        $this->assertArrayHasKey('employees_count', $departments->first()->toArray());
    }

    public function test_can_create_department(): void
    {
        $departmentData = [
            'name' => 'Human Resources',
        ];

        $department = $this->departmentService->createDepartment($departmentData);

        $this->assertInstanceOf(Department::class, $department);
        $this->assertEquals('Human Resources', $department->name);
        $this->assertDatabaseHas('departments', ['name' => 'Human Resources']);
    }

    public function test_can_update_department(): void
    {
        $department = Department::factory()->create(['name' => 'Old Name']);
        
        $updateData = [
            'name' => 'Updated Name',
        ];

        $updatedDepartment = $this->departmentService->updateDepartment($department, $updateData);

        $this->assertEquals('Updated Name', $updatedDepartment->name);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_can_delete_empty_department(): void
    {
        $department = Department::factory()->create();

        $result = $this->departmentService->deleteDepartment($department);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_cannot_delete_department_with_employees(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->create(['department_id' => $department->id]);

        $result = $this->departmentService->deleteDepartment($department);

        $this->assertFalse($result);
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }

    public function test_can_check_if_department_has_employees(): void
    {
        $departmentWithEmployees = Department::factory()->create();
        $departmentWithoutEmployees = Department::factory()->create();
        
        Employee::factory()->create(['department_id' => $departmentWithEmployees->id]);

        $this->assertTrue($this->departmentService->hasDepartmentEmployees($departmentWithEmployees));
        $this->assertFalse($this->departmentService->hasDepartmentEmployees($departmentWithoutEmployees));
    }

    public function test_get_department_with_employees_count(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->count(3)->create(['department_id' => $department->id]);

        $departments = $this->departmentService->getAllDepartmentsWithEmployeeCount();
        $departmentWithCount = $departments->first();

        $this->assertEquals(3, $departmentWithCount->employees_count);
    }

    public function test_get_department_with_employees_loads_relation(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->count(2)->create(['department_id' => $department->id]);

        $departmentWithEmployees = $this->departmentService->getDepartmentWithEmployees($department);

        $this->assertTrue($departmentWithEmployees->relationLoaded('employees'));
        $this->assertCount(2, $departmentWithEmployees->employees);
    }

    public function test_can_check_if_can_delete_department(): void
    {
        $emptyDepartment = Department::factory()->create();
        $departmentWithEmployees = Department::factory()->create();
        Employee::factory()->create(['department_id' => $departmentWithEmployees->id]);

        $this->assertTrue($this->departmentService->canDeleteDepartment($emptyDepartment));
        $this->assertFalse($this->departmentService->canDeleteDepartment($departmentWithEmployees));
    }

    public function test_get_department_index_data_returns_proper_structure(): void
    {
        Department::factory()->count(2)->create();

        $data = $this->departmentService->getDepartmentIndexData();

        $this->assertArrayHasKey('departments', $data);
        $this->assertCount(2, $data['departments']);
    }

    public function test_get_department_show_data_returns_proper_structure(): void
    {
        $department = Department::factory()->create();

        $data = $this->departmentService->getDepartmentShowData($department);

        $this->assertArrayHasKey('department', $data);
        $this->assertTrue($data['department']->relationLoaded('employees'));
    }

    public function test_get_department_edit_data_returns_proper_structure(): void
    {
        $department = Department::factory()->create();

        $data = $this->departmentService->getDepartmentEditData($department);

        $this->assertArrayHasKey('department', $data);
        $this->assertEquals($department->id, $data['department']->id);
    }
}