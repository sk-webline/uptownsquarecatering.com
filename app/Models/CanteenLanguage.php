<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Category
 *
 */

class CanteenLanguage extends Model
{

    protected $table = 'canteen_languages';

    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

    }


}
