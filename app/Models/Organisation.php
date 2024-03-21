<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasMany\LatestOfMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int $zero_vending_id
 * @property tinyint $catering
 * @property tinyint $top_up
 * @property tinyint $required_field
 * @property tinyint $custom_packets
 * @property int $email_for_order_id
 * @property string $required_field_name
 */

class Organisation extends Model
{

    use SoftDeletes;

    protected $fillable = ['zero_vending_id', 'catering','top_up', 'required_field','required_field_name', 'custom_packets', 'email_for_order_id'];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Catering Settings (Periods)
     * Get the organisation setting of this organisation.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(OrganisationSetting::class);
    }

    /**
     *  Catering Locations
     * Get the organisation setting of this organisation.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(OrganisationLocation::class);
    }

    /**
     * Get the organisation's most recent settings.
     */
    public function currentSettings()
    {

        $today = Carbon::now();
        $settings= OrganisationSetting::where('organisation_id',$this->id)->orderBy('date_from', 'asc')->get();


        foreach ($settings as $setting){

            $start_date = Carbon::create($setting->date_from);
            $end_date = Carbon::create($setting->date_to);
            if($end_date->gte($today) && $today->gte($start_date)){
                return $setting;
            }else if($start_date->gte($today)){
                return $setting;
            }

        }

        return array();

    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }


    /**
     * Get the email for order for this organisation
     */
    public function email_for_order(): BelongsTo
    {
        return $this->belongsTo(EmailForOrder::class);
    }

    /**
     * Get the canteen setting of this organisation.
     */
    public function canteen_settings(): HasMany
    {
        return $this->hasMany(CanteenSetting::class);
    }

    /**
     * Get the canteen locations of this organisation.
     */
    public function canteen_locations(): HasMany
    {
        return $this->hasMany(CanteenLocation::class);
    }

    /**
     * Get the organisation setting of this organisation.
     */
    public function breaks(): HasMany
    {
        return $this->hasMany(OrganisationBreak::class);
    }

    /**
     * Get the organisation's most recent settings.
     */
    public function current_canteen_settings()
    {

        $today = Carbon::now();
        $settings= CanteenSetting::where('organisation_id',$this->id)->orderBy('date_from', 'asc')->get();


        foreach ($settings as $setting){

            $start_date = Carbon::create($setting->date_from);
            $end_date = Carbon::create($setting->date_to);
            if($end_date->gte($today) && $today->gte($start_date)){
                return $setting;
            }else if($start_date->gte($today)){
                return $setting;
            }

        }

        return array();

    }



}
