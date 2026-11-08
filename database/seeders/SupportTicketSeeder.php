<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Priority;
use App\Models\SlaRule;
use App\Models\BusinessHour;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorityData = [
            ['name' => 'Low', 'level' => 1, 'minutes' => 2880],
            ['name' => 'Medium', 'level' => 2, 'minutes' => 1440],
            ['name' => 'High', 'level' => 3, 'minutes' => 480],
            ['name' => 'Critical', 'level' => 4, 'minutes' => 120],
        ];

        foreach ($priorityData as $item) {
            $priority = Priority::updateOrCreate(
                ['name' => $item['name']],
                [
                    'level' => $item['level'],
                    'color' => strtolower($item['name']),
                    'is_active' => true,
                ]
            );

            SlaRule::updateOrCreate(
                ['priority_id' => $priority->id],
                [
                    'resolution_minutes' => $item['minutes'],
                    'warning_percent' => 20,
                    'is_active' => true,
                ]
            );
        }

        $categories = [
            'Technical Issue',
            'Payment Issue',
            'Account Problem',
            'General Inquiry',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // Sunday = 0, Monday = 1, ... Saturday = 6.
        for ($day = 0; $day <= 6; $day++) {
            $working = $day >= 0 && $day <= 4;

            BusinessHour::updateOrCreate(
                ['weekday' => $day],
                [
                    'is_working_day' => $working,
                    'start_time' => $working ? '09:00:00' : null,
                    'end_time' => $working ? '18:00:00' : null,
                ]
            );
        }
    }
}
