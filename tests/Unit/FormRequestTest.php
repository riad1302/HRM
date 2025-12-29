<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Skill;

class FormRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_employee_request_validates_required_fields(): void
    {
        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('department_id', $validator->errors()->toArray());
    }

    public function test_store_employee_request_validates_email_format(): void
    {
        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'department_id' => 1,
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_store_employee_request_validates_unique_email(): void
    {
        $department = Department::factory()->create();
        Employee::factory()->create(['email' => 'test@example.com']);

        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'department_id' => $department->id,
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_update_employee_request_validates_unique_email_excluding_current(): void
    {
        $department = Department::factory()->create();
        $employee1 = Employee::factory()->create(['email' => 'employee1@example.com']);
        $employee2 = Employee::factory()->create(['email' => 'employee2@example.com']);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee1->id,
            'department_id' => 'required|exists:departments,id',
        ];

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'employee2@example.com',
            'department_id' => $department->id,
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_update_employee_request_allows_same_email_for_current_employee(): void
    {
        $department = Department::factory()->create();
        $employee = Employee::factory()->create(['email' => 'test@example.com', 'department_id' => $department->id]);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
        ];

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'department_id' => $department->id,
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_store_department_request_validates_required_name(): void
    {
        $request = new StoreDepartmentRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_store_department_request_validates_unique_name(): void
    {
        Department::factory()->create(['name' => 'Engineering']);

        $request = new StoreDepartmentRequest();
        $rules = $request->rules();

        $data = ['name' => 'Engineering'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_update_department_request_validates_unique_name_excluding_current(): void
    {
        $department1 = Department::factory()->create(['name' => 'Engineering']);
        $department2 = Department::factory()->create(['name' => 'Marketing']);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'name' => 'required|string|max:255|unique:departments,name,' . $department1->id,
        ];

        $data = ['name' => 'Marketing'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_update_department_request_allows_same_name_for_current_department(): void
    {
        $department = Department::factory()->create(['name' => 'Engineering']);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ];

        $data = ['name' => 'Engineering'];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_store_skill_request_validates_required_name(): void
    {
        $request = new StoreSkillRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_store_skill_request_validates_unique_name(): void
    {
        Skill::factory()->create(['name' => 'PHP']);

        $request = new StoreSkillRequest();
        $rules = $request->rules();

        $data = ['name' => 'PHP'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_update_skill_request_validates_unique_name_excluding_current(): void
    {
        $skill1 = Skill::factory()->create(['name' => 'PHP']);
        $skill2 = Skill::factory()->create(['name' => 'Laravel']);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'name' => 'required|string|max:255|unique:skills,name,' . $skill1->id,
        ];

        $data = ['name' => 'Laravel'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_update_skill_request_allows_same_name_for_current_skill(): void
    {
        $skill = Skill::factory()->create(['name' => 'PHP']);

        // Test validation using the unique rule format for excluding current ID
        $rules = [
            'name' => 'required|string|max:255|unique:skills,name,' . $skill->id,
        ];

        $data = ['name' => 'PHP'];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_store_employee_request_validates_skills_array(): void
    {
        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'department_id' => 1,
            'skills' => 'invalid',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('skills', $validator->errors()->toArray());
    }

    public function test_store_employee_request_validates_skills_exist(): void
    {
        $department = Department::factory()->create();

        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'department_id' => $department->id,
            'skills' => [999], // Non-existing skill ID
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('skills.0', $validator->errors()->toArray());
    }

    public function test_store_employee_request_passes_with_valid_data(): void
    {
        $department = Department::factory()->create();
        $skill = Skill::factory()->create();

        $request = new StoreEmployeeRequest();
        $rules = $request->rules();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'department_id' => $department->id,
            'skills' => [$skill->id],
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }
}