<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

// LIBRARIES
use App\Libraries\Helper;

class ProductTemplateExportView implements FromView
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
        $authorize = Helper::authorizing($this->module, 'Import Excel');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        return null;
    }

    public function view(): View
    {
        return view('admin.product.import_excel_template', [
            'data' => $this->data
        ]);
    }
}
