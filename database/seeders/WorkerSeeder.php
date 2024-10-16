<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendor_admin = User::create([
            'name' => 'winch vendor',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
        ]);

        $vendor = Vendor::create([
            'name' => 'الامانه لاوناش الإنقاذ',
            'user_id' => $vendor_admin->id,
            'type' => 'winch',
        ]);

        $winch_drivers = User::factory(5)->create();

        foreach ($winch_drivers as $user) {
            $driver = Worker::create([
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
                'type' => 'winch',
                'lat' => fake()->latitude(),
                'lon' => fake()->longitude(),
                'car_plate_number' => fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter()  . fake()->randomDigit()  . fake()->randomDigit()  . fake()->randomDigit()
            ]);
        }
    }
}
