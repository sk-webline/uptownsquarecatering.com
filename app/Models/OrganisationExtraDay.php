<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $organisation_settings_id
 * @property datetime $date
 * @property int $created_by
 */

class OrganisationExtraDay extends Model
{

    protected $fillable = ['organisation_settings_id', 'date','created_by'];

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function organisation_setting(): BelongsTo
    {
        return $this->belongsTo(OrganisationSetting::class);
    }

}
