@extends('layouts.app')

{{-- @section('title', 'Bienvenido a la API') --}}

@section('content')
    <main class="text-neutral-100">
        <!-- Título -->
        <x-header />

        <section class="flex place-content-center mt-10">
            <h4 class="px-5 py-1 rounded-full border border-neutral-200 text-xs">
                API VERSION {{ preg_replace('/[^0-9]/', '', $apiVersion) }}
            </h4>
        </section>

        <!-- Descripción -->
        <section class="relative flex justify-center items-center mt-7">
            <img src="{{ asset('arrow.svg') }}" alt="" class="absolute rotate-[25deg] w-28 top-44 right-20">
            <div class="relative ">
                <p class="text-4xl font-mono font-medium">
                    <span class="block">Bienvenido a la API de gestión</span>
                    <span class="block">de tickets de soporte<span class="text-yellow-500">.</span></span>
                </p>
                <h3 class=" mt-5 text-center">Una documentación sencilla y práctica.</h3>
            </div>
        </section>

        <section class="flex place-content-center mt-7">
            <a href="{{ url('/api'.'/'. $apiVersion.'/doc') }}" class="px-5 py-1.5 bg-slate-600 rounded-md hover:bg-slate-700 transition-colors ease-in-out duration-300 text-sm font-semibold">
                Comenzar <span class="text-xs">🚀</span>
            </a>
        </section>

        <x-landing-card />
    </main>
@endsection
