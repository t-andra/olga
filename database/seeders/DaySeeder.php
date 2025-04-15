<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('olga_calendar_days')->where('date_at', '2025-04-20')->delete();
        DB::table('olga_calendar_days')->where('date_at', '2025-04-25')->delete();

        DB::table('olga_calendar_days')->insert([
            'name' => 'Пасха',
            'date_at' => '2025-04-20',
            'status' => 0,
        ]);

        $id = DB::table('olga_calendar_days')->insertGetId([
            'name' => 'День как день',
            'date_at' => '2025-04-25',
            'status' => 1,
        ]);

        DB::table('olga_calendar_appointments')->insert([
            [
                'olga_calendar_day_id' => $id,
                'status' => 1,
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'phone' => '01',
                'start' => '11:00:00',
                'finish' => '11:59:00',
                'comment' => 'Хочу набрать вес',
            ],
            [
                'olga_calendar_day_id' => $id,
                'status' => 1,
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'phone' => '02',
                'start' => '12:30:00',
                'finish' => '12:45:00',
                'comment' => 'Хочу подобрать закуски к водке',
            ],

            [
                'olga_calendar_day_id' => $id,
                'status' => 1,
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'phone' => '02',
                'start' => '14:30:00',
                'finish' => '17:20:00',
                'comment' => 'Хочу повысить давление',
            ],
        ]);

    }
}
