<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Skill;
use App\Models\Employee;
use App\Models\Department;

class SkillTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_skill_index_requires_authentication(): void
    {
        $response = $this->get(route('skills.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_skill_index(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('skills.index'));

        $response->assertOk()
            ->assertViewIs('skills.index')
            ->assertViewHas('skills')
            ->assertSee($skill->name);
    }

    public function test_skill_index_shows_employee_count(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employees = Employee::factory()->count(2)->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employees->pluck('id'));

        $response = $this->actingAs($this->user)
            ->get(route('skills.index'));

        $response->assertOk()
            ->assertSee('2 employees');
    }

    public function test_authenticated_user_can_view_skill_create_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('skills.create'));

        $response->assertOk()
            ->assertViewIs('skills.create')
            ->assertSee('Add New Skill');
    }

    public function test_authenticated_user_can_create_skill(): void
    {
        $skillData = [
            'name' => 'New Skill',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('skills.store'), $skillData);

        $response->assertRedirect(route('skills.index'))
            ->assertSessionHas('success', 'Skill created successfully.');

        $this->assertDatabaseHas('skills', [
            'name' => 'New Skill',
        ]);
    }

    public function test_skill_creation_validates_required_name(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('skills.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_skill_creation_validates_unique_name(): void
    {
        $existingSkill = Skill::factory()->create(['name' => 'PHP']);

        $skillData = [
            'name' => 'PHP',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('skills.store'), $skillData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authenticated_user_can_view_skill_details(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employees = Employee::factory()->count(2)->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employees->pluck('id'));

        $response = $this->actingAs($this->user)
            ->get(route('skills.show', $skill));

        $response->assertOk()
            ->assertViewIs('skills.show')
            ->assertViewHas('skill')
            ->assertSee($skill->name)
            ->assertSee($employees[0]->full_name)
            ->assertSee($employees[1]->full_name);
    }

    public function test_authenticated_user_can_view_skill_edit_form(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('skills.edit', $skill));

        $response->assertOk()
            ->assertViewIs('skills.edit')
            ->assertViewHas('skill')
            ->assertSee($skill->name);
    }

    public function test_authenticated_user_can_update_skill(): void
    {
        $skill = Skill::factory()->create(['name' => 'Old Skill']);

        $updateData = [
            'name' => 'Updated Skill Name',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('skills.update', $skill), $updateData);

        $response->assertRedirect(route('skills.index'))
            ->assertSessionHas('success', 'Skill updated successfully.');

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Updated Skill Name',
        ]);
    }

    public function test_skill_update_validates_unique_name_excluding_current(): void
    {
        $skill1 = Skill::factory()->create(['name' => 'Skill 1']);
        $skill2 = Skill::factory()->create(['name' => 'Skill 2']);

        $updateData = [
            'name' => 'Skill 2', // Using another skill's name
        ];

        $response = $this->actingAs($this->user)
            ->put(route('skills.update', $skill1), $updateData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authenticated_user_can_delete_empty_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('skills.destroy', $skill));

        $response->assertRedirect(route('skills.index'))
            ->assertSessionHas('success', 'Skill deleted successfully.');

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_cannot_delete_skill_with_employees(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employee->id);

        $response = $this->actingAs($this->user)
            ->delete(route('skills.destroy', $skill));

        $response->assertRedirect(route('skills.index'))
            ->assertSessionHas('error', 'Cannot delete skill assigned to employees.');

        $this->assertDatabaseHas('skills', ['id' => $skill->id]);
    }

    public function test_skill_show_displays_no_employees_message_when_empty(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('skills.show', $skill));

        $response->assertOk()
            ->assertSee('No employees have this skill');
    }

    public function test_skill_index_shows_zero_employees_for_empty_skill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('skills.index'));

        $response->assertOk()
            ->assertSee('0 employees');
    }
}