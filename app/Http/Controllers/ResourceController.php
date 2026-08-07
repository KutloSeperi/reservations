<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Resource;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
  public function index(): Response
  {
    $resources = Resource::orderBy('name')->get()->map(function (Resource $resource) {
      $currentlyActiveReservationsCount = Reservation::where('resource_id', $resource->id)
      ->where('status', 'active')
      ->count();

      $resource->available_capacity = max(0, $resource->capacity - $currentlyActiveReservationsCount);

      return $resource;
    });

    // Pass the resource list and upcoming reservation summary to Inertia.
    return Inertia::render('Resources/Index', [
      'resources' => $resources,
      'upcomingReservations' => Reservation::with('resource')
      ->where('status', 'active')
      ->where('end_at', '>=', now())
      ->orderBy('start_at')
      ->limit(20)
      ->get(),
    ]);
  }
}