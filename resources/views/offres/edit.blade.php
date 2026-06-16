@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem;">Modifier l'offre</h1>

    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('offres.update', $offre) }}">
        @csrf
        @method('PUT')

        @include('offres._form')

        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="{{ route('offres.show', $offre) }}" class="btn" style="background: transparent; color: #1b1b18; border: 1px solid #e3e3e0;">Annuler</a>
        </div>
    </form>
@endsection
