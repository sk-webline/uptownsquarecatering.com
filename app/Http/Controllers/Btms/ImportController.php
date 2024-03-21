<?php

namespace App\Http\Controllers\Btms;

use App\Brand;
use App\BtmsDiscountGroup;
use App\Category;
use App\Color;
use App\Http\Controllers\Controller;
use App\Mail\ImportDataEmailManager;
use App\Models\Btms\GroupDiscounts;
use App\Models\Btms\ItemCategory;
use App\Models\Btms\ItemColor;
use App\Models\Btms\Items;
use App\Models\Btms\ItemSize;
use App\Models\Btms\VatCodes;
use App\Models\Country;
use App\Product;
use App\ProductStock;
use App\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public $sizes;
    public $colors;
    public $items;
    private $products_code;

    private $eshop_brands;

    public $unique_id;

    /* Errors */
    public $errors = array();


    public function __construct()
    {
        $this->unique_id = date('dmYHis');
    }

    /**
     * @return array
     */
    public function getSizes(): array
    {
        $all_sizes = [];
        foreach (ItemSize::all() as $size) {
          $all_sizes[$size->{'Size Id'}] = [
              'code' => $size->{'Size Code'},
              'name' => $size->{'Size Name'},
          ];
        }
        return $all_sizes;
    }

    /**
     * @return array
     */
    public function getColors(): array
    {
        $all_colors = [];
        foreach (ItemColor::all() as $color) {
            $all_colors[$color->{'Color Id'}] = [
                'code' => $color->{'Color Code'},
                'name' => $color->{'Color Name'},
            ];
        }
        return $all_colors;
    }

    /**
     * @return array
     */
    public function getItems($accounting_data = false)
    {
        $items = Items::getItems();
        if ($accounting_data) return $items;
        $all_items = [];
        foreach ($items as $item) {
            $item_code = strval($item->{'Item Code'});
//            $parent_item_code = strval($item->{'Item Parent Code'});
//            $key = ($item->{'Size Code'} === '0' && $item->{'Color Code'} === '0') ? $item_code : $family_code;
            $family_code = strval($item->{'Family Code'});
            $key = $family_code != 'ZZZZ' ? $family_code : $item_code;
            $item_prices = $item->price(18, true, false);

            $all_items[$key][$item_code] = [
                'item_code' => $item_code,
                'family_code' => $family_code,
                'name' => $item->{'SKU Short Name'},
                'color_id' => $item->{'Color Id'},
                'color_code' => $item->{'Color Code'},
                'color_name' => $item->{'Color Name'},
                'size_id' => $item->{'Size Id'},
                'size_code' => $item->{'Size Code'},
                'size_name' => $item->{'Size Name'},
                'vat_code' => $item->{'VAT Code'},
                'item_code_old' => $item->{'Item Code Old'},
                'weight' => $item->WeightInKilos ?? 0,
                'retail_price' => $item_prices->retail,
                'wholesale_price' => $item_prices->wholesale,
                'clearance_price' => $item_prices->clearance,
                'discount_amount' => ($item_prices->clearance != null ? $item_prices->retail - $item_prices->clearance : 0),
                'brand_code' => $item->{'Category Code 1'},
                'category' => $item->{'Category Code 2'},
                'subcategory' => $item->{'Category Code 3'},
                'stock' => $item->stock()
            ];

        }
        return $all_items;
    }

    public function import_data() {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 7200);

        $this->updateVatOnCountries();

        /* Import Colors */
        if (empty($this->colors)) $this->colors = $this->getColors();
        if (!empty($this->colors)) {
            foreach ($this->colors as $color_id => $color) {
                $row_color = Color::where('accounting_id', $color_id)->first();
                if ($row_color == null) {
                    $row_color = new Color;
                    $row_color->accounting_id = $color_id;
                }
                $row_color->accounting_code = $color['code'];
                $row_color->name = $color['name'];
                $row_color->save();

                $this->colors[$color_id]['db_id'] = $row_color->id;
            }
        }
        /* Import Sizes */
        if (empty($this->sizes)) $this->sizes = $this->getSizes();
        if (!empty($this->sizes)) {
            foreach ($this->sizes as $size_id => $size) {
                $row_size = Size::where('btms_size_id', $size_id)->first();
                if ($row_size == null) {
                  $row_size = new Size;
                  $row_size->btms_size_id = $size_id;
                }
                $row_size->btms_size_code = $size['code'];
                $row_size->btms_size_name = $size['name'];
                $row_size->save();

                $this->sizes[$size_id]['db_id'] = $row_size->id;
            }
        }

        /* Import Group Discounts */
        /*$groupDiscounts = GroupDiscounts::all();
        if (!empty($groupDiscounts)) {
            foreach ($groupDiscounts as $group_discount) {
                BtmsDiscountGroup::updateOrCreate(
                    ['email' =>  request('email')],
                    ['name' => request('name')]
                );

                $row_color = Color::where('accounting_id', $color_id)->first();
                if ($row_color == null) {
                    $row_color = new Color;
                    $row_color->accounting_id = $color_id;
                }
                $row_color->accounting_code = $color['code'];
                $row_color->name = $color['name'];
                $row_color->save();

                $this->colors[$color_id]['db_id'] = $row_color->id;
            }
        }*/

        /* Eshop Brands */
        $get_eshop_brands = Brand::whereNotNull('accounting_code')->get();
        if (!empty($get_eshop_brands)) {
          foreach ($get_eshop_brands as $get_eshop_brand) {
            $this->eshop_brands[$get_eshop_brand->accounting_code] = $get_eshop_brand->id;
          }
        }


        /* Import Products */
        $this->items = $this->getItems();
        if (count($this->items) > 0) {
            $fp = fopen('Cronjob_'.$this->unique_id.'.log','a');

            $counter_cron = 0;
            $categories_without_for_sale = []; // Oi katigories pou tha prostethoun se auto to array exoyn proionta poy prostethikan apo to btms alla einai categories not for sale
            /* PARENT PRODUCT */
            foreach ($this->items as $parent_item_code => $items) {
                $find_error = false;

                if (empty($items)) {
                    $this->errors['Products'][$parent_item_code][] = "Not found products in parent product";
                    $find_error = true;
                }
                if ($find_error) continue;

                $product = Product::where('part_number', $parent_item_code)->first();
                $item_codes = array_keys($items);

                if ($product == null) {
                    $product = new Product;

                    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $items[$item_codes[0]]['name']));
                    if (Product::where('slug', $slug)->where('part_number', '!=', $parent_item_code)->count() > 0) {
                        $slug .= '-' . Str::random(5);
                    }
                    $product->tags = implode(',', explode(' ', $items[$item_codes[0]]['name']));
                    $product->slug = strtolower($slug);
                    $product->part_number = $parent_item_code;
                    $product->published = 0;
                    $product->discount_type = 'amount';
                    $product->save();
                }
                $product->old_item_code = $items[$item_codes[0]]['item_code_old'];
                /*$product_category = $items[$item_codes[0]]['category'];
                if ($product_category != '999') {
                    $category = Category::where('btms_category_level', '2')->where('btms_category_code', $product_category)->first();
//                    dd($items[$item_codes[0]], $category->first(), $category->toSql(), $category->getBindings());
                    if ($category != null) {
                        if  ($category->for_sale == 0) $categories_without_for_sale[$category->id] = $category->name;
                        $product->category_id = $category->id;
                        $product->save();
                    }
                }*/
                /*if ($parent_item_code == "B17-AT101-W6-1L" || $parent_item_code == "B17-AT101-W6") {
                    dd($parent_item_code, $product);
                }*/
                $product_subcategory = $items[$item_codes[0]]['subcategory'];
                if ($product_subcategory != '999') {
                    $subcategory = Category::where('btms_category_level', '3')->where('btms_category_code', $product_subcategory)->first();
                    if ($subcategory != null) {
                        if  ($subcategory->for_sale == 0) $categories_without_for_sale[$subcategory->id] = $subcategory->name;
                        $product->subcategory_id = $subcategory->id;
                        $product->save();
                    }
                }

                $product_colors = [];
                $product_sizes = [];
                $current_item_name = '';
                // Product Stocks
                $this->products_code[] = $parent_item_code;
                foreach ($items as $item_number => $item) {
                    echo "<br>$item_number: {$item['name']}";
                    fwrite($fp, "$counter_cron updateProducts(): $parent_item_code  $item_number 1" . PHP_EOL);
                    $find_error = false;

                    /* Start Validation Product */
                    if (empty($item['name'])) {
                        $this->errors['Products'][$item_number][] = "Not found name";
                        $find_error = true;
                    }
                    if (empty($item_number)) {
                        $this->errors['Products'][$item_number][] = "Not found item code";
                        $find_error = true;
                    }
                    if (empty($item['retail_price'])) {
                        $this->errors['Products'][$item_number][] = "Retail price is empty";
                        $find_error = true;
                    }
                    if ($find_error) continue;
                    /* End Validation Product */

                    $variants = [];
                    if (!empty($item['color_code']) && $item['color_code'] != '0') {
                        $product_colors[] =$this->colors[$item['color_id']]['db_id'];
                        $variants[] = $this->colors[$item['color_id']]['db_id'];
                    }

                    if (!empty($item['size_code']) && $item['size_code'] != '0') {
                        $product_sizes[] = $item['size_id'];
                        $variants[] = $item['size_id'];
                    }

                    $variant_str = empty($variants) ? null : implode('-', $variants);
                    $product_stock = ProductStock::where('product_id', $product->id)->where('variant', $variant_str)->where('part_number', $item_number)->first();
                    if ($product_stock == null) {
                        $product_stock = new ProductStock;
                        $product_stock->product_id = $product->id;
                        $product_stock->sku = $item['family_code'];
                        $product_stock->part_number = $item['item_code'];
                    }
                    $product_stock->old_item_code = $item['item_code_old'];

                    $product_stock->size = $item['size_code'] == '0' ? null : $item['size_name'];
                    $product_stock->color = $item['color_code'] == '0' ? null : $item['color_name'];
                    $product_stock->weight = $item['weight'];
                    $product_stock->variant = $variant_str;
                    $product_stock->price = $item['retail_price'];
                    $product_stock->whole_price = $item['wholesale_price'];
                    $product_stock->clearance_price = $item['clearance_price'];
                    $product_stock->qty = $item['stock'];
                    $product_stock->image = null;
                    $product_stock->save();
                    $product->current_stock = $item['stock'];

                    $current_item_name = $item['name'];
                } // End of the foreach for the items => product_stocks
                $product_colors = array_unique($product_colors);
                if (empty($product_colors)) {
                    $product->colors = '[]';
                }
                else {
                    $product->colors = json_encode($product_colors);
                }
                $product_sizes = array_unique($product_sizes);
                $product_sizes = array_values($product_sizes);

                if (empty($product_sizes)) {
                    $product->attributes = '[]';
                    $product->choice_options = '[]';
                }
                else {
                    $product->attributes = '["1"]';
                    $product->choice_options = json_encode(array(array("attribute_id" => "1", "values" => $product_sizes)));
                }

                if (!empty($product_colors) || !empty($product_sizes)) {
                    $product->variant_product = 1;
                }
                else {
                    $product->variant_product = 0;
                }

                $product->name = $current_item_name; // prepei na pernw to product name apo ta variants
                $product->added_by = "admin";
                $product->user_id = "9";
                $product->unit = 'pc';
                // Το πεδίο της τιμής είναι υποχρεωτικό
                $product->unit_price = !empty($item['retail_price']) ? $item['retail_price'] : 0;
//                $product->wholesale_price = !empty($item['wholesale_price']) ? $item['wholesale_price'] : $product->unit_price;
                $product->wholesale_price = $item['wholesale_price'];
                $product->clearance_price = $item['clearance_price'];
                $product->discount = $item['discount_amount'] ?? 0;
                $product->discount_type = 'amount';
                $product->purchase_price = 0;
                $product->import_from_btms = 1;
                $product->disable_date = null;
                if  ($item['brand_code'] != null) {
                  $product->brand_id = $this->eshop_brands[$item['brand_code']] ?? null;
                }
                $product->save();
                fwrite($fp, "$counter_cron updateProducts(): " . $item['item_code'] . " 2" . PHP_EOL);

                $counter_cron++;
//                dd('Complete!', $parent_item_code, $item_number, $this->items);
            }

            if (!empty($categories_without_for_sale)) $this->errors['Not_For_Sale_Categories'] = $categories_without_for_sale;

            if (!empty($this->errors)) {
                try {
                    Mail::to(config('app.email_report_import_data'))->queue(new ImportDataEmailManager(array(
                        'view' => "import_product.import_products",
                        'from' => env('MAIL_USERNAME'),
                        'subject' => "Import data report " . date('d/m/Y H:i'),
                        'errors' => $this->errors
                    )));
                    mail('skweblinetest@gmail.com', "Uptown: End Cronjob ".$this->unique_id, "Date ".date('Y-m-d H:i:s'));
                }
                catch(\Exception $exception) {
//                    mail('skweblinetest@gmail.com', "Uptown Cronjob - Problem with report ".$this->unique_id.date("d.m.Y H:i:s"), $exception->getMessage());
                }
            }
            Product::whereNotIn('part_number', $this->products_code)->where('import_from_btms', '1')->whereNull('disable_date')->update([
                'published' => 0,
                'disable_date' => now()
            ]);
          Product::where('import_from_btms', '1')->where('published', '1')->whereNotNull('disable_date')->update([
            'published' => 0
          ]);
          Redis::flushDB();
//            dd("Complete", $this->items, 'Errors', $this->errors);
        }
    }

    private function updateVatOnCountries():void
    {
        $getActiveVatCodes = Country::select('btms_vat_code', 'vat_percentage')
          ->whereNotNull('btms_vat_code')
          ->groupBy('btms_vat_code')
          ->get();

        if (!empty($getActiveVatCodes))
        {
          foreach ($getActiveVatCodes as $vat_code_row)
          {
            $vat_code = $vat_code_row->btms_vat_code;
            $vat_perc = (int) $vat_code_row->vat_percentage;
            $btms_vat_code = VatCodes::getVatCodeFromCode($vat_code);
            if ($btms_vat_code == null) continue;
            $btms_vat_perc = (int) $btms_vat_code->Percentage;

            if ($vat_perc !== $btms_vat_perc) {
              Country::where('btms_vat_code', $vat_code)->update(['vat_percentage' => $btms_vat_perc]);
            }
          }
        }
    }

    public function export_data($api, $accounting_data = true)
    {
        if  ($accounting_data === 'false') {
            $accounting_data = false;
        }
        else {
            $accounting_data = true;
        }

        switch ($api):
            case "products":
                dd($this->getItems($accounting_data), $this->errors);
                break;
            default:
                dd("Not found API with name '$api'");
                break;
        endswitch;
    }
}
