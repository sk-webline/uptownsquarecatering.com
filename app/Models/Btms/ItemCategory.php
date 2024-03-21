<?php

namespace App\Models\Btms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ItemCategory extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'dbo.Item Categories';

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('companyCode', function (Builder $builder)  {
            $builder->where('Company Code', config('btms.company_code'));
            $builder->whereIn('Level', [2])->whereNull('Related Category Code');
        });
    }

    public function childrenCategories()
    {
        return $this->hasMany(ItemSubCategory::class, 'Related Category Code', 'Category Code')->orderBy('Name');
    }

    public static function getAllCategories(): \Illuminate\Support\Collection
    {
        return DB::connection('sqlsrv')->table('dbo.Item Categories')
            ->select(DB::raw('[Level], [Category Code], [Name]'))
            ->where('Company Code', config('btms.company_code'))
            ->whereNull('Related Category Code')
            ->where('Level', 2)
            ->orderBy('Name')
            ->get();
    }

    public static function getChildrenByCategory($category_code): \Illuminate\Support\Collection
    {
        return Cache::remember('btms_subcategory_'.$category_code, now()->addMinutes(40), function () use ($category_code) {
            return DB::connection('sqlsrv')->table('dbo.Item Categories')
                ->select(DB::raw('[Level], [Category Code], [Name]'))
                ->where('Related Category Code', $category_code)
                ->where('Level', 3)
                ->orderBy('Name')
                ->get();
        });
    }

}
