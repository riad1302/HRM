<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            'PHP',
            'Laravel',
            'JavaScript',
            'React',
            'Vue.js',
            'Node.js',
            'Python',
            'Java',
            'C#',
            'MySQL',
            'PostgreSQL',
            'MongoDB',
            'Docker',
            'AWS',
            'Git',
            'Project Management',
            'Team Leadership',
            'Communication',
            'Problem Solving',
            'Data Analysis',
            'Digital Marketing',
            'SEO',
            'Social Media',
            'Content Writing',
            'Graphic Design',
            'UI/UX Design',
            'Sales',
            'Customer Service',
            'Accounting',
            'Financial Analysis',
        ];

        foreach ($skills as $skill) {
            Skill::create(['name' => $skill]);
        }
    }
}
