<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AyudaController extends Controller
{
    public function index(Request $request): View
    {
        $guides = config('ayuda');

        if ($request->user()->rol !== 'admin') {
            unset($guides['inventario'], $guides['devolucion']);
        }

        $topic = $request->query('tema');
        $guide = is_string($topic) ? ($guides[$topic] ?? null) : null;
        abort_if($topic !== null && $guide === null, 404);

        $query = $request->query('q', '');
        $query = is_string($query) ? Str::limit(trim($query), 120, '') : '';
        $normalize = static fn (string $text) => Str::lower(Str::ascii($text));
        $words = preg_split('/[^a-z0-9]+/', $normalize($query), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_diff($words, ['como', 'que', 'en', 'el', 'la', 'los', 'las', 'de', 'del', 'un', 'una', 'a', 'puedo']);
        $scores = [];

        foreach ($guides as $key => $item) {
            $haystack = $normalize(implode(' ', [$item['title'], $item['question'], $item['keywords']]));
            foreach (array_unique($words) as $word) {
                if (str_contains($haystack, $word)) {
                    $scores[$key] = ($scores[$key] ?? 0) + 1;
                }
            }
        }

        arsort($scores);
        $results = [];
        foreach (array_keys($scores) as $key) {
            $results[$key] = $guides[$key];
        }

        return view('ayuda.index', compact('guides', 'guide', 'topic', 'query', 'results'));
    }
}


