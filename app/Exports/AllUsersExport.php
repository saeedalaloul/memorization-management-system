<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AllUsersExport implements
    ShouldAutoSize,
    WithEvents,
    FromView
{
    use Exportable;

    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getDelegate()->setTitle(Str::limit('قاعدة بيانات جميع عاملي المركز', 31, ''));
            }
        ];
    }


    public function view(): View
    {
        return \view('pages.users.all_users_report', ['users' => $this->users]);
    }
}
