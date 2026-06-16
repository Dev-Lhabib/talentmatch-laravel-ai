<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'TalentMatch') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: ui-sans-serif, system-ui, sans-serif; background: #FDFDFC; color: #1b1b18; min-height: 100vh; }
        .container { max-width: 640px; margin: 0 auto; padding: 2rem 1rem; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem; max-width: 640px; margin: 0 auto; }
        nav a { text-decoration: none; color: #1b1b18; padding: 0.375rem 1.25rem; border: 1px solid #e3e3e0; border-radius: 2px; font-size: 0.875rem; }
        nav a:hover { border-color: #1915014a; }
        .btn { display: inline-block; padding: 0.5rem 1.25rem; background: #1b1b18; color: #fff; border: none; border-radius: 2px; font-size: 0.875rem; cursor: pointer; text-decoration: none; }
        .btn:hover { opacity: 0.9; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-size: 0.875rem; font-weight: 500; }
        .form-group input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e3e3e0; border-radius: 2px; font-size: 0.875rem; }
        .form-group input:focus { outline: none; border-color: #1915014a; }
        .errors { color: #dc2626; font-size: 0.875rem; margin-bottom: 1rem; }
        .errors ul { list-style: none; padding: 0; }
        .link { color: #f53003; text-decoration: underline; text-underline-offset: 4px; }
        .link:hover { text-decoration: none; }
    </style>
</head>
<body>
    <nav>
        <a href="/" style="border: none; font-weight: 500;">TalentMatch</a>
        <div style="display: flex; gap: 0.5rem;">
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn" style="font-size: 0.875rem;">Déconnexion</button>
                </form>
            @else
                @if(Route::has('login'))
                    <a href="{{ route('login') }}">Connexion</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}">Inscription</a>
                @endif
            @endauth
        </div>
    </nav>
    <main class="container">
        @yield('content')
    </main>
</body>
</html>
