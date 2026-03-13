<?php

namespace App\Imports;

use App\NombreModelo;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;

class DatosImport implements ToArray
{
    public function array($row) {
        return $row;
    }
}
