<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'department' => 'Information Technology',
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'MySQL', 'Git']
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@company.com',
                'department' => 'Marketing',
                'skills' => ['Digital Marketing', 'SEO', 'Content Writing', 'Social Media']
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@company.com',
                'department' => 'Sales',
                'skills' => ['Sales', 'Communication', 'Customer Service', 'Problem Solving']
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.wilson@company.com',
                'department' => 'Human Resources',
                'skills' => ['Team Leadership', 'Communication', 'Project Management']
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@company.com',
                'department' => 'Information Technology',
                'skills' => ['Python', 'Docker', 'AWS', 'PostgreSQL', 'Git']
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@company.com',
                'department' => 'Finance',
                'skills' => ['Accounting', 'Financial Analysis', 'Data Analysis']
            ],
            [
                'first_name' => 'Alex',
                'last_name' => 'Garcia',
                'email' => 'alex.garcia@company.com',
                'department' => 'Information Technology',
                'skills' => ['React', 'Vue.js', 'Node.js', 'JavaScript', 'MongoDB']
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Martinez',
                'email' => 'lisa.martinez@company.com',
                'department' => 'Marketing',
                'skills' => ['UI/UX Design', 'Graphic Design', 'Digital Marketing']
            ],
        ];

        foreach ($employees as $employeeData) {
            $department = Department::where('name', $employeeData['department'])->first();
            
            if ($department) {
                $employee = Employee::create([
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'email' => $employeeData['email'],
                    'department_id' => $department->id,
                ]);

                if (!empty($employeeData['skills'])) {
                    $skills = Skill::whereIn('name', $employeeData['skills'])->pluck('id');
                    $employee->skills()->attach($skills);
                }
            }
        }
    }
}
