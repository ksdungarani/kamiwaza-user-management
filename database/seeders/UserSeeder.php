<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Alice',
                'last_name' => 'Anderson',
                'email' => 'alice@example.com',
                'password' => Hash::make('test@123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Baker',
                'email' => 'bob@example.com',
                'password' => Hash::make('test@123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Charlie',
                'last_name' => 'Clark',
                'email' => 'charlie@example.com',
                'password' => Hash::make('test@123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Diana',
                'last_name' => 'Davis',
                'email' => 'diana@example.com',
                'password' => Hash::make('test@123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Ethan',
                'last_name' => 'Evans',
                'email' => 'ethan@example.com',
                'password' => Hash::make('test@123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
