<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\Employee;

class DepartmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_department_index_requires_authentication(): void
    {
        $response = $this->get(route('departments.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_department_index(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('departments.index'));

        $response->assertOk()
            ->assertViewIs('departments.index')
            ->assertViewHas('departments')
            ->assertSee($department->name);
    }

    public function test_department_index_shows_employee_count(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->count(3)->create(['department_id' => $department->id]);

        $response = $this->actingAs($this->user)
            ->get(route('departments.index'));

        $response->assertOk()
            ->assertSee('3 employees');
    }

    public function test_authenticated_user_can_view_department_create_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('departments.create'));

        $response->assertOk()
            ->assertViewIs('departments.create')
            ->assertSee('Add New Department');
    }

    public function test_authenticated_user_can_create_department(): void
    {
        $departmentData = [
            'name' => 'New Department',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('departments.store'), $departmentData);

        $response->assertRedirect(route('departments.index'))
            ->assertSessionHas('success', 'Department created successfully.');

        $this->assertDatabaseHas('departments', [
            'name' => 'New Department',
        ]);
    }

    public function test_department_creation_validates_required_name(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('departments.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_department_creation_validates_unique_name(): void
    {
        $existingDepartment = Department::factory()->create(['name' => 'Engineering']);

        $departmentData = [
            'name' => 'Engineering',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('departments.store'), $departmentData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authenticated_user_can_view_department_details(): void
    {
        $department = Department::factory()->create();
        $employees = Employee::factory()->count(2)->create(['department_id' => $department->id]);

        $response = $this->actingAs($this->user)
            ->get(route('departments.show', $department));

        $response->assertOk()
            ->assertViewIs('departments.show')
            ->assertViewHas('department')
            ->assertSee($department->name)
            ->assertSee($employees[0]->full_name)
            ->assertSee($employees[1]->full_name);
    }

    public function test_authenticated_user_can_view_department_edit_form(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('departments.edit', $department));

        $response->assertOk()
            ->assertViewIs('departments.edit')
            ->assertViewHas('department')
            ->assertSee($department->name);
    }

    public function test_authenticated_user_can_update_department(): void
    {
        $department = Department::factory()->create(['name' => 'Old Name']);

        $updateData = [
            'name' => 'Updated Department Name',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('departments.update', $department), $updateData);

        $response->assertRedirect(route('departments.index'))
            ->assertSessionHas('success', 'Department updated successfully.');

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Department Name',
        ]);
    }

    public function test_department_update_validates_unique_name_excluding_current(): void
    {
        $department1 = Department::factory()->create(['name' => 'Department 1']);
        $department2 = Department::factory()->create(['name' => 'Department 2']);

        $updateData = [
            'name' => 'Department 2', // Using another department's name
        ];

        $response = $this->actingAs($this->user)
            ->put(route('departments.update', $department1), $updateData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authenticated_user_can_delete_empty_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('departments.destroy', $department));

        $response->assertRedirect(route('departments.index'))
            ->assertSessionHas('success', 'Department deleted successfully.');

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_cannot_delete_department_with_employees(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->create(['department_id' => $department->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('departments.destroy', $department));

        $response->assertRedirect(route('departments.index'))
            ->assertSessionHas('error', 'Cannot delete department with existing employees.');

        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }

    public function test_department_show_displays_no_employees_message_when_empty(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('departments.show', $department));

        $response->assertOk()
            ->assertSee('No employees assigned to this department');
    }

    public function test_department_index_shows_zero_employees_for_empty_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('departments.index'));

        $response->assertOk()
            ->assertSee('0 employees');
    }
}
