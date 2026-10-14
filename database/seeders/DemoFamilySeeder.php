<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FamilyRelationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoFamilySeeder extends Seeder
{
    public function run(): void
    {
        // Create demo parent
        $parent = User::updateOrCreate(
            ['email' => 'parent@example.com'],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'password' => Hash::make('password'),
                'type' => 'parent',
                'email_verified_at' => now(),
                'parent_code' => strtoupper(substr(md5(uniqid()), 0, 6)),
                'onboarding_completed' => true,
                'is_active' => true,
            ]
        );
        $parent->assignRole('parent');

        // Create demo children
        $children = [
            [
                'first_name' => 'Emma',
                'last_name' => 'Johnson',
                'username' => 'emma_j',
                'email' => 'emma@example.com',
                'password' => Hash::make('emma123'),
                'type' => 'child',
                'birth_date' => now()->subYears(8),
                'date_of_birth' => now()->subYears(8),
                'parent_id' => $parent->id,
                'email_verified_at' => now(),
                'onboarding_completed' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Liam',
                'last_name' => 'Johnson',
                'username' => 'liam_j',
                'email' => 'liam@example.com',
                'password' => Hash::make('liam123'),
                'type' => 'child',
                'birth_date' => now()->subYears(6),
                'date_of_birth' => now()->subYears(6),
                'parent_id' => $parent->id,
                'email_verified_at' => now(),
                'onboarding_completed' => true,
                'is_active' => true,
            ],
        ];

        foreach ($children as $childData) {
            $child = User::updateOrCreate(
                ['username' => $childData['username']],
                $childData
            );
            $child->assignRole('child');

            // Link child to parent (only parent_id + child_id columns)
            FamilyRelationship::firstOrCreate([
                'parent_id' => $parent->id,
                'child_id' => $child->id,
            ]);
        }

        $this->command->info('✅ Demo family seeded: parent@example.com / password');
        $this->command->info('   Children: emma_j / emma123, liam_j / liam123');
    }
}