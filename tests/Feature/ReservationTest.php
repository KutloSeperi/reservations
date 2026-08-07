<?php

use App\Models\Resource;
use App\Models\Reservation;

it('creates a valid reservation', function () {
    $resource = Resource::create([
        'name' => 'Conference Room A',
        'type' => 'Meeting Room',
        'status' => 'available',
        'capacity' => 1,
    ]);

    $response = $this->post('/reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'start_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'end_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
        'notes' => 'Team sync',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseHas('reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'status' => 'active',
    ]);
});

it('rejects an invalid date range', function () {
    $resource = Resource::create([
        'name' => 'Storage Unit 3',
        'type' => 'Storage',
        'status' => 'available',
        'capacity' => 1,
    ]);

    $response = $this->post('/reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'Jon Doe',
        'customer_email' => 'jon@example.com',
        'start_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
        'end_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'notes' => 'Invalid timeframe',
    ]);

    $response->assertSessionHasErrors(['end_at']);
    $this->assertDatabaseCount('reservations', 0);
});

it('rejects overlapping active reservations', function () {
    $resource = Resource::create([
        'name' => 'Portable Projector',
        'type' => 'Equipment',
        'status' => 'available',
        'capacity' => 1,
    ]);

    Reservation::create([
        'resource_id' => $resource->id,
        'customer_name' => 'Existing User',
        'customer_email' => 'existing@example.com',
        'start_at' => now()->addHours(1),
        'end_at' => now()->addHours(2),
        'status' => 'active',
    ]);

    $response = $this->post('/reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'New User',
        'customer_email' => 'new@example.com',
        'start_at' => now()->addHours(1)->addMinutes(15)->format('Y-m-d H:i:s'),
        'end_at' => now()->addHours(2)->addMinutes(15)->format('Y-m-d H:i:s'),
        'notes' => 'Overlapping request',
    ]);

    $response->assertSessionHasErrors(['start_at']);
    $this->assertDatabaseCount('reservations', 1);
});

it('allows booking after a conflicting reservation is cancelled', function () {
    $resource = Resource::create([
        'name' => 'Boardroom',
        'type' => 'Meeting Room',
        'status' => 'available',
        'capacity' => 1,
    ]);

    $reservation = Reservation::create([
        'resource_id' => $resource->id,
        'customer_name' => 'First User',
        'customer_email' => 'first@example.com',
        'start_at' => now()->addHours(1),
        'end_at' => now()->addHours(2),
        'status' => 'active',
    ]);

    $this->delete("/reservations/{$reservation->id}");

    $response = $this->post('/reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'Second User',
        'customer_email' => 'second@example.com',
        'start_at' => now()->addHours(1)->addMinutes(15)->format('Y-m-d H:i:s'),
        'end_at' => now()->addHours(2)->addMinutes(15)->format('Y-m-d H:i:s'),
        'notes' => 'Booking after cancellation',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseHas('reservations', [
        'resource_id' => $resource->id,
        'customer_name' => 'Second User',
        'status' => 'active',
    ]);
});
