<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DailyMemorizationExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $daily_memorization;

    public function __construct($daily_memorization)
    {
        $this->daily_memorization = $daily_memorization;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle('تقرير الحفظ والمراجعة');
//                $count = count($this->daily_memorization['data']);
//                $range = "D".($count == 0 ? 3 : $count + 2).":"."H".($count == 0 ? 3 : $count + 2);
//                $event->sheet->getDelegate()->mergeCells($range);
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.report_daily_memorization.report', ['reports_daily_memorization' => $this->daily_memorization]);
    }
}
