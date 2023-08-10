<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SunnahExternalExamsExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $exams;

    public function __construct($exams)
    {
        $this->exams = $exams;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle('تقرير الإختبارات الخارجية');
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.sunnah_exams.external_exam_report', ['exams' => $this->exams]);
    }
}
