<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
  public function store(StoreReservationRequest $request)
  {
  
    Reservation::create($request->validated());

    return redirect()->route('resources.index')
    ->with('success', 'Reservation created.');
  }

  public function show(Reservation $reservation): Response
  {
    // Load the associated resource so the frontend can display reservation details.
    return Inertia::render('Reservations/Show', [
      'reservation' => $reservation->load('resource'),
    ]);
  }

  public function destroy(Reservation $reservation)
  {
    // Mark the reservation cancelled instead of deleting it.
    $reservation->update(['status' => 'cancelled']);

    return redirect()->back()->with('success', 'Reservation cancelled.');
  }
}