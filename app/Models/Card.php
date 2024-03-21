<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Card
 *
 * @property int $id
 * @property int $organisation_id
 * @property int $rfid_no
 * @property int $user_id
 */

class Card extends Model
{

    use SoftDeletes;

    protected $fillable = ['organisation_id', 'rfid_no', 'rfid_no_dec','user_id', 'auto_generate'];

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canteen_app_user()
    {
        return $this->hasOne(CanteenAppUser::class);
    }




}
