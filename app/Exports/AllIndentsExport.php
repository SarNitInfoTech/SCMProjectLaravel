<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllIndentsExport implements FromView, ShouldAutoSize
{
    protected $records;
    protected $kpis;
    protected $filterText;

    public function __construct($records, $kpis = [], $filterText = '')
    {
        $this->records = $records;
        $this->kpis = $kpis;
        $this->filterText = $filterText;
    }

    public function view(): View
    {
        return view('exports.all_indents_excel', [
            'records'    => $this->records,
            'kpis'       => $this->kpis,
            'filterText' => $this->filterText,
        ]);
    }
}
