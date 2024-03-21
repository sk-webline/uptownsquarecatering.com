<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Color
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Color whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Color extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('alphabetical', function (Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    public static function getColorOrImage($id, $background=false, $returnType=false):string
    {

        $color_details = self::where('id', $id)->first();
        if (!empty($color_details->image) && in_array($returnType, [false, 'image'])) {
            if ($background) {
                $return_value = 'background-image:url("' . uploaded_asset($color_details->image) . '");background-size: cover;';
            } else {
                $return_value = uploaded_asset($color_details->image);
            }
        } elseif ($returnType == 'image') {
            $return_value = '';
        } elseif ($color_details->code != null && in_array($returnType, [false, 'code'])) {
            $return_value = "background: $color_details->code";
        } elseif ($returnType == 'code') {
            $return_value = '';
        } else {
            if ($background)
                $return_value = "background-image: url('" . uploaded_asset(42687) . "')"; // 40277 is a default test Image
            else
                $return_value = uploaded_asset(42687);
        }
        return $return_value;
    }
}
