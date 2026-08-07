# Resource Reservation and Availability Application

## Overview

A small app for managing bookable resources and their reservations. A resource can be a meeting room, storage unit, or piece of equipment. Users can view available resources, create reservations, see upcoming bookings, and cancel reservations when needed.

The main business rule is that a resource cannot have overlapping active reservations. That overlap check is enforced on the server, not just in the browser.

## Current status

- Laravel, Inertia, and Vue 3 are installed and working.
- Resource and reservation database tables and models are implemented.
- Server-side validation is in place, including:
  - required fields
  - valid email
  - `start_at` not in the past
  - `end_at` after `start_at`
  - overlap check for active reservations
  - same-day reservations are allowed if the times do not overlap
  - cancelled reservations are ignored
- `POST /reservations` creates reservations.
- All four required automated tests exist and pass:
  - valid reservation can be created
  - invalid date range is rejected
  - overlapping active reservation is rejected
  - resource can be rebooked after a conflicting reservation is cancelled
- The resources page shows each resource’s:
  - name
  - type
  - status
  - capacity
  - availability
- Upcoming reservations are shown in chronological order.
- The reservation form is available and server-side validation errors are surfaced in the UI.
- Reservation cancellation works.
- Seed data includes a set of named resources to demonstrate the app.

This was my first time using Inertia with Laravel and Vue together; I spent almost 30 minutes reading how Inertia works before implementing the app. That saved me from wiring up Axios or a separate API, but it also means this is a better fit for a SPA-style workflow.

## Stack

| Layer | Technology | Reason |
|---|---|---|
| Backend | Laravel | Handles routing, validation, persistence, and testing |
| Frontend | Vue 3 | Builds the user interface |
| Page wiring | Inertia.js | Lets Laravel and Vue work together without a separate API |
| Database | PostgreSQL | Stores resources and reservations |
| Styling | Tailwind CSS | Lightweight utility styling |
| Testing | Pest | Tests reservation business rules |

## Why Inertia

For this project and timebox, Inertia was a good fit because it lets Laravel render pages with Vue components without building a separate REST API. That saved time and kept the backend and frontend tightly integrated. I did not have to wire Axios or an external API into the app, and I avoided the old AJAX-style flow of `url`, `data`, `success`, and callback-based response handling. If this were a larger system or needed to support other client apps, a REST API would be the better choice.

## Setup and environment

Built locally on Linux. The app uses PostgreSQL by default.

Clone the repo:

```bash
# HTTPS
git clone https://github.com/KutloSeperi/reservations.git

# SSH
git clone git@github.com:KutloSeperi/reservations.git

cd reservations
```

Install dependencies and prepare the environment:

```bash
composer install
npm install
```

Copy the environment example:

```bash
cp .env.example .env
```

Set the database details in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=reservations
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Generate the application key:

```bash
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate --seed
```

Start the frontend and backend:

```bash
npm run dev
php artisan serve
```

Open the URL shown by `php artisan serve` in your browser.

## Testing

Run the full test suite:

```bash
php artisan test
```

Run only the reservation tests:

```bash
php artisan test --filter=ReservationTest
```

Run the reservation feature test file directly:

```bash
php artisan test tests/Feature/ReservationTest.php
```

Or run Pest directly:

```bash
./vendor/bin/pest tests/Feature/ReservationTest.php
```

## Data model

- `resources`: `name`, `type`, `status`, `capacity`
- `reservations`: `resource_id`, `customer_name`, `customer_email`, `start_at`, `end_at`, `notes`, `status`

Cancelled reservations are kept in the database and filtered out of overlap checks so they do not block new bookings.

No authentication is implemented, since the brief did not require it.

## Concurrency note

If two users attempt to book the same resource at the same exact moment, there is a possible race condition where both requests could pass the overlap check before saving. I did not fully solve that within the timebox, but I considered:

- locking the resource row during availability checks and save
- enforcing a database-level constraint that prevents overlapping active reservations

A queue was not appropriate here, because the user needs an immediate booking result.

## Known limitations and assumptions

- Capacity is treated as the number of simultaneous active reservations allowed for a resource. For example, a resource with capacity 4 can have up to 4 overlapping active reservations.
- Concurrency is not fully handled.
- No authentication is implemented, so anyone can create or cancel reservations.
- Timezones are not explicitly handled; all times are treated as local.
- Resources are created via seed data only; there is no UI for managing resources.
- The resource list shows total capacity, not remaining spots for a specific requested period.

## AI usage

See `AI_USAGE.md` for details on how AI was used during this exercise.
