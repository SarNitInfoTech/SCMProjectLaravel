<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class GenericImport implements ToArray
{
    /**
     * Parse the spreadsheet into a raw PHP array of rows.
     *
     * @param array $array
     * @return array
     */
    public function array(array $array)
    {
        return $array;
    }
}
