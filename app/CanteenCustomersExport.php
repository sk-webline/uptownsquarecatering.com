<?php

namespace App;

use App\Models\Card;
use App\Models\Organisation;
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


class CanteenCustomersExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    public function __construct(object $customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {

        $array = [];


        foreach ($this->customers as $customer) {

            $organisation = Organisation::find($customer->organisation_id);

            if($organisation != null){
                $organisation = $organisation->name;
            }

            $array[] = [
                'Customer UserName' => $customer->username,
                'RFID No' => $customer->rfid_no,
                'Parent Email' => $customer->parent_email,
                'Parent Name' => $customer->parent_name,
                'Organisation' => $organisation,
                'Registration Date' => Carbon::create($customer->created_at)->format('d/m/y H:i') ,
            ];
        }

        return collect($array);


    }

    public function headings(): array
    {


        return [
            'Customer UserName',
            'RFID No',
            'Parent Email',
            'Parent Name',
            'Organisation',
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


        if (in_array($cell->getColumn(), ['B'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

}
