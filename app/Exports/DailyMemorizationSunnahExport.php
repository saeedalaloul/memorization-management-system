<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DailyMemorizationSunnahExport implements
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
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.report_daily_memorization_sunnah.report', ['reports_daily_memorization' => $this->daily_memorization]);
    }
}
