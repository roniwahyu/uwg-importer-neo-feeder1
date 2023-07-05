<?php

namespace App\Imports;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithValidation;

class PengujianMahasiswaImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,WithTitle
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }
    public function title(): string
    {
        return 'template';
        
    }
    public function headingRow(): int
    {
        return 3;
    }

    public function rules(): array
    {
        return [
            '*.id_aktivitas' => 'required',
            '*.id_kategori_kegiatan' => 'required',
            // '*.id_dosen' => 'required',
            '*.nidn' => 'required',
            '*.penguji_ke' => [
                'required',
                Rule::in(['1', '2','3']),
            ]
        ];
    }
}
