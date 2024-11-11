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
        $winch_vendor_admin = User::create([
            'name' => 'winch vendor',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
        ]);

        $winch_vendor = Vendor::create([
            'name' => 'الامانه لاوناش الإنقاذ',
            'user_id' => $winch_vendor_admin->id,
            'type' => 'winch',
        ]);

        $winch_drivers = User::factory(5)->create();

        foreach ($winch_drivers as $user) {
            $driver = Worker::create([
                'user_id' => $user->id,
                'vendor_id' => $winch_vendor->id,
                'type' => 'winch',
                'lat' => fake()->latitude(),
                'lon' => fake()->longitude(),
                'car_plate_number' => fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter()  . fake()->randomDigit()  . fake()->randomDigit()  . fake()->randomDigit()
            ]);
        }

        $emergency_vendor_admin = User::create([
            'name' => 'emergency vendor',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
        ]);

        $emergency_vendor = Vendor::create([
            'name' => 'ورشة الامانه لخدمات ا لانقاذ',
            'user_id' => $emergency_vendor_admin->id,
            'type' => 'workshop',
        ]);

        $emergency_workers = User::factory(5)->create();

        foreach ($emergency_workers as $user) {
            Worker::create([
                'user_id' => $user->id,
                'vendor_id' => $emergency_vendor->id,
                'type' => 'emergency',
                'lat' => fake()->latitude(),
                'lon' => fake()->longitude(),
                'car_plate_number' => fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter()  . fake()->randomDigit()  . fake()->randomDigit()  . fake()->randomDigit()
            ]);
        }
    }
}
