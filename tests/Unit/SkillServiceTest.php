<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Skill;
use App\Models\Employee;
use App\Models\Department;
use App\Services\SkillService;

class SkillServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SkillService $skillService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skillService = new SkillService();
    }

    public function test_can_get_all_skills_with_employee_count(): void
    {
        Skill::factory()->count(3)->create();

        $skills = $this->skillService->getAllSkillsWithEmployeeCount();

        $this->assertCount(3, $skills);
        $this->assertArrayHasKey('employees_count', $skills->first()->toArray());
    }

    public function test_can_create_skill(): void
    {
        $skillData = [
            'name' => 'JavaScript',
        ];

        $skill = $this->skillService->createSkill($skillData);

        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertEquals('JavaScript', $skill->name);
        $this->assertDatabaseHas('skills', ['name' => 'JavaScript']);
    }

    public function test_can_update_skill(): void
    {
        $skill = Skill::factory()->create(['name' => 'Old Skill']);
        
        $updateData = [
            'name' => 'Updated Skill',
        ];

        $updatedSkill = $this->skillService->updateSkill($skill, $updateData);

        $this->assertEquals('Updated Skill', $updatedSkill->name);
        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Updated Skill'
        ]);
    }

    public function test_can_delete_skill_without_employees(): void
    {
        $skill = Skill::factory()->create();

        $result = $this->skillService->deleteSkill($skill);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_cannot_delete_skill_with_employees(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employee->id);

        $result = $this->skillService->deleteSkill($skill);

        $this->assertFalse($result);
        $this->assertDatabaseHas('skills', ['id' => $skill->id]);
    }

    public function test_can_check_if_skill_has_employees(): void
    {
        $skillWithEmployees = Skill::factory()->create();
        $skillWithoutEmployees = Skill::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        
        $skillWithEmployees->employees()->attach($employee->id);

        $this->assertTrue($this->skillService->hasSkillEmployees($skillWithEmployees));
        $this->assertFalse($this->skillService->hasSkillEmployees($skillWithoutEmployees));
    }

    public function test_get_skills_with_employees_count(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employees = Employee::factory()->count(3)->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employees->pluck('id'));

        $skills = $this->skillService->getAllSkillsWithEmployeeCount();
        $skillWithCount = $skills->first();

        $this->assertEquals(3, $skillWithCount->employees_count);
    }

    public function test_get_skill_with_employees_loads_relation(): void
    {
        $skill = Skill::factory()->create();
        $department = Department::factory()->create();
        $employees = Employee::factory()->count(2)->create(['department_id' => $department->id]);
        
        $skill->employees()->attach($employees->pluck('id'));

        $skillWithEmployees = $this->skillService->getSkillWithEmployees($skill);

        $this->assertTrue($skillWithEmployees->relationLoaded('employees'));
        $this->assertCount(2, $skillWithEmployees->employees);
    }

    public function test_can_check_if_can_delete_skill(): void
    {
        $emptySkill = Skill::factory()->create();
        $skillWithEmployees = Skill::factory()->create();
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        
        $skillWithEmployees->employees()->attach($employee->id);

        $this->assertTrue($this->skillService->canDeleteSkill($emptySkill));
        $this->assertFalse($this->skillService->canDeleteSkill($skillWithEmployees));
    }

    public function test_get_skill_index_data_returns_proper_structure(): void
    {
        Skill::factory()->count(2)->create();

        $data = $this->skillService->getSkillIndexData();

        $this->assertArrayHasKey('skills', $data);
        $this->assertCount(2, $data['skills']);
    }

    public function test_get_skill_show_data_returns_proper_structure(): void
    {
        $skill = Skill::factory()->create();

        $data = $this->skillService->getSkillShowData($skill);

        $this->assertArrayHasKey('skill', $data);
        $this->assertTrue($data['skill']->relationLoaded('employees'));
    }

    public function test_get_skill_edit_data_returns_proper_structure(): void
    {
        $skill = Skill::factory()->create();

        $data = $this->skillService->getSkillEditData($skill);

        $this->assertArrayHasKey('skill', $data);
        $this->assertEquals($skill->id, $data['skill']->id);
    }
}