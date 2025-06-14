# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
- `composer dev` - Start development server with hot reload (runs Laravel server, queue worker, logs, and Vite)
- `php artisan serve` - Start Laravel development server
- `npm run dev` - Start Vite development server for frontend assets
- `npm run build` - Build production assets

### Testing
- `composer test` - Run all tests (clears config cache first)
- `php artisan test` - Run PHPUnit tests directly
- `php artisan test --filter=TestName` - Run specific test

### Database
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migrate with seeders
- `php artisan db:seed` - Run database seeders

### Code Quality
- `php artisan pint` - Format PHP code (Laravel Pint)

### Other Useful Commands
- `php artisan tinker` - Interactive Laravel shell
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - View application logs

## Architecture

This is a Laravel 12 application for a gaming/creator platform that enables creators to offer scheduled events and appointments. The architecture follows Laravel's modular approach with domain-specific organization.

### Core Domain Models
- **Users**: Base authentication and user management
- **Creators**: Content creators who offer events/appointments
- **Customers**: Users who book events
- **Event Types**: Different types of events creators can offer
- **Reservations**: Bookings made by customers
- **Availabilities**: Creator time slots
- **Schedules**: Creator availability patterns

### Directory Structure
- Models, Controllers, and Routes are organized by feature domain (authentication, creator, customer, event-type, public-profile, availability)
- Each domain has its own subdirectory within `app/Models/`, `app/Http/Controllers/`, and `routes/`
- Multi-user system with role-based access (creators vs customers)

### Key Features
- Event scheduling and booking system
- Multi-timezone support (stored in creator profiles and reservations)
- Payment integration (Stripe/PayPal support indicated by enums)
- Meeting platform integration (Zoom, Teams, Google Meet)
- Notification system for reservations and confirmations
- Platform commission system for creators

### Technology Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vite + TailwindCSS v4
- **Database**: SQLite (development), likely PostgreSQL/MySQL for production
- **Testing**: PHPUnit
- **Queue**: Laravel Queues for background jobs

### Theme Integration
The application includes multiple frontend themes:
- Admin theme for backend management
- Auth theme for login/registration
- Home theme for public-facing pages

### Testing
- PHPUnit configuration for Feature and Unit tests
- In-memory SQLite database for testing
- Test environment isolation with array drivers for cache/sessions