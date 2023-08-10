<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlySunnahMemorizationExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $monthly_memorization;
    private $month, $teacher_name;

    public function __construct($monthly_memorization, $month, $teacher_name)
    {
        $this->monthly_memorization = $monthly_memorization;
        $this->month = $month;
        $this->teacher_name = $teacher_name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle(Str::limit( 'تقرير شهر '. $this->month .' '.$this->teacher_name  , 31,''));
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.report_monthly_memorization.sunnah_report', ['reports_monthly_memorization' => $this->monthly_memorization]);
    }
}
