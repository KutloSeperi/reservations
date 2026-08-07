<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
      ]);

      Resource::create([
        'name' => 'Conference Room A',
        'type' => 'Meeting Room',
        'status' => 'available',
        'capacity' => 1,
      ]);

      Resource::create([
        'name' => 'Storage Unit 3',
        'type' => 'Storage',
        'status' => 'available',
        'capacity' => 1,
      ]);

      Resource::create([
        'name' => 'Portable Projector',
        'type' => 'Equipment',
        'status' => 'available',
        'capacity' => 2,
      ]);

      Resource::create([
        'name' => 'Boardroom',
        'type' => 'Meeting Room',
        'status' => 'maintenance',
        'capacity' => 1,
      ]);
    }
  }