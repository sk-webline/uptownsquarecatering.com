<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Payment Types';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
        });
    }
}
