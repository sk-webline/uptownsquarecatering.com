<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.AT_ORDER_LINES';

    public $timestamps = false;
}
