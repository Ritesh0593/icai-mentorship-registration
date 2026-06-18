<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Registration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define some cities
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Pune', 'Chennai', 'Kolkata'];

        $categories = [
            'MSME Entrepreneur',
            'Startup Founder',
            'Chartered Accountant (CA)',
            'Student / Aspirant',
            'Business Executive',
            'Other'
        ];

        $areas = [
            'Business Growth & Scaling',
            'Fund Raising & Venture Capital',
            'GST, Taxation & Auditing',
            'Technology, Digital Transformation & AI',
            'Legal, IPR & Patent Filing',
            'Marketing, Branding & Sales',
            'Other'
        ];

        foreach ($cities as $cityName) {
            // Create City
            $city = City::create([
                'name' => $cityName,
            ]);

            // Create between 5 to 25 random registrations for each city
            $count = rand(5, 25);
            for ($i = 0; $i < $count; $i++) {
                Registration::create([
                    'city_id' => $city->id,
                    'name' => $this->getRandomName(),
                    'email' => strtolower(Str::random(8)) . '@example.com',
                    'phone' => '+91 ' . rand(70000, 99999) . ' ' . rand(10000, 99999),
                    'participant_category' => $categories[array_rand($categories)],
                    'mentorship_area' => $areas[array_rand($areas)],
                    'otp_verified' => true
                ]);
            }
        }
    }

    /**
     * Helper to generate realistic Indian names.
     */
    private function getRandomName(): string
    {
        $firstNames = ['Aarav', 'Vihaan', 'Aditya', 'Vivaan', 'Sai', 'Arjun', 'Kian', 'Krishna', 'Ishaan', 'Shaurya', 'Ananya', 'Diya', 'Priya', 'Saanvi', 'Kiara', 'Aanya', 'Riya', 'Ira', 'Avani', 'Myra'];
        $lastNames = ['Sharma', 'Verma', 'Gupta', 'Patel', 'Reddy', 'Rao', 'Kumar', 'Singh', 'Joshi', 'Mehra', 'Nair', 'Pillai', 'Iyer', 'Sen', 'Das', 'Roy', 'Choudhury', 'Mishra', 'Trivedi', 'Pandey'];
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }
}
