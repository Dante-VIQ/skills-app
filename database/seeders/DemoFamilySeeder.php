<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\FamilyRelationship;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/DemoFamilySeeder.php
    public function run()
    {
        // Create a demo parent
        $parent = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'parent@example.com',
            'password' => Hash::make('password'),
            'type' => 'parent',
        ]);

        // Create demo children
        $children = [
            [
                'name' => 'Emma Johnson',
                'username' => 'emma_j',
                'password' => Hash::make('emma123'),
                'type' => 'child',
                'birth_date' => now()->subYears(8),
            ],
            [
                'name' => 'Liam Johnson',
                'username' => 'liam_j',
                'password' => Hash::make('liam123'),
                'type' => 'child',
                'birth_date' => now()->subYears(6),
            ],
        ];

        foreach ($children as $childData) {
            $child = User::create($childData);

            // Link child to parent
            FamilyRelationship::create([
                'parent_id' => $parent->id,
                'child_id' => $child->id,
            ]);
        }
    }
}
