@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 1.5rem;">Dashboard</h1>

    <p style="margin-bottom: 1rem;">Bienvenue, <strong>{{ auth()->user()->name }}</strong> !</p>
    <p style="font-size: 0.875rem; color: #706f6c;">Vous êtes connecté avec l'email : {{ auth()->user()->email }}</p>
@endsection
