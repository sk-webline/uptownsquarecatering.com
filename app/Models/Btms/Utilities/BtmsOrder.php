<?php

namespace App\Models\Btms\Utilities;


use App\Models\Btms\Customers;
use App\Models\Btms\OrderHeader;
use App\Models\Btms\OrderLine;
use App\Models\Btms\Items;
use App\Models\Btms\PaymentType;
use App\Models\Btms\PriceType;
use App\Models\Btms\VatCodes;
use App\Order;
use Illuminate\Support\Facades\Log;

class BtmsOrder
{

    public function __construct()
    {

    }

    public static function add($order_id) {

        return true;

        $order = Order::findOrFail($order_id);
        $user = $order->user;
        if ($user != null && $user->partner == 1)
        {
          $btms_account_code = $user->btms_customer_code;
          $btms_customer = Customers::where('Customer Code', $btms_account_code)->first();
          $btms_customer_name = ($btms_customer != null ? $btms_customer->{'Company Name'} : '');
        }
        else {
          $btms_account_code = config('btms.generic_customer_code');
          $btms_customer = Customers::where('Customer Code', $btms_account_code)->first();
          $btms_customer_name = ($btms_customer != null ? $btms_customer->{'Company Name'} : $btms_account_code);
        }
        $order_code = $order->code;

        foreach ($order->orderDetails as $orderDetail) {

            $product = $orderDetail->product;

            if  (!empty($orderDetail->variation)) {

                $product_stock = $orderDetail->product->stocks->where('variant', $orderDetail->variation)->first();
                $item_code = $product_stock->part_number;
            }
            else {
                $item_code = $orderDetail->product->part_number;
            }

            $btms_item = Items::where('Item Code', $item_code)->first();

            $discAmountWithoutVat = $orderDetail->disc_amount;

            $order_line = new OrderLine();
//            $order_line = (object) [];

            $priceGroup = PriceType::where('Price Id', $orderDetail->price_type_id)->first();
            $priceGroupCode = $priceGroup->{'Price Type Code'} ?? '';
//            dd($priceGroup, $orderDetail->price_type_id, $priceGroupCode);

            $order_line->DTHeader_Id = '-1';
            $order_line->ProductCode = $item_code;
            $order_line->ProductDesc = $product->name;
            $order_line->PriceGroupCode = $priceGroupCode;
            $order_line->LindInd = '-1';
            $order_line->LineQty = $orderDetail->quantity;
            $order_line->FreeQty = '0';
            $order_line->UnitPrice = priceFormatForBtms(($orderDetail->price + $orderDetail->disc_amount) / $orderDetail->quantity);
            $order_line->GoodsAmount = priceFormatForBtms($orderDetail->price + $orderDetail->disc_amount);
            $order_line->LineDiscount = priceFormatForBtms($discAmountWithoutVat); // TODO: George Urgent => Θα πρέπει να καταγράφω την έκπτωση που είχε το κάθε προϊόν κατά την παραγγελία στο table order_details χωρίς το VAT
            $order_line->DocDiscount = '0';
            $order_line->VatAmount = priceFormatForBtms($orderDetail->tax);
            $order_line->VatCode = $btms_item->{'VAT Code'};
            $order_line->VatRate = $orderDetail->vat_perc;
            $order_line->DiscType = $orderDetail->disc_perc > 0 ? '1' : '2'; // 1 percentage & 2 fixed amount
            $order_line->DiscValue = $orderDetail->disc_perc > 0 ? $orderDetail->disc_perc : priceFormatForBtms($discAmountWithoutVat);
            $order_line->StoreCode = '01'; // 01 είναι η αποθήκη της Λεμεσού
            $order_line->DTHeader_Id = $order->id;
            $order_line->DateCreated = date("Y-m-d H:i:s");
            $order_line->UserCode =  'Eshop';
            try {
              $order_line->save();
            }
            catch (\Exception $exception) {
              Log::alert('Import Order Line to BTMS for order deatil '.$orderDetail->id.'('.$order->id.'): '.$exception->getMessage());
            }

        }

        // Sending the shipping cost to BTMS
        if ($order->shipping_cost > 0) {
            $btms_shipping_code = config('btms.shipping_service_code');
            $btms_product = Items::where('Item Code', $btms_shipping_code)->first();
            $btms_vat_code = VatCodes::getVatCodeFromCode($btms_product->{'VAT Code'});

            $order_line = new OrderLine();
            $order_line->DTHeader_Id = '-1';
            $order_line->ProductCode = $btms_shipping_code;
            $order_line->ProductDesc = $btms_product->{'SKU Short Name'};
//            $order_line->PriceGroupCode = $priceGroupCode;
            $order_line->LindInd = '-1';
            $order_line->LineQty = 1;
            $order_line->FreeQty = '0';
            $order_line->UnitPrice = priceFormatForBtms($order->shipping_cost);
            $order_line->GoodsAmount = priceFormatForBtms($order->shipping_cost);
            $order_line->LineDiscount = 0;
            $order_line->DocDiscount = '0';
            $order_line->VatAmount = priceFormatForBtms($order->shipping_vat);
            $order_line->VatCode = $btms_product->{'VAT Code'};
            $order_line->VatRate = $btms_vat_code->{'Percentage'};
            $order_line->DiscType = '2';
            $order_line->DiscValue = 0;
            $order_line->StoreCode = '01'; // 01 είναι η αποθήκη της Λεμεσού
            $order_line->DTHeader_Id = $order->id;
            $order_line->DateCreated = date("Y-m-d H:i:s");
            $order_line->UserCode =  'Eshop';
            try {
              $order_line->save();
            }
            catch (\Exception $exception) {
              Log::alert('Import Order Line to BTMS for order deatil (shipping cost) '.$orderDetail->id.'('.$order->id.'): '.$exception->getMessage());
            }
        }

        $order_header = new OrderHeader();
//        $order_header = (object) [];

        $payment_method_id = 0;
        $payment_method_name = '';
        if ($order->payment_type == 'cash_on_delivery')
        {
          $payment_method_id = 1;
        }
        else if ($order->payment_type == 'viva_wallet')
        {
          $payment_method_id = 2;
        }
        else if ($order->payment_type == 'pay_on_credit')
        {
          $payment_method_id = 5;
        }

        if ($payment_method_id != 0 ) {
          $payment_type = PaymentType::where('Payment Type Code', $payment_method_id)->first();
          $payment_method_name = $payment_type->{'Payment Type Name'} ?? '';
        }

        $order_header->CompanyCode          = config('btms.company_code');
        $order_header->TransCode            = 'SO';
        $order_header->DocumentNo           = $order_code;
        $order_header->DocumentDate         = date("Y-m-d H:i:s");
        $order_header->DocumentStatus       = 2;
        $order_header->AccountCode          = $btms_account_code;
        $order_header->AccountName          = $btms_customer_name;
        $order_header->StoreCode            = '01'; // 01 είναι η αποθήκη της Λεμεσού
        $order_header->SalesmanCode         = config('btms.salesperson_code');
        $order_header->PayMathodCode        = $payment_method_name;
        $order_header->CurrencyCode         = 'EUR';
        $order_header->CurrencyRate         = 1;
        $order_header->ExpectedDeliveryDate = date("Y-m-d");
        $order_header->DocumentDetails      = 'Sales Orders';
        $order_header->DTHeader_Id          = $order->id;
        $order_header->DateCreated          = date("Y-m-d H:i:s");
        $order_header->UserCode             = 'Eshop';
        if ($order->vat_percentage == 0) {
          $order_header->VatExceptionCode = $order->vat_btms_code;
        }

        try {
          $order_header->save();
        }
        catch (\Exception $exception) {
          Log::alert('Import Order Header to BTMS for order '.$order->id.': '.$exception->getMessage());
        }
        $order_header->save();
    }
}
