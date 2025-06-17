# Route Organization

This directory contains all application routes organized by feature/domain for better maintainability.

## Structure

```
routes/
├── web.php                 # Main route file (includes all others)
├── console.php            # Artisan commands
├── auth/                  # Authentication routes
│   ├── auth.php          # Login, logout, role selection
│   ├── registration.php  # Creator and customer registration
│   ├── email-verification.php # Email verification flow
│   └── password-reset.php # Password reset flow
├── creator/               # Creator-specific routes
│   ├── setup.php         # Creator onboarding (timezone, profile)
│   └── dashboard.php     # Creator dashboard and features
├── customer/              # Customer-specific routes
│   └── dashboard.php     # Customer dashboard and features
└── admin/                 # Admin routes (future)
```

## Route Groups and Middleware

### Auth Routes (`/auth/`)
- **auth.php**: Basic authentication (login, logout, choose role)
- **registration.php**: User registration for creators and customers
- **email-verification.php**: Email verification with `auth` middleware
- **password-reset.php**: Password reset flow

### Creator Routes (`/creator/`)
- **setup.php**: Protected with `['auth', 'creator']` middleware
  - Timezone selection
  - Profile setup wizard
- **dashboard.php**: Protected with `['auth', 'creator']` middleware
  - Dashboard access
  - Future: events, availability, reservations management

### Customer Routes (`/customer/`)
- **dashboard.php**: Protected with `['auth']` middleware
  - Dashboard access  
  - Future: browse creators, manage reservations, profile

## Adding New Routes

When adding new routes:

1. **Choose the appropriate domain folder** (auth/, creator/, customer/, admin/)
2. **Create new files for major features** (e.g., `creator/events.php`)
3. **Include the new file in `web.php`** using `require __DIR__ . '/path/to/file.php'`
4. **Use appropriate middleware** for security
5. **Follow naming conventions** for route names

### Example: Adding Creator Events Management

```php
// routes/creator/events.php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Creator\EventController;

Route::middleware(['auth', 'creator'])->group(function () {
    Route::get('/creator/events', [EventController::class, 'index'])->name('creator.events');
    Route::post('/creator/events', [EventController::class, 'store'])->name('creator.events.store');
    Route::get('/creator/events/{event}', [EventController::class, 'show'])->name('creator.events.show');
    Route::put('/creator/events/{event}', [EventController::class, 'update'])->name('creator.events.update');
    Route::delete('/creator/events/{event}', [EventController::class, 'destroy'])->name('creator.events.destroy');
});
```

Then add to `web.php`:
```php
require __DIR__ . '/creator/events.php';
```

## Benefits

- **Modular organization**: Easy to find and maintain related routes
- **Clear separation of concerns**: Each domain has its own routes
- **Scalable**: Easy to add new features without cluttering main route file
- **Team development**: Different team members can work on different domains
- **Security**: Easier to apply appropriate middleware to related routes