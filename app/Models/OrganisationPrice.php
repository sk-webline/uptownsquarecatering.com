<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $organisation_price_range_id
 * @property string $type
 * @property int $quantity
 * @property decimal $price
 */

class OrganisationPrice extends Model
{

    protected $fillable = ['organisation_price_range_id', 'type','quantity', 'price'];

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function price_range(): BelongsTo
    {
        return $this->belongsTo(OrganisationPriceRange::class);
    }

}
