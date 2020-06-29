<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\Product;

class ProductExportView implements FromView
{
    use Exportable;

    // SET THIS MODULE
    private $module = 'Product';

    public function __construct()
    {
        $this->data = $this->get_data();
    }

    private function get_data()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Export Excel');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        // GET THE DATA
        $query = Product::select(
            'title',
            'subtitle',
            'description',
            'created_at',
            'updated_at'
        )
            ->whereNull('replaced_at')
            ->orderBy('id');

        $data = $query->get();

        return $data;
    }

    public function view(): View
    {
        return view('admin.product.export_excel', [
            'data' => $this->data
        ]);
    }
}
