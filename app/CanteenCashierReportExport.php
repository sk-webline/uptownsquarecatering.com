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


class CanteenCashierReportExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    protected $canteen_products;
    protected $purchases;
    protected $date;
    protected $break;

    public function __construct(object $canteen_products, object $purchases, string $date, object $break)
    {
        $this->canteen_products = $canteen_products;
        $this->purchases = $purchases;
        $this->date = $date;
        $this->break = $break;
    }

    public function collection()
    {

        $array[] = [];

        foreach ($this->canteen_products as $canteen_product) {

            $flag = false;
            foreach ($this->purchases as $purchase) {
                if($purchase->canteen_product_id == $canteen_product->id){
                    $flag = true;
                    $array[] = [
                        'Product Name' => $canteen_product->name,
                        'Total Orders' => $purchase->total_quantity
                    ];

                    break;
                }
            }

            if(!$flag){
                $array[] = [
                    'Product Name' => $canteen_product->name,
                    'Total Orders' => '0'
                ];
            }

        }

        return collect($array);


    }

    public function headings(): array
    {

        $carbon = Carbon::create($this->date);

        $headings [] = ['DATE', $carbon->format('D') . '. ' . $carbon->format('d/m/Y')];

        $headings [] = ['BREAK', ordinal($this->break->break_num) . ' Break', substr($this->break->hour_from, 0, 5) . ' - ' . substr($this->break->hour_to, 0, 5)];

        $headings [] = [];

        $headings [] = ['Product Name', 'Total Orders'];

        return $headings;


    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]]

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
