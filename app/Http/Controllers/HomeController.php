<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrossfitClass;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page with upcoming classes and tenant info.
     */
    public function index(Request $request)
    {
        // For demo purposes, we'll use the first tenant or create one
        $tenant = Tenant::active()->first();
        
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'CrossFit Demo Gym',
                'domain' => 'demo.crossfitapp.com',
                'slug' => 'demo-gym',
                'is_active' => true,
            ]);
        }

        // Get upcoming classes for the tenant
        $upcomingClasses = CrossfitClass::with(['instructor', 'confirmedBookings'])
            ->where('tenant_id', $tenant->id)
            ->upcoming()
            ->orderBy('starts_at')
            ->take(6)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'description' => $class->description,
                    'starts_at' => $class->starts_at,
                    'duration_minutes' => $class->duration_minutes,
                    'instructor_name' => $class->instructor->name,
                    'available_spots' => $class->availableSpots(),
                    'max_participants' => $class->max_participants,
                    'teen_approved' => $class->teen_approved,
                    'drop_in_price' => $class->drop_in_price,
                ];
            });

        return Inertia::render('welcome', [
            'tenant' => [
                'name' => $tenant->name,
                'logo_url' => $tenant->logo_url,
            ],
            'upcoming_classes' => $upcomingClasses,
        ]);
    }
}