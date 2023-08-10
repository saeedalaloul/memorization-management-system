<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExamsExport implements
    ShouldAutoSize,
    WithEvents,
    WithColumnFormatting,
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
                $event->sheet->getDelegate()->setTitle('تقرير كل الإختبارات القرأنية');
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.exams.report', ['exams' => $this->exams]);
    }

    public function columnFormats(): array
    {
        return [
            'D' => DataType::TYPE_STRING,
        ];
    }
}
