<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class QuranMemorizersExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $quran_memorizers;

    public function __construct($quran_memorizers)
    {
        $this->quran_memorizers = $quran_memorizers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle('تقرير الحفظة');
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.quran_memorizers.report', ['quran_memorizers' => $this->quran_memorizers]);
    }
}
