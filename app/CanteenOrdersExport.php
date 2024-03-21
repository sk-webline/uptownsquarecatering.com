<?php

namespace App;

use App\Models\AppOrder;
use App\Models\AppOrderDetail;
use App\Models\CanteenAppUser;
use App\Models\Card;
use Faker\Provider\Text;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;


class CanteenOrdersExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    public function __construct(object $orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {

        $array = [];

        foreach ($this->orders as $order) {

            $canteen_username = null;
            $parent_name = null;
            $parent_email = null;
            $rfid_no = null;
            if ($order->user_id != null) {
                $canteen_username = $order->user_id;
                $canteen_user = CanteenAppUser::find($order->user_id);

                if($canteen_user!=null){
                    $canteen_username = $canteen_user->username;
                    $rfid_card = $canteen_user->card;

                    if($rfid_card!=null){
                        $rfid_no = $rfid_card->rfid_no;
                    }else{
                        $rfid_no = '';
                    }


                    $parent_customer = User::find($canteen_user->user_id);
                    if($parent_customer!=null){
                        $parent_name = $parent_customer->name;
                        $parent_email = $parent_customer->email;
                    }

                }else{

                    $parent_name =  json_decode($order->shipping_address)->parent_fullName;
                    $parent_email = json_decode($order->shipping_address)->email;
                }

            } else {

                $canteen_username = json_decode($order->shipping_address)->app_username;
                $parent_name =  json_decode($order->shipping_address)->parent_fullName;
                $parent_email = json_decode($order->shipping_address)->email;
            }

            $total_quantity = AppOrderDetail::where('app_order_id', $order->id)->sum('total_quantity');

            $array[] = [
                'Order Code' => $order->code,
                'Num. of Products' => intval($total_quantity),
                'Canteen Customer' => $canteen_username,
                'RFID No' => $rfid_no,
                'Parent Customer' => $parent_name,
                'Email' => $parent_email,
                'Amount' => single_price($order->grand_total),
                'Payment Status' => $order->payment_status,
                'Order Date' => \Carbon\Carbon::create($order->created_at)->format('d/m/Y')
            ];

        }


        return collect($array);


    }

    public function headings(): array
    {


        return [
            'Order Code',
            'Num. of Products',
            'Canteen Customer',
            'RFID No',
            'Parent Customer',
            'Email',
            'Amount',
            'Payment Status',
            'Order Date',
        ];


    }

    public function columnFormats(): array
    {
        return [
//            'A' => DataType::TYPE_STRING2,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

        ];
    }

    public function bindValue(Cell $cell, $value)
    {


        if (in_array($cell->getColumn(), ['A', 'C','D'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
