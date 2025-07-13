<?php

namespace Database\Seeders;

use Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Modules\Client\Database\Seeders\ClientDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(ClientDatabaseSeeder::class);
// Call module seeders
        $this->call([
            ClientDatabaseSeeder::class,
            UserDatabaseSeeder::class,
        ]);

        // Optionally seed users directly
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
