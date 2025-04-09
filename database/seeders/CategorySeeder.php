<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentCategories = [
            // Programming Languages

            'Frontend' => ['HTML', 'CSS', 'React', 'Vue.js', 'Angular', 'TypeScript', 'Tailwind CSS'],
            'Backend' => ['Laravel', 'Node.js', 'Django', 'Ruby on Rails', 'Express', 'Spring', 'ASP.NET'],
            'Full Stack' => ['Laravel', 'Node.js', 'Django', 'Ruby on Rails', 'Express', 'Spring', 'ASP.NET'],
            'DevOps' => ['Docker', 'Kubernetes', 'Jenkins', 'Git', 'CI/CD', 'AWS', 'Azure'],
            'Mobile' => ['React Native', 'Flutter', 'Swift', 'Kotlin', 'Java', 'Objective-C', 'Swift'],
            'Testing' => ['JUnit', 'Selenium', 'Mocha', 'Jest', 'Cypress', 'Puppeteer', 'Playwright'],
            'Project Management' => ['Agile', 'Scrum', 'Kanban', 'Waterfall', 'Lean', 'DevOps', 'Agile'],
            'Team Leadership' => ['Team Management', 'Leadership', 'Mentorship', 'Project Management', 'Agile', 'Scrum', 'Kanban'],
        ];


        foreach ($parentCategories as $category => $childCategories) {
            $parentCategory = Category::firstOrCreate(['name' => $category], ['slug' => Str::slug($category), 'parent_id' => null]);
            foreach ($childCategories as $childCategory) {
                Category::firstOrCreate(['name' => $childCategory], ['slug' => Str::slug($childCategory), 'parent_id' => $parentCategory->id]);
            }
        }
    }
}
