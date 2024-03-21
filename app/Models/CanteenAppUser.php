<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;


/**
 * App\Models\Category
 *
 */

class CanteenAppUser extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $table = 'canteen_app_users';

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function card():BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
