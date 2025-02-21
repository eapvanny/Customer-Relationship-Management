<?php

namespace App\Http\Controllers;

use App\Exports\TranslationsExport;
use App\Http\Helpers\ImportHelper;
use App\Imports\TranslationsImport;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TranslationController extends Controller
{
    protected $locales = ['en', 'kh']; // Defined locales.

    /**
     * Display a listing of the translations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $translations = [];

        $locales = $this->locales;

        $locale = 'kh';
        if ($request->has('locale')) {
            $locale = $request->locale;
        }

        if ($request->ajax()) {
            $translations = Translation::where('locale', $locale)->get();

            return DataTables::of($translations)
                ->addColumn('item', function ($data) {
                    return $data->item;
                })
                ->addColumn('text', function ($data) {
                    return __($data->text);
                })
                ->addColumn('action', function ($data) {
                    return '<div class="change-action-item">
                        <a title="Edit" href="#" data-item="' . $data->item . '" data-text="' . $data->text . '" class="btn btn-primary btn-sm btn-edit-translate" data-bs-toggle="modal" data-bs-target="#editTranslateModal"><i class="fa fa-edit"></i></a>
                        <a href="' . route('translation.destroy', $data->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.translations.list', compact('translations', 'locale', 'locales'));
    }

    /**
     * Store or update a translation entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'text' => 'required|string|max:255',
            'locale' => 'required|in:en,kh',    
        ]);

        if (!in_array($validated['locale'], $this->locales)) {
            return redirect()->back()->with('error', 'Invalid language selected!');
        }

        // Check if translation item already exists for the selected locale.
        $translation = Translation::where('locale', $validated['locale'])
            ->where('item', $validated['item'])
            ->first();
        if ($translation) {
            $translation->update(['text' => $validated['text']]);
            $this->applyTranslation($validated['locale']);
            return redirect()->back()->with('success', 'Translation updated successfully!');
        } else {
            Translation::create($validated);
            $this->applyTranslation($validated['locale']);
            return redirect()->back()->with('success', 'Translation Created successfully!');
        }

        return redirect()->back()->with('success', 'Translation updated successfully!');
    }

    /**
     * Apply translations to the language files.
     *
     * @param  string  $locale
     */
    public function applyTranslation(string $locale)
    {
        $translations = Translation::where('locale', $locale)->get()->pluck('text', 'item');

        // Define the file path where the translation JSON file will be stored.
        $localPath = base_path("lang/{$locale}.json");

        // Write translations to the JSON file.
        File::put($localPath, json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Remove a translation entry.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $translation = Translation::findOrFail($id);
        $locale = $translation->locale;
        $translation->delete();
        $this->applyTranslation($locale);

        return redirect()->back()->with('success', 'Translation deleted successfully!');
    }

    /**
     * Import translations from an Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new TranslationsImport, $request->file('import_file'));

            // Rebuild language files after import
            foreach ($this->locales as $locale) {
                $this->applyTranslation($locale);
            }

            return redirect()->back()->with('success', 'Translations imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }


    /**
     * Export translations to an Excel file.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new TranslationsExport(), 'translations_' . now()->format('Y_m_d_His') . '.xlsx');
    }
}
