<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $name
 * @property enum $status
 */
class LoginLog extends Model
{
    protected $fillable = ['ip_address', 'status'];

    protected static function boot()
    {
        parent::boot();

    }
}
