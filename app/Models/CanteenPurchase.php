<?php

namespace App\Models;

use App\Models\CanteenProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenPurchase extends Model
{

    use SoftDeletes;

    protected $fillable = ['canteen_app_user_id', 'canteen_setting_id', 'canteen_product_id', 'organisation_break_id', 'date', 'price', 'custom_price_status'];

    public function canteen_product(){
        return $this->belongsTo(CanteenProduct::class);
    }

    public function organisation_break(){
        return $this->belongsTo(OrganisationBreak::class);
    }

    public function canteen_user(){
        return $this->belongsTo(CanteenAppUser::class);
    }

    public function canteen_setting(){
        return $this->belongsTo(CanteenSetting::class);
    }

    public function extractNumberFromMealCode($inputString) {
        // Use a regular expression to match the numeric part at the end of the string
        preg_match('/(\d+)$/', $inputString, $matches);

        // Check if any numeric part is found
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return null; // Return null if no numeric part is found
        }
    }

    public function formatMealCode($number) {
        // Convert the number to an integer
        $number = intval($number);

        // Add leading zeros to ensure at least 4 characters
        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        // Add the 'M' prefix
        $result = 'M' . $formattedNumber;

        return $result;
    }
}
