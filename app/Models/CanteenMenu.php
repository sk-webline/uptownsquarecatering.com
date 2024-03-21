<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $canteen_setting_id
 * @property int $canteen_product_id
 * @property int $organisation_break_id
 * @property tinyint $custom_price_status
 * @property decimal $custom_price
 */

class CanteenMenu extends Model
{

    protected $table = 'canteen_menus';
    protected $fillable = ['canteen_setting_id','canteen_product_id','organisation_break_id', 'organisation_break_num','custom_price_status','custom_price'];

    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Get the organisation that owns these settings.
     */
    public function canteen_setting(): BelongsTo
    {
        return $this->belongsTo(CanteenSetting::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(CanteenProduct::class);
    }

    /**
     * Get the organisation that owns these settings.
     */
    public function break(): BelongsTo
    {
        return $this->belongsTo(OrganisationBreak::class);
    }


}
