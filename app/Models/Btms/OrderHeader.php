<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Model;

class OrderHeader extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.AT_ORDER_HEADERS';

    public $timestamps = false;
}
