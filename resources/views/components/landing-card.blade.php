<!-- Tarjeta para la landing -->
@php
    $jsonData = json_encode([
        "status" => "Created",
        "success" => true,
        "message" => "Ticket creado con éxito",
        "status_code" => 201,
        "data" => [
            "codigo_ticket" => "PU-SW-000003",
            "asunto" => "Activación del Office",
            "descripcion" => "Bloquea algunas herramientas",
            "fecha_inicio" => "02/03/2025 11:10:10"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Aplicando colores a las claves y valores usando expresiones regulares
    $jsonData = preg_replace_callback('/"([^"]+)"\s*:/', fn($m) => '<span class="text-slate-400">"'.$m[1].'"</span>:', $jsonData);
    $jsonData = preg_replace_callback('/:\s*"([^"]+)"/', fn($m) => ': <span class="text-green-300">"'.$m[1].'"</span>', $jsonData);
    $jsonData = preg_replace('/:\s*201\b/', ': <span class="text-yellow-400">201</span>', $jsonData);
    // Se reemplaza las barras invertidas"\/" por el formato correcto "/"
    $jsonData = str_replace('\/', '/', $jsonData);
    $jsonData = preg_replace_callback(
        '/:\s*"(\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2})"/',
        fn($m) => ': <span class="text-cyan-500">"'.$m[1].'"</span>',
        $jsonData
    );
    $jsonData = preg_replace_callback('/:\s*(true|false)/', fn($m) => ': <span class="text-rose-400">'.$m[1].'</span>', $jsonData);
@endphp

<article class="mt-7 flex place-content-center">
    <div class="flex relative p-5 min-w-[485px] border-neutral-200 border rounded-md">
        <div class="flex relative flex-col gap-y-2 justify-center text-center text-sm">
            <div class="text-green-300 border border-neutral-200 rounded-md px-3 py-1.5">
                GET
            </div>
            <div class="text-yellow-400 border border-neutral-200 rounded-md px-3 py-1">
                POST
            </div>
            <div class="text-green-300 border border-neutral-200 rounded-md px-3 py-1">
                SHOW
            </div>
            <div class="text-purple-500 border border-neutral-200 rounded-md px-3 py-1">
                PATCH
            </div>
            <div class="text-red-400 border border-neutral-200 rounded-md px-3 py-1">
                DELETE
            </div>
        </div>

        <div class="flex items-center absolute top-0 left-28 right-0 bottom-0">
            <pre class="flex break-word">
                <code class="text-xs">{!! $jsonData !!}</code>
            </pre>
        </div>
    </div>
</article>
