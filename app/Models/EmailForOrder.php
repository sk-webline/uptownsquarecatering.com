<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property varchar $email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */

class EmailForOrder extends Model
{
    protected $table = 'email_for_orders';

    /**
     * Get the organisations that have this email for orders
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Organisation::class);
    }


}
