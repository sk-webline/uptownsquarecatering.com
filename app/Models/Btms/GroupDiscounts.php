<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GroupDiscounts extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Group Discounts';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
        });
    }

}
