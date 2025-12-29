<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
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
                'UI/UX Design'
            ]),
        ];
    }
}
