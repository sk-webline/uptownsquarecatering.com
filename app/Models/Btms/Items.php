<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Items extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Items';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
        });
    }

    public static function getItems() {
        /*$items_id = self::select(DB::raw("[Item Parent Code] AS parent_code"))
            ->groupBy('Item Parent Code')
            ->having(DB::raw("COUNT(*)"), '>', 1)
            ->limit(10)
            ->get();*/

//        $all_item_ids = [];
//        foreach ($items_id as $item_id) {
//            $all_item_ids[] = $item_id->parent_code;
//        }

      $all_item_ids = ['18600', 'B17-AT101-W6', 'WEBLINE TEST', 'WEBLINE TEST 2', 'WEBLINE TEST 3', 'WEBLINE TEST 4'];
      /*
       * Κανονικά πρέπει να ελέγχο μόνο ποια προϊόντα είναι exportToEcommerce και έχουν βαρος
       *
       * */
//        return self::whereIn('Item Parent Code', $all_item_ids)->limit('20')->get();
        return self::where(function($query) {
            $query->where('WeightInKilos', '>', 0);
//          $query->where('ExportToEcommerce', 1);
        })->orWhereIn('Item Parent Code', $all_item_ids)->get();
//        return self::where('Family Code', '<>', 'ZZZZ')->whereIn('Item Parent Code', $all_item_ids)->limit('20')->get();
//        return self::where('Family Code', '<>', 'ZZZZ')->get();
    }

    public function barcode()
    {
        $item_barcode = $this->belongsTo(ItemBarcodes::class, 'Item Code','Item Code')->first();
        if  ($item_barcode == null) return null;
        return $item_barcode->{'AS Barcode'};
    }

    public function stock()
    {
        $item_stock = $this->belongsTo(ItemStock::class, 'Item Code','Item Code')->first();
        if  ($item_stock == null) return 0;
        return (int) $item_stock->{'Available'};
    }

    public function getWeightInKilosAttribute($value)
    {
        return ($value > 0) ? number_format($value,'3', '','') : null;
    }

    public function getVatRate():float
    {
        $vat = $this->belongsTo(VatCodes::class, 'VAT Code','VAT Code')->first();
        if ($vat == null) return 0;
        return (float) $vat->Percentage;
    }

    public function price($pricelist_id = 18, $retailAndWholesale = false,  $live = false)
    {
        /* Price Ids
         * Wholesale = 19
         * Retail = 18
         */
        $item_price = DB::connection('sqlsrv')->table('dbo.Item Prices')
            ->select(DB::raw('*'))
            ->where('Item Code', $this->{'Item Code'})
            ->where('Company Code', config('btms.company_code'))
            ->get();
//        $item_price = $this->belongsTo(ItemPrice::class, 'Item Code','Item Code')->where('Company Code', config('btms.company_code'))->get();
        if ($item_price == null) abort(404);

        if  ($live && Auth::check() && Auth::user()->partner) {
            $wholesale_price = $item_price->where('Price Id', '19')->first();
            if($wholesale_price == null) {
                $retail_price = $item_price->where('Price Id', '18')->first();
                if($retail_price == null) abort(404);
                return round($retail_price->{'SellingPrice'}, 2);
            }
            else {
                return round($wholesale_price->{'SellingPrice'}, 2);
            }
        }
        else {
            if ($retailAndWholesale) {
                $retail_price = $item_price->where('Price Id', 18)->first();
                $wholesale_price = $item_price->where('Price Id', 19)->first();
                $clearance_price = $item_price->where('Price Id', 20)->first();
                $final_retail_price = ($retail_price == null) ? null : round($retail_price->{'SellingPrice'}, 2);
                $final_clearance_price = ($clearance_price == null) ? null : round($clearance_price->{'SellingPrice'}, 2);
                return (object) array(
                    'retail' => $final_retail_price,
//                    'wholesale' => ($wholesale_price == null) ? $final_retail_price : ($wholesale_price->{'Include VAT'} ? remove_vat($retail_price->Price, $this->getVatRate()) : $wholesale_price->Price),
                    'wholesale' => ($wholesale_price == null) ? null : round($wholesale_price->{'SellingPrice'}, 2),
                    'clearance' => $final_clearance_price,
                );
            }
            else {
                $retail_price = $item_price->where('Price Id', $pricelist_id)->first();
                return ($retail_price == null) ? null : round($retail_price->{'SellingPrice'}, 2);
            }
        }
    }
}
