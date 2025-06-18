<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="customer-dashboard-container" data-testid="customer-dashboard">
        <nav class="header-nav">
            <div class="user-menu" data-testid="user-menu">
                <span>Welcome, {{ auth()->user()->first_name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </nav>
        <main>
            <h1>Customer Dashboard</h1>
            <p>Welcome to your customer dashboard!</p>
        </main>
    </div>
</body>
</html>