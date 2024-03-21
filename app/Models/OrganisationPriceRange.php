<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $organisation_settings_id
 * @property int $start_range
 * @property int $end_range
 */

class OrganisationPriceRange extends Model
{

    protected $fillable = ['organisation_settings_id', 'start_range','end_range'];

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function organisation_prices(): HasMany
    {
        return $this->hasMany(OrganisationPrice::class);

    }

}
