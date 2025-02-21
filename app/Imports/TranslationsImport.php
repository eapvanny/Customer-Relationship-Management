<?php

namespace App\Imports;

use App\Models\Translation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

class TranslationsImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts

{
      /**
    * @p
    * @return \Illuminate\Database\Eloquent\Model|nullaram array $row
    *
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $updateResult = Translation::updateOrCreate(
                ['locale' => $row['locale'], 'item' => $row['item']],
                [
                    'locale' => $row['locale'],
                    'item' => $row['item'],
                    'text' => $row['text'],
                ]
            );
        }
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
            '*.locale' => 'required|in:en,kh',
            '*.item' => 'required',
            '*.text' => 'required',
        ];
    }

}
