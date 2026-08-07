# Architecture and Design

## Inertia instead of a separate API

This application uses Laravel with Inertia and Vue rather than a separate REST API plus a standalone frontend.

Reason: the app is small and time is limited. Building a separate API would require extra routes, response formatting, and frontend integration without adding much value for this scope. Inertia lets Laravel handle routing, validation, and server-side state while Vue renders the interface and handles client-side behavior.

Trade-off: a separate API would be more appropriate for a larger system or one where other applications need to consume the backend. That is not required here.

## Data model

The data model is intentionally simple with only two main tables:

- `resources`: `name`, `type`, `status`, `capacity`
- `reservations`: `resource_id`, `customer_name`, `customer_email`, `start_at`, `end_at`, `notes`, `status`

The app assumes that `capacity` means the number of identical units of a resource that can be reserved at the same time. For example, a capacity of `2` means two concurrent reservations are allowed for the same period.

There is no `users` table in the app, and the reservation concept is used consistently rather than introducing a separate `booking` term.

### Simplicity and usability

The implementation was kept intentionally simple and user-friendly rather than over-engineered. The focus is on the reservation workflow and a clean user experience, while still preserving back-end correctness.

### Cancellation strategy

Cancelling a reservation sets `status` to `cancelled` instead of deleting the row. This preserves history and makes overlap checking easier by excluding cancelled reservations from conflict checks.

## Overlap check

A new reservation is considered conflicting when:

```sql
existing.start_at < new.end_at
AND existing.end_at > new.start_at
```

This check only considers reservations where `cancelled_at` is null.

Using strict `<` and `>` allows back-to-back reservations where one ends exactly when another begins, which matches the intended behavior.

## Concurrency and race conditions

There is a potential race condition if two requests try to book the same resource at the same moment. Both requests could pass the overlap check before either is saved.

That is a real issue, and it is noted here rather than ignored.

### Better solutions for full production

- Use a database transaction with a row lock when checking and saving a reservation.
- Or enforce non-overlapping reservations at the database level with a PostgreSQL constraint.

A queue is not a good fit for this problem by itself because booking is an immediate user action and the user is waiting for a result, so deferred processing does not solve the core conflict issue.
