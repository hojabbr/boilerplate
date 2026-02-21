<?php

namespace App\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\TranslationLoader\LanguageLine;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationsCsvController extends Controller
{
    /**
     * Stream CSV export of language lines (group *). Columns: key, then one per locale.
     */
    public function export(Request $request): StreamedResponse
    {
        if (! Gate::allows('use-translation-manager')) {
            abort(403);
        }

        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $lines = LanguageLine::query()->where('group', '*')->orderBy('key')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="translations.csv"',
        ];

        return response()->streamDownload(function () use ($lines, $locales): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_merge(['key'], $locales));
            foreach ($lines as $line) {
                $text = $line->text ?? [];
                $row = [$line->key];
                foreach ($locales as $locale) {
                    $row[] = $text[$locale] ?? '';
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'translations.csv', $headers);
    }
}
