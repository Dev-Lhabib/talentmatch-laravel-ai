@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 1.5rem;">Connexion</h1>

    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Se connecter</button>

        <p style="margin-top: 1rem; font-size: 0.875rem;">
            Pas encore de compte ? <a href="{{ route('register') }}" class="link">S'inscrire</a>
        </p>
    </form>
@endsection
