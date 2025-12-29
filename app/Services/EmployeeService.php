<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class EmployeeService
{
    public function getEmployeesList(?int $departmentId = null): Collection
    {
        $query = Employee::with(['department', 'skills']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get();
    }

    public function getAllDepartments(): Collection
    {
        return Department::all();
    }

    public function getAllSkills(): Collection
    {
        return Skill::all();
    }

    public function getEmployeeWithRelations(Employee $employee): Employee
    {
        return $employee->load(['department', 'skills']);
    }

    public function createEmployee(array $validatedData): Employee
    {
        $employee = Employee::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'department_id' => $validatedData['department_id'],
        ]);

        if (!empty($validatedData['skills'])) {
            $this->syncEmployeeSkills($employee, $validatedData['skills']);
        }

        return $employee;
    }

    public function updateEmployee(Employee $employee, array $validatedData): Employee
    {
        $employee->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'department_id' => $validatedData['department_id'],
        ]);

        $skills = $validatedData['skills'] ?? [];
        $this->syncEmployeeSkills($employee, $skills);

        return $employee;
    }

    public function deleteEmployee(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function checkEmailUniqueness(string $email, ?int $excludeEmployeeId = null): bool
    {
        $query = Employee::where('email', $email);
        
        if ($excludeEmployeeId) {
            $query->where('id', '!=', $excludeEmployeeId);
        }
        
        return $query->exists();
    }


    public function getEmployeeCreateData(): array
    {
        return [
            'departments' => $this->getAllDepartments(),
            'skills' => $this->getAllSkills(),
        ];
    }

    public function getEmployeeEditData(Employee $employee): array
    {
        return [
            'employee' => $this->getEmployeeWithRelations($employee),
            'departments' => $this->getAllDepartments(),
            'skills' => $this->getAllSkills(),
        ];
    }

    protected function syncEmployeeSkills(Employee $employee, array $skillIds): void
    {
        $employee->skills()->sync($skillIds);
    }

    public function getEmployeeIndexData(?int $departmentId = null): array
    {
        return [
            'employees' => $this->getEmployeesList($departmentId),
            'departments' => $this->getAllDepartments(),
        ];
    }

    public function getEmployeesForAjax(?int $departmentId = null): Collection
    {
        return $this->getEmployeesList($departmentId);
    }

    public function prepareEmployeeForShow(Employee $employee): Employee
    {
        return $this->getEmployeeWithRelations($employee);
    }
}