<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

// MODELS
use App\Models\Product;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'title' => $row['Title'],
            'subtitle' => $row['Subtitle'],
            'description' => $row['Description'],
        ]);
    }

    /**
     * Heading row on different row
     * In case your heading row is not on the first row, you can easily specify this.
     * The 2nd row will now be used as heading row.
     */
    public function headingRow(): int
    {
        return 1;
    }
}
