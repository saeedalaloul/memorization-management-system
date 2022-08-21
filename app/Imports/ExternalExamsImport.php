<?php

namespace App\Imports;

use App\Models\ExternalExam;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExternalExamsImport implements ToCollection, WithStartRow
{
    use Importable;


//    public function rules(): array
//    {
//      return [
//          'id' => 'required|string',
//          'date' => 'required||date|date_format:Y-m-d',
//      ];
//    }
    public function startRow(): int
    {
        return 0;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $c) {
            ExternalExam::create([
                'id' => $c[1],
                'mark' => $c[7],
                'date' => $c[8],
            ]);
        }
    }
}
