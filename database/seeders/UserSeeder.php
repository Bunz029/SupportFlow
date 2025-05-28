<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'job_title' => 'Administrator',
            'company' => 'SupportFlow',
        ]);

        // Create agent users
        $agents = [
            [
                'name' => 'John Agent',
                'email' => 'john@supportflow.com',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'job_title' => 'Support Specialist',
                'department' => 'Customer Support',
                'company' => 'SupportFlow',
            ],
            [
                'name' => 'Jane Agent',
                'email' => 'jane@supportflow.com',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'job_title' => 'Technical Support',
                'department' => 'Customer Support',
                'company' => 'SupportFlow',
            ],
            [
                'name' => 'Mike Agent',
                'email' => 'mike@supportflow.com',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'job_title' => 'Senior Support Specialist',
                'department' => 'Customer Support',
                'company' => 'SupportFlow',
            ],
        ];

        foreach ($agents as $agentData) {
            $agent = User::create($agentData);
        }

        // Create client users
        $clients = [
            [
                'name' => 'Client One',
                'email' => 'client1@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
                'job_title' => 'CEO',
                'company' => 'Company One',
            ],
            [
                'name' => 'Client Two',
                'email' => 'client2@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
                'job_title' => 'Manager',
                'company' => 'Company Two',
            ],
            [
                'name' => 'Client Three',
                'email' => 'client3@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
                'job_title' => 'Developer',
                'company' => 'Company Three',
            ],
            [
                'name' => 'Client Four',
                'email' => 'client4@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
                'job_title' => 'Designer',
                'company' => 'Company Four',
            ],
            [
                'name' => 'Client Five',
                'email' => 'client5@example.com',
                'password' => Hash::make('password'),
                'role' => 'client',
                'job_title' => 'Marketing',
                'company' => 'Company Five',
            ],
        ];

        foreach ($clients as $clientData) {
            User::create($clientData);
        }
    }
} 