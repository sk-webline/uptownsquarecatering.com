<?php
namespace App;

use App\Models\Card;
use App\Models\CateringPlan;
use App\Models\Organisation;
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



class OrdersRfidFilteredExport extends DefaultValueBinder implements FromCollection,  WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithCustomValueBinder
{

    use Exportable;

    public function __construct(object $orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {

        $array = [];

//        dd( Card::whereNotIn('id', $this->orders ))

        foreach ($this->orders as $order){

            if ($order->user_id != null){
                $user = \App\User::find($order->user_id);
                $customer = $user->name;

                $email = $user->email;
            }else{
                $customer =json_decode($order->shipping_address)->name;
                $email = json_decode($order->shipping_address)->email;
            }

            $card = Card::find($order->card_id);

            $organisation = OrganisationSetting::find($order->organisation_settings_id)->organisation;

            if($card==null){

            }else {

                $catering_plan = CateringPlan::find($order->catering_plan_id);

                $array[] = ['Order Code' => $order->code ,
                    'Customer' => $customer,
                    'Email' => $email,
                    'Organisation' => $organisation->name,
                    'Card Name' => $card->name,
                    'RFID No' => $card->rfid_no,
                    'Catering Plan Name' => $catering_plan->name,
                    'Amount' => single_price($catering_plan->price),
    //                'Payment Status' => $order->payment_status,
                    'Order Date'=> \Carbon\Carbon::create($order->created_at)->format('d/m/Y')
                ];
            }

        }

//        dd($array);

        return collect($array);


    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Customer',
            'Email',
            'Organisation',
            'Card Name',
            'RFID No',
            'Catering Plan Name',
            'Amount',
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
            1    => ['font' => ['bold' => true]],

        ];
    }

    public function bindValue(Cell $cell, $value)
    {


        if (in_array($cell->getColumn(), ['A', 'F'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
