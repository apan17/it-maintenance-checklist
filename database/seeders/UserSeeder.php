<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'staff_no' => '00001',
            'email' => 'masteradmin@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('masteradmin')
            ->profile()->create([
            'username' => 'Masteradmin',
            'fullname' => 'Masteradmin',
            'contact_no' => '0123456789',
        ]);

        User::create([
            'staff_no' => '00002',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin')
            ->profile()->create([
            'username' => 'Arfan Tompol',
            'fullname' => 'Arfan',
            'contact_no' => '01111920398',
        ]);

        User::create([
            'staff_no' => '00003',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('staff')
            ->profile()->create([
            'username' => 'Staff',
            'fullname' => 'Staff',
            'contact_no' => '0198765432',
        ]);
    }
}
