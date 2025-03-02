<!-- Tarjeta con las rutas -->
@php
    $apiRoutes = collect(Route::getRoutes())->filter(
        fn($route) => str_starts_with($route->uri(), "api"))->count();
@endphp

<article class="mt-7 bg-neutral-900 bg-opacity-70 rounded-lg py-5 px-7 w-[560px] mx-auto">
    <section class="flex items-center justify-between text-base font-medium">
        <h2>Rutas de la API</span></h2>
        <div class="flex items-center justify-center gap-x-1">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-300">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 19a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M19 7a2 2 0 1 0 0 -4a2 2 0 0 0 0 4z" />
                <path d="M11 19h5.5a3.5 3.5 0 0 0 0 -7h-8a3.5 3.5 0 0 1 0 -7h4.5" />
            </svg>
            <span>{{ $apiRoutes }}</span>
        </div>
    </section>

    <section class="mt-3">
        <h2 class="text-base font-medium mb-3">Rutas
            <span class="text-sm text-green-300">GET</span>
            <span class="text-sm">&</span>
            <span class="text-sm text-green-300">SHOW</span>
        </h2>
        <div class="w-full h-[392px] overflow-y-auto">
            <ul class="text-sm text-left space-y-[5px] mr-[5px]">
                @foreach(Route::getRoutes() as $route)
                    @if(in_array("GET", $route->methods()) && str_starts_with(
                        $route->uri(), "api"))
                        <li class="bg-neutral-800 px-3 py-1 rounded-[4px]">
                            {{ url($route->uri()) }}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </section>
</article>
