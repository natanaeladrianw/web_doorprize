<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => 'adminpass',
            ]
        );

        // Create input hadiah user (admin dengan akses terbatas - hanya input hadiah)
        User::updateOrCreate(
            ['email' => 'hadiah@gmail.com'],
            [
                'name' => 'Input Hadiah',
                'email' => 'hadiah@gmail.com',
                'role' => 'input_hadiah',
                'password' => 'hadiahpass',
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('');
        $this->command->info('Admin Full Access:');
        $this->command->info('Email: admin@gmail.com');
        $this->command->info('Password: adminpass');
        $this->command->info('');
        $this->command->info('Admin Input Hadiah (Limited Access):');
        $this->command->info('Email: hadiah@gmail.com');
        $this->command->info('Password: hadiahpass');
    }
}
