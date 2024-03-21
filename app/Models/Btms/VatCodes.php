<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Model;

class VatCodes extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.VAT Codes';

    public static function getVatCodes() {
        return self::where('Company Code', config('btms.company_code'))->orderBy('Percentage')->get();
    }
    public static function getVatCodeFromCode($code) {
        return self::where('Company Code', config('btms.company_code'))->where('VAT Code', $code)->first();
    }
}
