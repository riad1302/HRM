<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Services\SkillService;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    protected SkillService $skillService;

    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
    }

    public function index()
    {
        $data = $this->skillService->getSkillIndexData();
        return view('skills.index', $data);
    }

    public function create()
    {
        $data = $this->skillService->getSkillCreateData();
        return view('skills.create', $data);
    }

    public function store(StoreSkillRequest $request)
    {
        $this->skillService->createSkill($request->validated());

        return redirect()->route('skills.index')->with('success', 'Skill created successfully.');
    }

    public function show(Skill $skill)
    {
        $data = $this->skillService->getSkillShowData($skill);
        return view('skills.show', $data);
    }

    public function edit(Skill $skill)
    {
        $data = $this->skillService->getSkillEditData($skill);
        return view('skills.edit', $data);
    }

    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $this->skillService->updateSkill($skill, $request->validated());

        return redirect()->route('skills.index')->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        if (!$this->skillService->canDeleteSkill($skill)) {
            return redirect()->route('skills.index')->with('error', 'Cannot delete skill assigned to employees.');
        }

        $this->skillService->deleteSkill($skill);
        return redirect()->route('skills.index')->with('success', 'Skill deleted successfully.');
    }
}
