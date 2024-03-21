<?php

namespace App;

use App\Models\Card;
use App\Models\CateringPlan;
use App\Models\CateringPlanPurchase;
use App\Models\OrganisationSetting;
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


class OrdersExport extends DefaultValueBinder implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
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

            if ($order->user_id != null) {
                $user = \App\User::find($order->user_id);
                $customer = $user->name;
                $email = $user->email;
            } else {
                $customer = json_decode($order->shipping_address)->name;
                $email = json_decode($order->shipping_address)->email;
            }


            $orderDetails = OrderDetail::where('order_id', $order->id)->get();
            $plans = '';
            foreach ($orderDetails as $key => $detail) {

                $purchase = CateringPlanPurchase::find($detail->id);

                if ($purchase != null) {
                    $catering_plan = CateringPlan::find($purchase->catering_plan_id);

                    if ($catering_plan != null) {
                        $plan_name = $catering_plan->name;
                    } else {
                        $plan_name = null;
                    }

                    $card = Card::find($purchase->card_id);

                    if ($card != null) {
                        $card_name = $card->name;
                        $rfid_no = $card->rfid_no;
                    } else {
                        $card_name = null;
                        $rfid_no = null;
                    }
                }

                if ($key == 0) {
                    $plans = ($key + 1) . '. ' . $plan_name . 'RFID No: ' . $rfid_no . ' Card Name: ' . $card_name;
                } else {
                    $plans = $plans . ' - ' . ($key + 1) . '. ' . $plan_name . 'RFID No: ' . $rfid_no . ' Card Name: ' . $card_name;
                }

            }


            $array[] = ['Order Code' => $order->code,
                'Num. of Products' => \App\Models\OrderDetail::where('order_id', $order->id)->count(),
                'Customer' => $customer,
                'Email' => $email,
                'Plans' => $plans,
                'Amount' => single_price($order->grand_total),
                'Payment Status' => $order->payment_status,
                'Order Date' => \Carbon\Carbon::create($order->created_at)->format('d/m/Y')
            ];

//            } else {
//                $array[] = ['Order Code' => $order->code,
//                    'Num. of Products' => \App\Models\OrderDetail::where('order_id', $order->id)->count(),
//                    'Customer' => $customer,
//                    'Email' => $email,
//                    'Amount' => single_price($order->grand_total),
//                    'Payment Status' => $order->payment_status,
//                    'Order Date' => \Carbon\Carbon::create($order->created_at)->format('d/m/Y')
//                ];
//            }

        }


        return collect($array);


    }

    public function headings(): array
    {


        return [
            'Order Code',
            'Num. of Products',
            'Customer',
            'Email',
            'Plans',
            'Amount',
            'Payment Status',
            'Order Date',

        ];


//        return [
//            'Order Code',
//            'Num. of Products',
//            'Customer',
//            'Email',
//            'Amount',
//            'Payment Status',
//            'Order Date',
//
//        ];


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


        if (in_array($cell->getColumn(), ['A'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
