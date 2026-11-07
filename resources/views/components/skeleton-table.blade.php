@props([
    'rows' => 5,
    'cols' => 6,
    'avatarCol' => 0 // Índice de columna con avatar (0 por defecto, o null si no aplica)
])

<div {{ $attributes->merge(['class' => 'w-full select-none overflow-hidden']) }} role="status" aria-label="Cargando datos...">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-500">
            <thead class="border-b border-slate-200 bg-slate-100/90 text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    @for($c = 0; $c < $cols; $c++)
                        <th scope="col" class="px-5 py-4">
                            <div class="h-4 w-20 rounded-md ax-skeleton-shimmer"></div>
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @for($r = 0; $r < $rows; $r++)
                    <tr class="transition-colors">
                        @for($c = 0; $c < $cols; $c++)
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($c === $avatarCol)
                                    {{-- Columna con avatar o thumbnail cuadrado --}}
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg ax-skeleton-shimmer"></div>
                                        <div class="space-y-2 flex-1">
                                            <div class="h-4 w-32 rounded-md ax-skeleton-shimmer"></div>
                                            <div class="h-3 w-20 rounded-md ax-skeleton-shimmer"></div>
                                        </div>
                                    </div>
                                @elseif($c === $cols - 1)
                                    {{-- Botón de acción / detalle --}}
                                    <div class="flex items-center justify-end">
                                        <div class="h-7 w-24 rounded-lg ax-skeleton-shimmer"></div>
                                    </div>
                                @elseif($c === 1 && $cols >= 5)
                                    {{-- Badge de estado / tipo --}}
                                    <div class="h-6 w-20 rounded-lg ax-skeleton-shimmer"></div>
                                @else
                                    <div class="h-4 w-20 rounded-md ax-skeleton-shimmer"></div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <span class="sr-only">Cargando información...</span>
</div>

