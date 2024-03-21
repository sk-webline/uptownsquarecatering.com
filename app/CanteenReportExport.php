<?php

namespace App;

use App\Models\Card;
use App\Models\Organisation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class CanteenReportExport extends DefaultValueBinder implements FromCollection, WithMultipleSheets, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    protected $organisations;
    protected $start_date;
    protected $end_date;
    protected $dates;
    protected $formatted_dates;

    public function __construct(object $organisations, string $start_date,  string $end_date, array $dates, array $formatted_dates)
    {
        $this->organisations = $organisations;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->dates = $dates;
        $this->formatted_dates = $formatted_dates;
    }

    public function collection()
    {

        $array = [];

        return collect($array);


    }

    public function headings(): array
    {


        return [
//            'Customer UserName',
//            'RFID No',
//            'Parent Email',
//            'Parent Name',
//            'Organisation',
//            'Registration Date',
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

    public function sheets(): array
    {
        $sheets = [];

        try{
            foreach ($this->organisations as $key => $organisation){
                $sheets[] = new CanteenReportSheet($organisation, $organisation->name, $this->dates, $this->formatted_dates, $this->start_date, $this->end_date);
            }
        }catch (\Exception $e) {
            dd($e->getMessage(), $e);
        }

        return $sheets;

    }

}
