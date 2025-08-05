<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\CrossfitClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrossFitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo tenant
        $tenant = Tenant::create([
            'name' => 'CrossFit Elite Gym',
            'domain' => 'elite.crossfitapp.com',
            'slug' => 'crossfit-elite',
            'logo_url' => null,
            'settings' => [
                'theme_color' => '#ef4444',
                'timezone' => 'America/New_York',
            ],
            'is_active' => true,
        ]);

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@crossfitapp.com',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Create tenant admin
        $tenantAdmin = User::create([
            'name' => 'Gym Manager',
            'email' => 'manager@elite.crossfitapp.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // Create instructors
        $instructor1 = User::create([
            'name' => 'Coach Sarah',
            'email' => 'sarah@elite.crossfitapp.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'instructor',
            'phone' => '555-0101',
            'is_active' => true,
        ]);

        $instructor2 = User::create([
            'name' => 'Coach Mike',
            'email' => 'mike@elite.crossfitapp.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'instructor',
            'phone' => '555-0102',
            'is_active' => true,
        ]);

        $instructor3 = User::create([
            'name' => 'Coach Alex',
            'email' => 'alex@elite.crossfitapp.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'instructor',
            'phone' => '555-0103',
            'is_active' => true,
        ]);

        // Create sample members
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'member',
            'phone' => '555-0201',
            'membership_type' => 'standard',
            'membership_expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'member',
            'phone' => '555-0202',
            'membership_type' => 'student',
            'membership_expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        // Create sample classes for the next week
        $classTypes = [
            ['name' => 'Morning WOD', 'description' => 'High-intensity workout to start your day', 'teen_approved' => false],
            ['name' => 'Teen CrossFit', 'description' => 'CrossFit fundamentals for teenagers', 'teen_approved' => true],
            ['name' => 'Open Gym', 'description' => 'Self-directed training with coach supervision', 'teen_approved' => true],
            ['name' => 'Olympic Lifting', 'description' => 'Focus on snatch and clean & jerk technique', 'teen_approved' => false],
            ['name' => 'MetCon Mayhem', 'description' => 'Metabolic conditioning workout', 'teen_approved' => false],
            ['name' => 'Beginner Friendly', 'description' => 'Perfect for those new to CrossFit', 'teen_approved' => true],
        ];

        $instructors = [$instructor1, $instructor2, $instructor3];
        
        for ($day = 0; $day < 7; $day++) {
            $date = now()->addDays($day);
            
            // Morning classes (6:00 AM, 7:00 AM, 8:00 AM)
            for ($hour = 6; $hour <= 8; $hour++) {
                $classType = $classTypes[random_int(0, count($classTypes) - 1)];
                $instructor = $instructors[random_int(0, count($instructors) - 1)];
                
                CrossfitClass::create([
                    'tenant_id' => $tenant->id,
                    'instructor_id' => $instructor->id,
                    'name' => $classType['name'],
                    'description' => $classType['description'],
                    'starts_at' => $date->copy()->setTime($hour, 0),
                    'duration_minutes' => 60,
                    'max_participants' => random_int(12, 20),
                    'teen_approved' => $classType['teen_approved'],
                    'drop_in_price' => random_int(20, 35),
                    'is_cancelled' => false,
                ]);
            }
            
            // Evening classes (5:00 PM, 6:00 PM, 7:00 PM)
            for ($hour = 17; $hour <= 19; $hour++) {
                $classType = $classTypes[random_int(0, count($classTypes) - 1)];
                $instructor = $instructors[random_int(0, count($instructors) - 1)];
                
                CrossfitClass::create([
                    'tenant_id' => $tenant->id,
                    'instructor_id' => $instructor->id,
                    'name' => $classType['name'],
                    'description' => $classType['description'],
                    'starts_at' => $date->copy()->setTime($hour, 0),
                    'duration_minutes' => 60,
                    'max_participants' => random_int(12, 20),
                    'teen_approved' => $classType['teen_approved'],
                    'drop_in_price' => random_int(20, 35),
                    'is_cancelled' => false,
                ]);
            }
        }
    }
}