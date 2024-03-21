<?php

namespace App;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class CustomersExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    public function __construct(object $customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {

        $array = [];

        $this->customers;

        foreach ($this->customers as $customer) {

            $user = $customer->user;

            $array[] = ['Customer Name' => $user->name,
                'Email' => $user->email,
                'Registration Date' => Carbon::create($user->created_at)->format('d/m/y H:i') ,
            ];
        }

        return collect($array);


    }

    public function headings(): array
    {


        return [
            'Customer Name',
            'Email',
            'Registration Date',
        ];

    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

        ];
    }

    public function columnFormats(): array
    {
        return [
//            'A' => DataType::TYPE_STRING2,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {


        if (in_array($cell->getColumn(), [])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

}
