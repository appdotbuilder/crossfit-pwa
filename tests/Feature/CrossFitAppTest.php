<?php

use App\Models\Booking;
use App\Models\CrossfitClass;
use App\Models\Tenant;
use App\Models\User;

it('displays tenant info and classes on home page', function () {
    $tenant = Tenant::factory()->create([
        'name' => 'Test CrossFit Gym',
    ]);

    $instructor = User::factory()->instructor()->create(['tenant_id' => $tenant->id]);
    
    $class = CrossfitClass::factory()->upcoming()->create([
        'tenant_id' => $tenant->id,
        'instructor_id' => $instructor->id,
        'name' => 'Morning WOD',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('welcome')
            ->has('tenant')
            ->has('upcoming_classes', 1)
            ->where('upcoming_classes.0.name', 'Morning WOD')
    );
});

it('allows authenticated user to view dashboard', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->member()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('dashboard')
            ->has('user')
            ->has('upcoming_bookings')
            ->has('available_classes')
            ->where('user.name', $user->name)
    );
});

it('allows member to book a class', function () {
    $tenant = Tenant::factory()->create();
    $member = User::factory()->member()->create(['tenant_id' => $tenant->id]);
    $instructor = User::factory()->instructor()->create(['tenant_id' => $tenant->id]);
    
    $class = CrossfitClass::factory()->upcoming()->create([
        'tenant_id' => $tenant->id,
        'instructor_id' => $instructor->id,
        'max_participants' => 10,
    ]);

    $response = $this->actingAs($member)->post('/bookings', [
        'class_id' => $class->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Class booked successfully!');

    $this->assertDatabaseHas('bookings', [
        'user_id' => $member->id,
        'class_id' => $class->id,
        'status' => 'confirmed',
    ]);
});

it('adds member to waiting list when class is full', function () {
    $tenant = Tenant::factory()->create();
    $member = User::factory()->member()->create(['tenant_id' => $tenant->id]);
    $instructor = User::factory()->instructor()->create(['tenant_id' => $tenant->id]);
    
    $class = CrossfitClass::factory()->upcoming()->create([
        'tenant_id' => $tenant->id,
        'instructor_id' => $instructor->id,
        'max_participants' => 1,
    ]);

    // Fill the class
    $existingMember = User::factory()->member()->create(['tenant_id' => $tenant->id]);
    Booking::factory()->confirmed()->create([
        'user_id' => $existingMember->id,
        'class_id' => $class->id,
    ]);

    $response = $this->actingAs($member)->post('/bookings', [
        'class_id' => $class->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Added to waiting list. You\'ll be notified if a spot opens up.');

    $this->assertDatabaseHas('bookings', [
        'user_id' => $member->id,
        'class_id' => $class->id,
        'status' => 'waiting_list',
    ]);
});

it('allows member to cancel booking', function () {
    $tenant = Tenant::factory()->create();
    $member = User::factory()->member()->create(['tenant_id' => $tenant->id]);
    $instructor = User::factory()->instructor()->create(['tenant_id' => $tenant->id]);
    
    $class = CrossfitClass::factory()->create([
        'tenant_id' => $tenant->id,
        'instructor_id' => $instructor->id,
        'starts_at' => now()->addHours(3), // 3 hours from now, should be refundable
    ]);

    $booking = Booking::factory()->confirmed()->create([
        'user_id' => $member->id,
        'class_id' => $class->id,
        'booking_type' => 'membership', // membership type should be refundable
        'is_refundable' => true,
    ]);

    $response = $this->actingAs($member)->delete("/bookings/{$booking->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Booking cancelled successfully.');

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled',
    ]);
});

it('redirects guest to login when accessing dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

it('redirects guest to login when booking classes', function () {
    $tenant = Tenant::factory()->create();
    $instructor = User::factory()->instructor()->create(['tenant_id' => $tenant->id]);
    $class = CrossfitClass::factory()->upcoming()->create([
        'tenant_id' => $tenant->id,
        'instructor_id' => $instructor->id,
    ]);

    $response = $this->post('/bookings', [
        'class_id' => $class->id,
    ]);

    $response->assertRedirect('/login');
});