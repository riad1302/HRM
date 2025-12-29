<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Skill;
use App\Services\EmployeeService;

class EmployeeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Department $department;
    protected Skill $skill1;
    protected Skill $skill2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->department = Department::factory()->create(['name' => 'Engineering']);
        $this->skill1 = Skill::factory()->create(['name' => 'PHP']);
        $this->skill2 = Skill::factory()->create(['name' => 'Laravel']);
    }

    public function test_employee_index_requires_authentication(): void
    {
        $response = $this->get(route('employees.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_employee_index(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('employees.index'));

        $response->assertOk()
            ->assertViewIs('employees.index')
            ->assertViewHas('employees')
            ->assertViewHas('departments')
            ->assertSee($employee->full_name);
    }

    public function test_employee_index_can_filter_by_department(): void
    {
        $department2 = Department::factory()->create(['name' => 'Marketing']);
        
        $employee1 = Employee::factory()->create(['department_id' => $this->department->id]);
        $employee2 = Employee::factory()->create(['department_id' => $department2->id]);

        $response = $this->actingAs($this->user)
            ->get(route('employees.index', ['department_id' => $this->department->id]));

        $response->assertOk()
            ->assertSee($employee1->full_name)
            ->assertDontSee($employee2->full_name);
    }

    public function test_employee_index_ajax_filter_returns_json(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxCall('GET', route('employees.index', ['department_id' => $this->department->id]));

        $response->assertOk()
            ->assertJsonStructure(['html']);
    }

    public function test_authenticated_user_can_view_employee_create_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('employees.create'));

        $response->assertOk()
            ->assertViewIs('employees.create')
            ->assertViewHas('departments')
            ->assertViewHas('skills')
            ->assertSee($this->department->name)
            ->assertSee($this->skill1->name);
    }

    public function test_authenticated_user_can_create_employee_without_skills(): void
    {
        $employeeData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'department_id' => $this->department->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('employees.store'), $employeeData);

        $response->assertRedirect(route('employees.index'))
            ->assertSessionHas('success', 'Employee created successfully.');

        $this->assertDatabaseHas('employees', [
            'first_name' => $employeeData['first_name'],
            'last_name' => $employeeData['last_name'],
            'email' => $employeeData['email'],
            'department_id' => $this->department->id,
        ]);
    }

    public function test_authenticated_user_can_create_employee_with_skills(): void
    {
        $employeeData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'department_id' => $this->department->id,
            'skills' => [$this->skill1->id, $this->skill2->id],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('employees.store'), $employeeData);

        $response->assertRedirect(route('employees.index'))
            ->assertSessionHas('success', 'Employee created successfully.');

        $employee = Employee::where('email', $employeeData['email'])->first();
        
        $this->assertDatabaseHas('employees', [
            'first_name' => $employeeData['first_name'],
            'last_name' => $employeeData['last_name'],
            'email' => $employeeData['email'],
        ]);

        $this->assertTrue($employee->skills->contains($this->skill1));
        $this->assertTrue($employee->skills->contains($this->skill2));
    }

    public function test_employee_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('employees.store'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'department_id',
        ]);
    }

    public function test_employee_creation_validates_unique_email(): void
    {
        $existingEmployee = Employee::factory()->create();

        $employeeData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $existingEmployee->email,
            'department_id' => $this->department->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('employees.store'), $employeeData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_authenticated_user_can_view_employee_details(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);
        $employee->skills()->attach([$this->skill1->id, $this->skill2->id]);

        $response = $this->actingAs($this->user)
            ->get(route('employees.show', $employee));

        $response->assertOk()
            ->assertViewIs('employees.show')
            ->assertViewHas('employee')
            ->assertSee($employee->full_name)
            ->assertSee($employee->email)
            ->assertSee($this->department->name)
            ->assertSee($this->skill1->name)
            ->assertSee($this->skill2->name);
    }

    public function test_authenticated_user_can_view_employee_edit_form(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);
        $employee->skills()->attach($this->skill1->id);

        $response = $this->actingAs($this->user)
            ->get(route('employees.edit', $employee));

        $response->assertOk()
            ->assertViewIs('employees.edit')
            ->assertViewHas('employee')
            ->assertViewHas('departments')
            ->assertViewHas('skills')
            ->assertSee($employee->first_name)
            ->assertSee($employee->last_name)
            ->assertSee($employee->email);
    }

    public function test_authenticated_user_can_update_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);

        $updateData = [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
            'email' => 'updated@example.com',
            'department_id' => $this->department->id,
            'skills' => [$this->skill1->id, $this->skill2->id],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('employees.update', $employee), $updateData);

        $response->assertRedirect(route('employees.index'))
            ->assertSessionHas('success', 'Employee updated successfully.');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
            'email' => 'updated@example.com',
        ]);

        $employee->refresh();
        $this->assertTrue($employee->skills->contains($this->skill1));
        $this->assertTrue($employee->skills->contains($this->skill2));
    }

    public function test_employee_update_validates_unique_email_excluding_current(): void
    {
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        $updateData = [
            'first_name' => $employee1->first_name,
            'last_name' => $employee1->last_name,
            'email' => $employee2->email, // Using another employee's email
            'department_id' => $employee1->department_id,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('employees.update', $employee1), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_authenticated_user_can_delete_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->department->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('employees.destroy', $employee));

        $response->assertRedirect(route('employees.index'))
            ->assertSessionHas('success', 'Employee deleted successfully.');

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_check_email_endpoint_returns_correct_availability(): void
    {
        $existingEmployee = Employee::factory()->create();

        // Test with existing email
        $response = $this->actingAs($this->user)
            ->postJson(route('check.email'), ['email' => $existingEmployee->email]);

        $response->assertOk()
            ->assertJson(['exists' => true]);

        // Test with new email
        $response = $this->actingAs($this->user)
            ->postJson(route('check.email'), ['email' => 'new@example.com']);

        $response->assertOk()
            ->assertJson(['exists' => false]);

        // Test with existing email but excluding current employee
        $response = $this->actingAs($this->user)
            ->postJson(route('check.email'), [
                'email' => $existingEmployee->email,
                'employee_id' => $existingEmployee->id
            ]);

        $response->assertOk()
            ->assertJson(['exists' => false]);
    }

    protected function ajaxCall($method, $uri, $data = [])
    {
        return $this->call($method, $uri, $data, [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);
    }
}