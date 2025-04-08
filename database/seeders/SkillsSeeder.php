<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
  public function run(): void
  {
    $skills = [
      // Programming Languages
      'PHP',
      'JavaScript',
      'Python',
      'Java',
      'C#',
      'Ruby',
      'Go',
      'Swift',

      // Frontend
      'HTML',
      'CSS',
      'React',
      'Vue.js',
      'Angular',
      'TypeScript',
      'Tailwind CSS',

      // Backend
      'Laravel',
      'Node.js',
      'Django',
      'Spring Boot',
      'ASP.NET',
      'Express.js',

      // Databases
      'MySQL',
      'PostgreSQL',
      'MongoDB',
      'Redis',
      'SQLite',

      // DevOps
      'Docker',
      'Kubernetes',
      'AWS',
      'Git',
      'CI/CD',
      'Linux',

      // Soft Skills
      'Project Management',
      'Team Leadership',
      'Communication',
      'Problem Solving',

      // Testing
      'Unit Testing',
      'PHPUnit',
      'Jest',
      'Cypress',

      // Mobile
      'iOS Development',
      'Android Development',
      'React Native',
      'Flutter'
    ];

    foreach ($skills as $skill) {
      Skill::create(['name' => $skill]);
    }
  }
}
