<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AllTeachersExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $teachers;

    public function __construct($teachers)
    {
        $this->teachers = $teachers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle(Str::limit('قاعدة بيانات جميع محفظي المركز', 31, ''));
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.grades.all_teachers_report', ['teachers' => $this->teachers]);
    }
}
