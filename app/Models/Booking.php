<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property int $user_id
 * @property int $class_id
 * @property string $status
 * @property string $booking_type
 * @property float|null $amount_paid
 * @property string|null $stripe_payment_intent_id
 * @property bool $is_refundable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\CrossfitClass $class
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStripePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereIsRefundable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking confirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking waitingList()
 * @method static \Database\Factories\BookingFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'class_id',
        'status',
        'booking_type',
        'amount_paid',
        'stripe_payment_intent_id',
        'is_refundable',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'is_refundable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class for this booking.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(CrossfitClass::class, 'class_id');
    }

    /**
     * Scope a query to only include confirmed bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include waiting list bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWaitingList($query)
    {
        return $query->where('status', 'waiting_list');
    }

    /**
     * Check if booking is refundable based on class start time.
     */
    public function isRefundableNow(): bool
    {
        if (!$this->is_refundable) {
            return false;
        }

        // No refund if within 1 hour of class start for drop-in bookings
        if (in_array($this->booking_type, ['drop_in', 'day_pass'])) {
            return $this->class->starts_at->diffInHours(now()) > 1;
        }

        return true;
    }
}