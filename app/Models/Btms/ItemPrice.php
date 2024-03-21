<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Item Prices';

}
