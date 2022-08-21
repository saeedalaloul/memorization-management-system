<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GradeStudentsExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $students;
    private $grade_name;

    public function __construct($students, $grade_name)
    {
        $this->students = $students;
        $this->grade_name = $grade_name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle(Str::limit('قاعدة بيانات طلاب ' . $this->grade_name, 31, ''));
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.groups.grade_students_report', ['students' => $this->students]);
    }
}
