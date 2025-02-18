<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Bayu Setiawan',
            'nik' => '3571013101930001',
            'email' => 'bayukkominfo@gmail.com',
            'password' => Hash::make('`'),
            'role_id' => '1',
            'id_instansi' => '1'
        ]);
    }
}
