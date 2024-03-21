<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class BusinessSetting extends Model
{

  public static function boot()
  {
    parent::boot();

    self::created(function($model){
      self::deleteCacheKey($model);
    });
    self::updated(function($model){
      self::deleteCacheKey($model);
    });
    self::deleting(function($model){
      self::deleteCacheKey($model);
    });
    self::deleted(function($model){
      self::deleteCacheKey($model);
    });

  }

  /**
   * @param $business_setting
   * @return void
   */
  private static function deleteCacheKey($business_setting) {
    $cache_key = 'business_settings:'.$business_setting->type;
    $redis_keys = Redis::keys($cache_key);
    if ($redis_keys != null) Redis::del($redis_keys);
  }
}
