<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $user_id
 * @property int $organisation_id
 */

class OrganisationCanteenCashier extends Model
{

    use SoftDeletes;

    protected $table = 'organisation_canteen_cashiers';

    protected $fillable = ['organisation', 'user_id'];

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
