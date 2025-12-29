<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeService;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{

    public function __construct(private EmployeeService $employeeService)
    {
    }

    public function index(Request $request)
    {
        $departmentId = $request->get('department_id');

        if ($request->ajax()) {
            $employees = $this->employeeService->getEmployeesForAjax($departmentId);
            return response()->json([
                'html' => view('employees.partials.employee-list', compact('employees'))->render()
            ]);
        }

        $data = $this->employeeService->getEmployeeIndexData($departmentId);
        return view('employees.index', $data);
    }

    public function create()
    {
        $data = $this->employeeService->getEmployeeCreateData();
        return view('employees.create', $data);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->employeeService->createEmployee($request->validated());

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee = $this->employeeService->prepareEmployeeForShow($employee);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $data = $this->employeeService->getEmployeeEditData($employee);
        return view('employees.edit', $data);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->employeeService->updateEmployee($employee, $request->validated());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->employeeService->deleteEmployee($employee);
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $employeeId = $request->get('employee_id');

        $exists = $this->employeeService->checkEmailUniqueness($email, $employeeId);

        return response()->json(['exists' => $exists]);
    }
}
