<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\CrossfitClass
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $instructor_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $starts_at
 * @property int $duration_minutes
 * @property int $max_participants
 * @property bool $teen_approved
 * @property float|null $drop_in_price
 * @property bool $is_cancelled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @property-read \App\Models\User $instructor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $confirmedBookings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $waitingListBookings
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereTeenApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereDropInPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereIsCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder|CrossfitClass available()
 * @method static \Database\Factories\CrossfitClassFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class CrossfitClass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'instructor_id',
        'name',
        'description',
        'starts_at',
        'duration_minutes',
        'max_participants',
        'teen_approved',
        'drop_in_price',
        'is_cancelled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'teen_approved' => 'boolean',
        'drop_in_price' => 'decimal:2',
        'is_cancelled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the class.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the instructor for the class.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get all bookings for this class.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id');
    }

    /**
     * Get confirmed bookings for this class.
     */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id')->where('status', 'confirmed');
    }

    /**
     * Get waiting list bookings for this class.
     */
    public function waitingListBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id')->where('status', 'waiting_list');
    }

    /**
     * Scope a query to only include upcoming classes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->where('is_cancelled', false);
    }

    /**
     * Scope a query to only include available classes (not cancelled).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_cancelled', false);
    }

    /**
     * Check if class is full.
     */
    public function isFull(): bool
    {
        return $this->confirmedBookings()->count() >= $this->max_participants;
    }

    /**
     * Get available spots in the class.
     */
    public function availableSpots(): int
    {
        return max(0, $this->max_participants - $this->confirmedBookings()->count());
    }
}