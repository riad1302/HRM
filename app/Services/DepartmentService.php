<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DepartmentService
{
    public function getAllDepartmentsWithEmployeeCount(): Collection
    {
        return Department::withCount('employees')->get();
    }

    public function getDepartmentWithEmployees(Department $department): Department
    {
        return $department->load('employees');
    }

    public function createDepartment(array $validatedData): Department
    {
        return Department::create($validatedData);
    }

    public function updateDepartment(Department $department, array $validatedData): Department
    {
        $department->update($validatedData);
        return $department;
    }

    public function deleteDepartment(Department $department): bool
    {
        if ($this->hasDepartmentEmployees($department)) {
            return false;
        }

        return $department->delete();
    }

    public function hasDepartmentEmployees(Department $department): bool
    {
        return $department->employees()->count() > 0;
    }


    public function getDepartmentIndexData(): array
    {
        return [
            'departments' => $this->getAllDepartmentsWithEmployeeCount(),
        ];
    }

    public function getDepartmentShowData(Department $department): array
    {
        return [
            'department' => $this->getDepartmentWithEmployees($department),
        ];
    }

    public function getDepartmentEditData(Department $department): array
    {
        return [
            'department' => $department,
        ];
    }

    public function canDeleteDepartment(Department $department): bool
    {
        return !$this->hasDepartmentEmployees($department);
    }

    public function getDepartmentCreateData(): array
    {
        return [];
    }
}