<?php

namespace App\Models\Gateways;

use Illuminate\Database\Eloquent\Model;

class AppViva extends Model
{

    protected $table = 'app_viva_wallet_logs';

    protected $guarded = [];


    public function saveResponse($status, $response_body)
    {

    }

}
