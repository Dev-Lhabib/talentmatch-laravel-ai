@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">Mon profil</h1>
    </div>

    <div class="space-y-6">
        <div class="rounded-xl border border-border bg-card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-xl border border-border bg-card p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-xl border border-border bg-card p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
