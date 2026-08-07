# AI Usage Record

## Tools used

- Claude: used to plan the project and clarify design decisions before writing code.

## What it was used for

I used AI to help interpret the brief, focus the implementation, and make architectural choices. Specifically, I used it to:

- Identify the most important requirements in the brief, especially overlap logic and test coverage.
- Choose between Inertia and a separate REST API with a Vue frontend.
- Keep the data model simple with only `resources` and `reservations`, since authentication was not required.
- Clarify the fields and validation rules for each table.
- Design the overlap check so that reservations ending exactly when another begins are not considered overlapping.
- Understand concurrency concerns for simultaneous reservation attempts and consider appropriate solutions.
- Decide against using a queue for reservation processing.
- Resolve setup issues such as the Laravel installer not being available and switching from HTTPS to SSH for GitHub.

## Prompts that shaped the solution

1. "Should I use Inertia or a REST API with a separate Vue frontend for this size of project?"

2. "How can I stop two people from booking the same resource at the same time?"

3. "Do I need a users table and a separate bookings table for this brief?"

## How I verified AI suggestions

I did not accept AI-generated ideas without validation. After implementing the overlap check and validation rules, I wrote the required tests and ran `php artisan test`. The initial test run exposed a real issue: `Resource::factory()` was not available because the `Resource` model was missing the `HasFactory` trait. After fixing that, all four required tests passed.

## Suggestion rejected or changed

AI suggested using a queue for reservation requests. I rejected that approach because queues are intended for background work and do not provide the immediate user response needed for booking. Instead, I focused on locking or database-level constraints as the right way to address simultaneous reservation attempts.
