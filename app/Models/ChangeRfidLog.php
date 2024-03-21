<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Card
 *
 * @property int $id
 * @property int $user_id
 * @property string $from_rfid_no
 * @property string $from_card_id
 * @property date $to_rfid_no
 * @property date $to_card_id
 */

class ChangeRfidLog extends Model
{

    use SoftDeletes;

    protected $fillable = ['user_id', 'from_rfid_no','from_card_id', 'to_rfid_no','to_card_id'];

    protected static function boot()
    {
        parent::boot();

    }



}
