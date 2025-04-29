<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $skills = Skill::all();
        $locations = ['London', 'Manchester', 'Birmingham', 'Glasgow', 'Edinburgh', 'Sheffield', 'Liverpool', 'Newcastle', 'Bristol', 'Leeds'];
        $titles = ['Software Engineer', 'Web Developer', 'Full Stack Developer', 'Frontend Developer', 'Backend Developer', 'DevOps Engineer', 'QA Engineer', 'Project Manager', 'Product Manager', 'UX/UI Designer'];
        $salaryRanges = ['10,000 - 20,000', '20,000 - 30,000', '30,000 - 40,000', '40,000 - 50,000', '50,000 - 60,000', '60,000 - 70,000', '70,000 - 80,000', '80,000 - 90,000', '90,000 - 100,000'];
        $description = ['Fantastic opportunity to join a leading company in the tech industry.', 'Looking for a new challenge? We have the perfect role for you.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.', 'We are looking for a new team member to join our team.'];
        $companies = ['WDC ltd', 'Prg ltd', 'Company 3', 'Company 4', 'Company 5', 'Company 6', 'Company 7', 'Company 8', 'Company 9', 'Company 10'];
        for ($i = 0; $i < 100; $i++) {
            $title = $titles[\rand(0, count($titles) - 1)];
            $listing = Listing::firstOrCreate([
                'location' => $locations[\rand(0, count($locations) - 1)],
                'salary' => $salaryRanges[\rand(0, count($salaryRanges) - 1)],
                'description' => $description[\rand(0, count($description) - 1)],
                'user_id' => 2,
                'company' => $companies[\rand(0, count($companies) - 1)],
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $i . '-' . \rand(1, 1000),
            ]);
            $listing->skills()->attach($skills->random());
            $listing->categories()->attach($categories->random());
        }
    }
}
