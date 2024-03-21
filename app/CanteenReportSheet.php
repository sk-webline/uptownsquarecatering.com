<?php

namespace App;

use App\Models\CanteenMenu;
use App\Models\CanteenPurchase;
use App\Models\CanteenProduct;
use Illuminate\Support\Facades\DB;
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
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class CanteenReportSheet extends DefaultValueBinder implements FromCollection, withTitle, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder, WithEvents
{

    use Exportable;

    protected $organisation;

    protected $breaks;
    protected $sheetName;

    protected $dates;

    protected $canteen_settings;
    protected $formatted_dates;

    protected $start_date;

    protected $end_date;

    public function __construct(object $organisation, string $sheetName, array $dates, array $formatted_dates, string $start_date, string $end_date)
    {
        $this->organisation = $organisation;

        $this->canteen_settings = $organisation->current_canteen_settings();
        $this->breaks = $organisation->breaks;
        $this->sheetName = $sheetName;
        $this->dates = $dates;
        $this->formatted_dates = $formatted_dates;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $array = [];
        if($this->canteen_settings == null){
            return collect($array);
        }

        $product_ids = CanteenMenu::where('canteen_setting_id', $this->canteen_settings->id)->pluck('canteen_product_id')->toArray();
        $product_ids = array_unique($product_ids);
        $products = \App\Models\CanteenProduct::whereIn('id', $product_ids)->get();

        $purchases = CanteenPurchase::select('canteen_purchases.canteen_product_id', 'canteen_purchases.date', 'canteen_purchases.break_num', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
            ->join('canteen_products', 'canteen_products.id', '=', 'canteen_purchases.canteen_product_id')
            ->where('canteen_purchases.date', '>=', $this->start_date)
            ->where('canteen_purchases.date', '<=', $this->end_date)
            ->where('canteen_purchases.canteen_setting_id', $this->canteen_settings->id)
            ->groupBy('canteen_purchases.canteen_product_id','canteen_purchases.date', 'canteen_purchases.break_num')
            ->get();


        $purchase_totals = CanteenPurchase::select('canteen_purchases.date', 'canteen_purchases.break_num', DB::raw('SUM(canteen_purchases.quantity) as total_quantity'))
            ->join('canteen_products', 'canteen_products.id', '=', 'canteen_purchases.canteen_product_id')
            ->where('canteen_purchases.date', '>=', $this->start_date)
            ->where('canteen_purchases.date', '<=', $this->end_date)
            ->where('canteen_purchases.canteen_setting_id', $this->canteen_settings->id)
            ->groupBy('canteen_purchases.date', 'canteen_purchases.break_num')
            ->get();



        $temp_array [] = 'Break';
        foreach ($this->dates as $date){
            foreach ($this->breaks as $break){
                array_push($temp_array, $break->break_num);
            }
        }

        $array[] = $temp_array;

        foreach ($products as $product){
            $temp_array= null;
            $temp_array [] = $product->name;

            foreach ($this->dates as $key => $date){
                foreach ($this->breaks as $break){

                    $flag = false;

                    foreach($purchases as $purchase) {
                        if ($purchase->date == $this->formatted_dates[$key] && $purchase->break_num == $break->break_num && $purchase->canteen_product_id == $product->id) {

                            $flag = true;
                            array_push($temp_array, $purchase->total_quantity);
                            break;
                        }
                    }
                    if(!$flag){
                        array_push($temp_array,  '0');
                    }

                }
            }

            $array[] = $temp_array;
        }

        $temp_array= null;
        $temp_array [] = 'Totals';

        foreach ($this->dates as $key => $date){

            if(count($this->breaks)>0){
                foreach ($this->breaks as $break){

                    $flag = false;

                    foreach($purchase_totals as $purchase) {
                        if ($purchase->date == $this->formatted_dates[$key] && $purchase->break_num == $break->break_num) {

                            $flag = true;
                            array_push($temp_array, $purchase->total_quantity);
                            break;
                        }
                    }
                    if(!$flag){
                        array_push($temp_array,  '0');
                    }

                }
            }else{
                array_push($temp_array,  '0');
            }

        }

        $array[] = $temp_array;


        return collect($array);


    }

    public function headings(): array
    {
        if($this->canteen_settings == null){
            return [];
        }

        $headings[] = 'Dates';
        foreach ($this->dates as $date){
            array_push($headings, $date);
            for($i=0; $i< count($this->breaks)-1; $i++){
                array_push($headings, '');
            }
        }


        return $headings;

    }


    public function styles(Worksheet $sheet)
    {

        $mergeCount = count($this->breaks); // Replace with your actual variable

        if($mergeCount>0){
            $startColumn = 2; // Start from column B

            // Set minimum width for each cell
            for ($i = 0; $i < count($this->dates) * $mergeCount; $i++) {
                $sheet->getColumnDimensionByColumn($startColumn)->setAutoSize(false);
                $sheet->getColumnDimensionByColumn($startColumn)->setWidth(7); // Set your desired minimum width
                $startColumn += 1; //$mergeCount+1; // Move to the next set of cells
            }
        }


        // Set styles for the entire sheet
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A' )->getAlignment()->setHorizontal('left');

        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['bold' => true]],
            // Style the second row with a grey background color.
            2 => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]],
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

    public function title(): string
    {
        return $this->sheetName;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $startColumn = 2; // Start merging from column B
                $columnCount = count($this->dates); // Assuming $this->dates contains your columns dynamically
                $merge_count = count($this->breaks);

                if($merge_count>0){

                    // Merge cells in the first row based on the specified count
                    for ($i = 0; $i < $columnCount; $i++) {
                        $endColumn = $startColumn + ($merge_count-1);
                        $event->sheet->getDelegate()->mergeCellsByColumnAndRow($startColumn, 1, $endColumn, 1);
                        $startColumn = $endColumn + 1; // Move to the next set of cells
                    }
                }


            },
        ];
    }

}
