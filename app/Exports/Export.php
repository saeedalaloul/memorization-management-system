<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Export implements FromQuery, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function query()
    {
        return User::query()->select(['id', 'name', 'email', 'email_verified_at', 'phone']);
    }

    /**
     * Return Headings for the exported data
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Id', 'Name', 'Email', 'Email Verified', 'Phone'
        ];
    }
}
