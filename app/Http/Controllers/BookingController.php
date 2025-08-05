<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CrossfitClass;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $user = $request->user();
        $class = CrossfitClass::findOrFail($request->class_id);

        // Check if user already has a booking for this class
        $existingBooking = Booking::where('user_id', $user->id)
            ->where('class_id', $class->id)
            ->first();

        if ($existingBooking) {
            return redirect()->back()->with('error', 'You already have a booking for this class.');
        }

        // Check if class is in the past
        if ($class->starts_at->isPast()) {
            return redirect()->back()->with('error', 'Cannot book a class that has already started.');
        }

        // Check if class is cancelled
        if ($class->is_cancelled) {
            return redirect()->back()->with('error', 'This class has been cancelled.');
        }

        // Determine booking status based on availability
        $confirmedBookingsCount = $class->confirmedBookings()->count();
        $status = $confirmedBookingsCount >= $class->max_participants ? 'waiting_list' : 'confirmed';

        // For demo purposes, all bookings are free (membership-based)
        $booking = Booking::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'status' => $status,
            'booking_type' => 'membership',
            'amount_paid' => null,
            'stripe_payment_intent_id' => null,
            'is_refundable' => true,
        ]);

        $message = $status === 'confirmed' 
            ? 'Class booked successfully!' 
            : 'Added to waiting list. You\'ll be notified if a spot opens up.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Cancel a booking.
     */
    public function destroy(Booking $booking)
    {
        $user = request()->user();

        // Load the class relationship
        $booking->load('class');

        // Check if user owns this booking
        if ($booking->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You can only cancel your own bookings.');
        }

        // Check if booking is refundable
        if (!$booking->isRefundableNow()) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled at this time.');
        }

        $class = $booking->class;
        $wasConfirmed = $booking->status === 'confirmed';
        
        // Cancel the booking
        $booking->update(['status' => 'cancelled']);

        // If this was a confirmed booking, promote someone from waiting list
        if ($wasConfirmed) {
            $nextWaitingBooking = Booking::where('class_id', $class->id)
                ->where('status', 'waiting_list')
                ->orderBy('created_at')
                ->first();

            if ($nextWaitingBooking) {
                $nextWaitingBooking->update(['status' => 'confirmed']);
                // In a real app, you'd send a notification to the user here
            }
        }

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}