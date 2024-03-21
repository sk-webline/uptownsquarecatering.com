<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ItemStock extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Stock';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
            $builder->where('Warehouse Code', '01');
        });
    }

  public function __get($key)
  {

    if ($key == 'Available') {
      if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201') {
//        if (in_array($this['Item Code'], ['B21-FS102-W6-0L'])) {
//          return 0;
//        }
//        if (in_array($this['Item Code'], ['486719002'])) {
//          return 1;
//        }
      }
    }
    return $this[$key];
  }
}
