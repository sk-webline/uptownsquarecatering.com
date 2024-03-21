<?php

namespace App\Models\Gateways;

use Illuminate\Database\Eloquent\Model;

class Viva extends Model
{

    protected $table = 'viva_wallet_logs';

    protected $guarded = [];


    public function saveResponse($status, $response_body)
    {

    }

}
