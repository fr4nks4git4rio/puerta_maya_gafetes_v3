<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartnersImport implements ToCollection
{
    use Importable;
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
//        dd($rows);
    }
}
