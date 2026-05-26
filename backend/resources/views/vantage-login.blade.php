<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vantage Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #1e293b; border-radius: 12px; padding: 2rem; width: 100%; max-width: 380px; }
        h1 { color: #f1f5f9; font-size: 1.25rem; margin-bottom: 1.5rem; }
        label { display: block; color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.4rem; }
        input { width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #f1f5f9; font-size: 0.95rem; margin-bottom: 1rem; }
        input:focus { outline: none; border-color: #6366f1; }
        button { width: 100%; padding: 0.65rem; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 0.95rem; cursor: pointer; }
        button:hover { background: #4f46e5; }
        .error { color: #f87171; font-size: 0.85rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Vantage &mdash; Admin Login</h1>

        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="/vantage-login">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>
