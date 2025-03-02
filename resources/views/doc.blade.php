@extends('layouts.app')

@section('title', 'Documentación de la API')

@section('content')
    <main class="text-neutral-100">
        <!-- Título -->
        <x-header />

        <!-- Descripción -->
        <p class="mt-5 text-center text-base font-medium">Actualmente, la API de gestión tickets está en la versión
            {{ preg_replace('/[^0-9]/', '', $apiVersion) }}
        </p>

        <x-route-card />
    </main>
@endsection
