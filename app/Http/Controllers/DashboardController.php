<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrossfitClass;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get user's upcoming bookings
        $upcomingBookings = Booking::with(['class.instructor'])
            ->where('user_id', $user->id)
            ->whereHas('class', function ($query) {
                $query->where('starts_at', '>', now())
                      ->where('is_cancelled', false);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'booking_type' => $booking->booking_type,
                    'amount_paid' => $booking->amount_paid,
                    'class' => [
                        'id' => $booking->class->id,
                        'name' => $booking->class->name,
                        'starts_at' => $booking->class->starts_at,
                        'duration_minutes' => $booking->class->duration_minutes,
                        'instructor_name' => $booking->class->instructor->name,
                    ],
                    'is_refundable' => $booking->isRefundableNow(),
                ];
            });

        // Get available classes for booking
        $availableClasses = CrossfitClass::with(['instructor', 'confirmedBookings'])
            ->where('tenant_id', $user->tenant_id)
            ->upcoming()
            ->whereDoesntHave('bookings', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('starts_at')
            ->take(8)
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
                    'is_full' => $class->isFull(),
                ];
            });

        return Inertia::render('dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'membership_type' => $user->membership_type,
                'membership_expires_at' => $user->membership_expires_at,
                'has_active_membership' => $user->hasActiveMembership(),
            ],
            'upcoming_bookings' => $upcomingBookings,
            'available_classes' => $availableClasses,
        ]);
    }
}