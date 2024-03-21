<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ItemBrand extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Item Categories';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
            $builder->where('Level', 1);
        });
    }
}
