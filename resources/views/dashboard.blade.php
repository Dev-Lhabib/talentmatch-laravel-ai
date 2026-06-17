@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-semibold text-white">Dashboard</h1>
    <p class="mt-2 text-text-secondary">Bienvenue, {{ auth()->user()->name }}.</p>
@endsection
