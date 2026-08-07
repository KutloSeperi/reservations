<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    // Basic form validation for the reservation request.
    return [
      'resource_id' => ['required', 'integer', 'exists:resources,id'],
      'customer_name' => ['required', 'string', 'max:255'],
      'customer_email' => ['required', 'email', 'max:255'],
      'start_at' => ['required', 'date', 'after_or_equal:now'],
      'end_at' => ['required', 'date', 'after:start_at'],
      'notes' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function messages(): array
  {
    return [
      'resource_id.required' => 'Please select a resource to reserve.',
      'resource_id.integer' => 'Selected resource is invalid.',
      'resource_id.exists' => 'Selected resource is not available.',
      'customer_name.required' => 'Please enter the customer name.',
      'customer_name.max' => 'Customer name must be 255 characters or fewer.',
      'customer_email.required' => 'Please enter the customer email.',
      'customer_email.email' => 'Please enter a valid email address.',
      'customer_email.max' => 'Email address must be 255 characters or fewer.',
      'start_at.required' => 'Please choose a start date and time.',
      'start_at.after_or_equal' => 'Start date and time must be now or in the future.',
      'end_at.required' => 'Please choose an end date and time.',
      'end_at.after' => 'End date and time must be after the selected start time.',
      'notes.max' => 'Notes must be 1000 characters or fewer.',
    ];
  }

    /**
     * Reject if the number of overlapping active reservations has reached
     * the resource's capacity (capacity 1 = no double-booking at all).
     */
    public function withValidator(Validator $validator): void
    {
      $validator->after(function (Validator $validator) {
        if ($validator->errors()->isNotEmpty()) {
          return;
        }

        $resource = Resource::find($this->resource_id);

        if (! $resource) {
          return;
        }

        $overlappingCount = Reservation::overlapping(
          $resource->id,
          $this->start_at,
          $this->end_at
        )->count();

        // Compare the number of overlapping active reservations with resource capacity.
        // A capacity of 1 means no simultaneous reservations are allowed.
        if ($overlappingCount >= $resource->capacity) {
          $validator->errors()->add(
            'start_at',
            $resource->capacity <= 1
            ? 'This resource is already reserved for part of the selected period.'
            : "This resource is fully booked for the selected period (capacity: {$resource->capacity})."
          );
        }
      });
    }
  }