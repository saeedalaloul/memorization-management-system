<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class GroupStudentsExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $students;
    private $teacher_name;

    public function __construct($students, $teacher_name)
    {
        $this->students = $students;
        $this->teacher_name = $teacher_name;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle(Str::limit('قاعدة بيانات طلاب حلقة المحفظ ' . $this->teacher_name, 31, ''));
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.groups.group_students_report', ['students' => $this->students]);
    }
}
