<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemSize extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.ItemSizes';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
        });
    }

}
