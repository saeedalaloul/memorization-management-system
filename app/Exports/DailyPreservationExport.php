<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DailyPreservationExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $daily_preservation;

    public function __construct($daily_preservation)
    {
        $this->daily_preservation = $daily_preservation;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle('تقرير الحفظ والمراجعة');
                $count = $this->daily_preservation->total();
                $range = "D".($count == 0 ? 3 : $count + 2).":"."H".($count == 0 ? 3 : $count + 2);
                $event->sheet->getDelegate()->mergeCells($range);
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.report_daily_preservation.report', ['reports_daily_preservation' => $this->daily_preservation]);
    }
}
