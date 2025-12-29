<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DepartmentService;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index()
    {
        $data = $this->departmentService->getDepartmentIndexData();
        return view('departments.index', $data);
    }

    public function create()
    {
        $data = $this->departmentService->getDepartmentCreateData();
        return view('departments.create', $data);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $this->departmentService->createDepartment($request->validated());

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $data = $this->departmentService->getDepartmentShowData($department);
        return view('departments.show', $data);
    }

    public function edit(Department $department)
    {
        $data = $this->departmentService->getDepartmentEditData($department);
        return view('departments.edit', $data);
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $this->departmentService->updateDepartment($department, $request->validated());

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if (!$this->departmentService->canDeleteDepartment($department)) {
            return redirect()->route('departments.index')->with('error', 'Cannot delete department with existing employees.');
        }

        $this->departmentService->deleteDepartment($department);
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
