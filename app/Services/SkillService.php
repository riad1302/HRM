<?php

namespace App\Services;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SkillService
{
    public function getAllSkillsWithEmployeeCount(): Collection
    {
        return Skill::withCount('employees')->get();
    }

    public function getSkillWithEmployees(Skill $skill): Skill
    {
        return $skill->load('employees');
    }

    public function createSkill(array $validatedData): Skill
    {
        return Skill::create($validatedData);
    }

    public function updateSkill(Skill $skill, array $validatedData): Skill
    {
        $skill->update($validatedData);
        return $skill;
    }

    public function deleteSkill(Skill $skill): bool
    {
        if ($this->hasSkillEmployees($skill)) {
            return false;
        }

        return $skill->delete();
    }

    public function hasSkillEmployees(Skill $skill): bool
    {
        return $skill->employees()->count() > 0;
    }


    public function getSkillIndexData(): array
    {
        return [
            'skills' => $this->getAllSkillsWithEmployeeCount(),
        ];
    }

    public function getSkillShowData(Skill $skill): array
    {
        return [
            'skill' => $this->getSkillWithEmployees($skill),
        ];
    }

    public function getSkillEditData(Skill $skill): array
    {
        return [
            'skill' => $skill,
        ];
    }

    public function canDeleteSkill(Skill $skill): bool
    {
        return !$this->hasSkillEmployees($skill);
    }

    public function getSkillCreateData(): array
    {
        return [];
    }
}