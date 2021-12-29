<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UsersExport implements
    ShouldAutoSize,
    WithHeadings,
    WithEvents,
    FromArray
{
    use Exportable;

    private array $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function array(): array
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            '#',
            'الاسم',
            'البريد الإلكتروني',
            'رقم الجوال',
            'رقم الهوية',
            'العنوان',
            'تاريخ الميلاد',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getDelegate()->setRightToLeft(true);
                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ],
                ];
                $event->sheet->getStyle('A1:G1')->applyFromArray($styleArray);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => DataType::TYPE_STRING,
            'C' => DataType::TYPE_STRING,
            'D' => DataType::TYPE_NUMERIC,
            'E' => DataType::TYPE_NUMERIC,
            'F' => DataType::TYPE_STRING,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
