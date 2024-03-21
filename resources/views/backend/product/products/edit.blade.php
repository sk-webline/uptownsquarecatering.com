@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <h1 class="mb-0 h6">
            @if($product->import_from_btms)
                {{ translate('Edit For Sale Product') }}
            @else
                {{ translate('Edit Not For Sale Product') }}
            @endif
        </h1>
    </div>
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('products.update', $product->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    <input name="_method" type="hidden" value="POST">
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf
                    <div class="card">
                        <ul class="nav nav-tabs nav-fill border-light">
                            @foreach (\App\Language::all() as $key => $language)
                                <li class="nav-item">
                                    <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('products.admin.edit', ['id'=>$product->id, 'lang'=> $language->code] ) }}">
                                        <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                        <span>{{$language->name}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Product Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="name" placeholder="{{translate('Product Name')}}" value="{{ $product->getTranslation('name', $lang) }}" required>
                                </div>
                            </div>
                            @if ($product->import_from_btms)
                                @php
                                    /*if ($product->variant_product) {
                                        $btms_item  = \App\Models\Btms\Items::where('Family Code', $product->part_number)->first();
                                    } else {*/
                                        $btms_item  = \App\Models\Btms\Items::where('Item Code', $product->part_number)->orWhere('Family Code', $product->part_number)->first();
                                    /*}*/

                                    $btms_category = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('dbo.Item Categories')->where('Company Code', config('btms.company_code'))->whereIn('Level', [2])->where('Category Code', $btms_item->{'Category Code 2'})->first();
//                                    $btms_category = \App\Models\Btms\ItemCategory::where('Category Code', $btms_item->{'Category Code 2'})->first();
                                    $btms_subcategory = \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('dbo.Item Categories')->where('Company Code', config('btms.company_code'))->whereIn('Level', [3])->where('Category Code', $btms_item->{'Category Code 3'})->first();
//                                    $btms_subcategory = \App\Models\Btms\ItemSubCategory::where('Category Code', $btms_item->{'Category Code 3'})->first();



                                    if ($product->category_id == null || $product->category_id == 0) {
                                        $find_category = \App\Category::where('btms_category_level', '2')->where('btms_category_code', $btms_item->{'Category Code 2'})->first();
                                        if ($find_category != null && $find_category->for_sale == 1) {
                                            $product->category_id = $find_category->id;
                                            $product->save();
                                        }

                                    }
                                    if ($product->subcategory_id == null || $product->subcategory_id == 0) {
                                        $find_subcategory = \App\Category::where('btms_category_level', '3')->where('btms_category_code', $btms_item->{'Category Code 3'})->first();
                                        if ($find_subcategory != null && $find_subcategory->for_sale == 1) {
                                            $product->subcategory_id = $find_subcategory->id;
                                            $product->save();
                                        }

                                    }

                                @endphp
                            @endif
                            <div class="form-group row" id="category">
                                <label class="col-lg-3 col-from-label">{{translate('Category')}}</label>
                                <div class="col-lg-8">

                                    <select class="form-control sk-selectpicker" name="category_id" id="category_id" data-selected="{{ $product->subcategory_id ?? $product->category_id }}" data-live-search="true"   <?php /* {{ productHasImported($product->import_from_btms, 'disabled', 'required') }} */?> required>
                                        @foreach ($categories as $category)
                                            @if(!$product->import_from_btms && $category->for_sale) @continue @endif
                                            @if($product->import_from_btms && !$category->for_sale) @continue @endif
                                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }} - {{ $category->for_sale ? 'For Sale' : 'Not For Sale' }}</span></option>
                                            @foreach ($category->childrenCategories as $childCategory)
                                                @include('categories.child_category', ['child_category' => $childCategory, 'show_for_sale_status' => $product->import_from_btms])
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if ($product->import_from_btms)

                                <div class="form-group row" id="btms_category">
                                    <label class="col-lg-3 col-from-label">{{translate('BTMS Category')}}</label>
                                    <div class="col-lg-8">
                                        {{ $btms_category->Name }} {{ $btms_subcategory != null ? '> '.$btms_subcategory->Name : '' }}
                                    </div>
                                </div>
                            @endif
                            <div id="product-subtitle">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{translate('Product Subtitle')}}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="subtitle" placeholder="{{ translate('Product Subtitle') }}" value="{{ $product->getTranslation('subtitle', $lang) }}">
                                    </div>
                                </div>
                            </div>
                            @if ($product->import_from_btms)
                                <input type="hidden" name="product_layout" value="1">
                            @else
                            <div id="product-layout">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{translate('Product Layout')}}</label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-auto">
                                                <label class="sk-switch sk-switch-success mb-0">
                                                    <input type="radio" name="product_layout" value="1" @if($product->product_layout == 1) checked @endif >
                                                    <span></span>
                                                    {{translate('Standard Layout')}}
                                                </label>
                                            </div>
                                            <div class="col-auto">
                                                <label class="sk-switch sk-switch-success mb-0">
                                                    <input type="radio" name="product_layout" value="2" @if($product->product_layout == 2) checked @endif >
                                                    <span></span>
                                                    {{translate('Layout with Bigger Images & Video')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group row" id="brand">
                                <label class="col-lg-3 col-from-label">{{translate('Brand')}}</label>
                                <div class="col-lg-8">
                                    <select class="form-control sk-selectpicker" name="brand_id{{ productHasImported($product->import_from_btms, '_disabled') }}" id="brand_id" data-live-search="true" {{ productHasImported($product->import_from_btms, 'disabled') }}>
                                        <option value="0"></option>
                                        @foreach (\App\Brand::all() as $brand)
                                            <option value="{{ $brand->id }}" @if($product->brand_id == $brand->id) selected @endif>{{ $brand->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                    @if($product->import_from_btms)
                                        <input type="hidden" name="brand_id" value="{{ $brand->id }}" >
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row" id="type">
                                <label class="col-md-3 col-from-label">{{translate('Type')}}</label>
                                <div class="col-md-8">
                                    <select class="form-control sk-selectpicker" name="type_id" id="type_id" data-live-search="true">
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}" @if($product->type_id == $type->id) selected @endif>{{ $type->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Weight (gr)')}}</label>
                                <div class="col-lg-8">
                                    <input type="number" lang="en" class="form-control" name="weight" placeholder="{{ translate('Weight (gr)') }}" value="{{$product->weight}}" min="0" required {{ $product->import_from_btms ? 'readonly' : ''  }}>
                                </div>
                            </div>
                            @if(hasAccessOnContent())
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{translate('Unit')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i> </label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" value="{{$product->getTranslation('unit', $lang)}}" required>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="unit" value="Pc">
                            @endif
                            @if(hasAccessOnContent())
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Minimum Qty')}}</label>
                                <div class="col-lg-8">
                                    <input type="number" lang="en" class="form-control" name="min_qty" value="@if($product->min_qty <= 1){{1}}@else{{$product->min_qty}}@endif" min="1" required>
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="min_qty" value="1">
                            @endif
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Tags')}} <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control sk-tag-input" name="tags[]" id="tags" value="{{ $product->tags }}" placeholder="{{ translate('Type to add a tag') }}" data-role="tagsinput" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Part Number')}} @if($product->import_from_btms)<span class="text-danger">*</span>@endif</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="part_number" placeholder="{{ translate('Part Number') }}" value="{{ $product->part_number }}" @if($product->import_from_btms) required @endif {{ productHasImported($product->import_from_btms, 'readonly') }}>
                                </div>
                            </div>
                            @if(hasAccessOnContent())
                                @php
                                    $pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
                                @endphp
                                @if ($pos_addon != null && $pos_addon->activated == 1)
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-from-label">{{translate('Barcode')}}</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}" value="{{ $product->barcode }}">
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @php
                                $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                            @endphp
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{translate('Refundable')}}</label>
                                    <div class="col-lg-8">
                                        <label class="sk-switch sk-switch-success mb-0" style="margin-top:5px;">
                                            <input type="checkbox" name="refundable" @if ($product->refundable == 1) checked @endif>
                                            <span class="slider round"></span></label>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Images')}}</h5>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}} <small>(1200 x 1200)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="skuploader" data-type="image" data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="photos" value="{{ $product->photos }}" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(1200 x 1200)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="skuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="thumbnail_img" value="{{ $product->thumbnail_img }}" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                                        <label class="col-lg-3 col-from-label">{{translate('Gallery Images')}}</label>
                            <div class="col-lg-8">
                                <div id="photos">
                                    @if(is_array(json_decode($product->photos)))
                                    @foreach (json_decode($product->photos) as $key => $photo)
                                    <div class="col-md-4 col-sm-4 col-xs-6">
                                        <div class="img-upload-preview">
                                            <img loading="lazy"  src="{{ uploaded_asset($photo) }}" alt="" class="img-responsive">
                                                <input type="hidden" name="previous_photos[]" value="{{ $photo }}">
                                                <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div> --}}
                            {{-- <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Thumbnail Image')}} <small>(290x300)</small></label>
                                <div class="col-lg-8">
                                    <div id="thumbnail_img">
                                        @if ($product->thumbnail_img != null)
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <div class="img-upload-preview">
                                                <img loading="lazy"  src="{{ uploaded_asset($product->thumbnail_img) }}" alt="" class="img-responsive">
                                                <input type="hidden" name="previous_thumbnail_img" value="{{ $product->thumbnail_img }}">
                                                <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card" id="product-videos">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Videos')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Video Provider')}}</label>
                                <div class="col-lg-8">
                                    <select class="form-control sk-selectpicker" name="video_provider" id="video_provider">
                                        <option value="youtube" <?php if ($product->video_provider == 'youtube') echo "selected"; ?> >{{translate('Youtube')}}</option>
                                        <?php /*<option value="dailymotion" <?php if ($product->video_provider == 'dailymotion') echo "selected"; ?> >{{translate('Dailymotion')}}</option>
                                        <option value="vimeo" <?php if ($product->video_provider == 'vimeo') echo "selected"; ?> >{{translate('Vimeo')}}</option>*/ ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Video Link')}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="video_link" value="{{ $product->video_link }}" placeholder="{{ translate('Video Link') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($product->import_from_btms && (count(json_decode($product->choice_options)) > 0 || count(json_decode($product->colors)) > 0))
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Variation')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row @if($product->import_from_btms) d-none @endif">
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" value="{{translate('Colors')}}" disabled>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control sk-selectpicker" data-live-search="true" data-selected-text-format="count" name="colors{{ productHasImported($product->import_from_btms, '_disabled') }}[]" id="colors" multiple {{ productHasImported($product->import_from_btms, 'disabled') }}>
                                        @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                            <option
                                                    value="{{ $color->id }}"
                                                    data-content="<span>@if(empty($color->image))<span class='size-30px d-inline-block mr-2 rounded border' style='background:{{ $color->code }};vertical-align: middle;'></span>@else<span class='size-30px d-inline-block mr-2 rounded border' style='background:url({{ uploaded_asset($color->image) }}) no-repeat fixed center;background-size:30px;vertical-align: middle;'></span>@endif{{ $color->name }} @if ($color->accounting_id != null) (BTMS) @endif</span>"
                                            <?php if (in_array($color->id, json_decode($product->colors))) echo 'selected' ?>
                                            ></option>
                                        @endforeach
                                    </select>
                                    @if($product->import_from_btms)
                                        @foreach (json_decode($product->colors) as $key => $pcolor)
                                            <input type="hidden" name="colors[]" value="{{ $pcolor }}">
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-lg-1">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input value="1" type="checkbox" name="colors_active" <?php if (count(json_decode($product->colors)) > 0) echo "checked"; ?>  {{ productHasImported($product->import_from_btms, 'disabled') }}>
                                        @if($product->import_from_btms)
                                            <input type="hidden" name="colors_active" value="{{ (count(json_decode($product->colors)) > 0) ? 1 : 0 }}">
                                        @endif
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            @php
                                $product_colors = \App\Color::whereIn('id', json_decode($product->colors, true))->orderBy('name', 'asc')->get();
                            @endphp
                            @if (count($product_colors) > 0)
                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" value="{{translate('Colors')}}" disabled>
                                </div>
                                <div class="col-lg-8">
                                    @foreach($product_colors as $product_color)
                                        <div>
                                            <span>@if(empty($product_color->image))<span class='size-30px d-inline-block mr-2 rounded border' style='background:{{ $product_color->code }};vertical-align: middle;'></span>@else<span class='size-30px d-inline-block mr-2 rounded border' style='background:url({{ uploaded_asset($product_color->image) }}) no-repeat fixed center;background-size:30px;vertical-align: middle;'></span>@endif
                                                <a class="" target="_blank"
                                               href="{{ route('colors.edit', ['id' => $product_color->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                               title="{{ translate('Edit') }} Color {{ $product_color->name }}">
                                                <b>Name:</b> {{ $product_color->name }}  <b>ID:</b> {{ $product_color->id }}  <b>BTMS Code:</b> {{ $product_color->accounting_code }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="form-group row d-none">
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" value="{{translate('Attributes')}}" disabled>
                                </div>
                                <div class="col-lg-8">
                                    <select name="choice_attributes{{ productHasImported($product->import_from_btms, '_disabled') }}[]" id="choice_attributes" data-selected-text-format="count" data-live-search="true" class="form-control sk-selectpicker" multiple data-placeholder="{{ translate('Choose Attributes') }}" {{ productHasImported($product->import_from_btms, 'disabled') }}>
                                        @foreach (\App\Attribute::all() as $key => $attribute)
                                            <option value="{{ $attribute->id }}" @if($product->attributes != null && in_array($attribute->id, json_decode($product->attributes, true))) selected @endif>{{ $attribute->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="">
{{--                                <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}</p>--}}
                                <br>
                            </div>
                            @if(count(json_decode($product->choice_options)) > 0)
                                <div class="customer_choice_options" id="customer_choice_options">
                                    @foreach (json_decode($product->choice_options) as $key => $choice_option)
                                        <div class="form-group row">
                                            <div class="col-lg-3">
                                                <input type="hidden" name="choice_no[]" value="{{ $choice_option->attribute_id }}">
                                                <input type="text" class="form-control" name="choice[]{{ productHasImported($product->import_from_btms, '_disabled') }}" value="{{ \App\Attribute::find($choice_option->attribute_id)->getTranslation('name') }}" placeholder="{{ translate('Choice Title') }}" disabled>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control sk-tag-input" name="choice_options_{{ $choice_option->attribute_id }}{{ productHasImported($product->import_from_btms, '_disabled') }}[]" placeholder="{{ translate('Enter choice values') }}" value="
                                                @if($product->import_from_btms && $choice_option->attribute_id == '1')
    {{--                                                @dd($choice_option->values)--}}
    {{--                                                @foreach($choice_option->values as $key => $size_id)--}}
    {{--                                                {{ getSizeName($size_id) }}@if(count($choice_option->values) < $key+1){{", "}} @endif--}}
    {{--                                                @endforeach--}}

                                                    {{ implode(',', getSizeName($choice_option->values)) }}

                                                @else {{ implode(',', $choice_option->values) }} @endif" data-on-change="update_sku" {{ productHasImported($product->import_from_btms, 'disabled') }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product price + stock')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Unit price')}} <small>{{translate('(without VAT)')}}</small></label>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="{{translate('Unit price')}}" name="unit_price" class="form-control" value="{{$product->unit_price}}" {{ productHasImported($product->import_from_btms, 'disabled', 'required') }}>
                                </div>
                            </div>
                            @if(hasAccessOnContent())
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Wholesale price')}} <small>{{translate('(without VAT)')}}</small></label>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="{{translate('Wholesale price')}}" name="wholesale_price" class="form-control" value="{{$product->wholesale_price}}" {{ productHasImported($product->import_from_btms, 'disabled', 'required') }}>
                                </div>
                            </div>
                            <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{translate('Purchase price')}}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Purchase price')}}" name="purchase_price" class="form-control" value="{{$product->purchase_price}}" required>
                                    </div>
                                </div>
                            @endif
                        <!--                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Tax')}}</label>
                            <div class="col-lg-6">
                                <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('tax')}}" name="tax" class="form-control" value="{{$product->tax}}" required>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control sk-selectpicker" name="tax_type" required>
                                    <option value="amount" <?php if ($product->tax_type == 'amount') echo "selected"; ?> >{{translate('Flat')}}</option>
                                    <option value="percent" <?php if ($product->tax_type == 'percent') echo "selected"; ?> >{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>-->
                            @if(hasAccessOnContent())
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Discount')}}</label>
                                <div class="col-lg-6">
                                    <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Discount')}}" name="discount" class="form-control" value="{{ $product->discount ?? 0 }}" {{ productHasImported($product->import_from_btms, 'disabled', 'required') }}>
                                </div>
                                <div class="col-lg-3">
                                    <select class="form-control sk-selectpicker" name="discount_type" {{ productHasImported($product->import_from_btms, 'disabled', 'required') }}>
                                        <option value="amount" <?php if ($product->discount_type == 'amount') echo "selected"; ?> >{{translate('Flat')}}</option>
                                        <option value="percent" <?php if ($product->discount_type == 'percent') echo "selected"; ?> >{{translate('Percent')}}</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            @if($product->import_from_btms)
                            <div class="form-group row {{ ($product->import_from_btms && $product->variant_product) ? 'd-none' : ''  }}" id="quantity" >
                                <label class="col-lg-3 col-from-label">{{translate('Quantity')}}</label>
                                <div class="col-lg-6">
                                    <input type="number" lang="en" value="{{ $product->current_stock }}" step="1" placeholder="{{translate('Quantity')}}" name="current_stock" class="form-control" {{ productHasImported($product->import_from_btms, 'disabled', 'required') }}>
                                </div>
                            </div>
                            <br>
                            <div class="sku_combination" id="sku_combination">

                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row" id="short-description">
                                <label class="col-md-3 col-from-label">{{translate('Short Description')}}</label>
                                <div class="col-md-9">
                                    <textarea name="short_description" class="form-control" placeholder="{{translate('Short Description')}}">{{ $product->getTranslation('short_description', $lang) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Description')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                                <div class="col-lg-9">
                                    <textarea class="sk-text-editor" name="description">{{ $product->getTranslation('description', $lang) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('PDF Specification')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('PDF Specification')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="skuploader">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="pdf" value="{{ $product->pdf }}" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('SEO Meta Tags')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Meta Title')}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}" placeholder="{{translate('Meta Title')}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Description')}}</label>
                                <div class="col-lg-8">
                                    <textarea name="meta_description" rows="8" class="form-control">{{ $product->meta_description }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Meta Images')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="skuploader" data-type="image" data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="meta_img" value="{{ $product->meta_img }}" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Slug')}}</label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug" value="{{ $product->slug }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6" class="dropdown-toggle" data-toggle="collapse" data-target="#collapse_2">
                                {{translate('Shipping Configuration')}}
                            </h5>
                        </div>
                        <div class="card-body collapse show" id="collapse_2">
                            @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
                                <div class="form-group row">
                                    <label class="col-lg-6 col-from-label">{{translate('Free Shipping')}}</label>
                                    <div class="col-lg-6">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="free" @if($product->shipping_type == 'free') checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-6 col-from-label">{{translate('Flat Rate')}}</label>
                                    <div class="col-lg-6">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="flat_rate" @if($product->shipping_type == 'flat_rate') checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flat_rate_shipping_div" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-lg-6 col-from-label">{{translate('Shipping cost')}}</label>
                                        <div class="col-lg-6">
                                            <input type="number" lang="en" min="0" value="{{ $product->shipping_cost }}" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{translate('Product Wise Shipping')}}</label>
                                    <div class="col-md-6">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="product_wise" @if($product->shipping_type == 'product_wise') checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="product_wise_shipping_div" style="display: none">
                                    @foreach(\App\City::all() as $city)
                                        <div class="form-group row">
                                            <label class="col-md-6 col-from-label">
                                                {{translate($city->name)}}
                                            </label>
                                            @php
                                                $shipping_cost = 0;
                                                if(is_array(json_decode($product->shipping_cost, true))) {
                                                    foreach(json_decode($product->shipping_cost, true) as $key => $val){
                                                        if($city->name == $key) {
                                                            $shipping_cost = $val;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" min="0" value="{{ $shipping_cost }}" step="1" name="shipping_cost[{{ $city->name }}]" placeholder="{{ translate('Cost') }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{translate('Is Product Quantity Mulitiply')}}</label>
                                    <div class="col-md-6">
                                        <label class="sk-switch sk-switch-success mb-0">
                                            <input type="checkbox" name="is_quantity_multiplied" value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Product wise shipping cost is disable. Shipping cost is configured from here') }}
                                    <a href="{{route('shipping_configuration.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                        <span class="sk-side-nav-text">{{translate('Shipping Configuration')}}</span>
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(hasAccessOnContent())
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{translate('Low Stock Quantity Warning')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="name">
                                        {{translate('Quantity')}}
                                    </label>
                                    <input type="number" name="low_stock_quantity" value="{{ $product->low_stock_quantity }}" min="0" step="1" class="form-control">
                                </div>
                            </div>
                        </div>
                    @else
                            <input type="hidden" name="low_stock_quantity" value="1">
                    @endif
                    @if(hasAccessOnContent())
                        <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{translate('Stock Visibility State')}}
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Show Stock Quantity')}}</label>
                                <div class="col-md-6">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="quantity" @if($product->stock_visibility_state == 'quantity') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Show Stock With Text Only')}}</label>
                                <div class="col-md-6">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="text" @if($product->stock_visibility_state == 'text') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Hide Stock')}}</label>
                                <div class="col-md-6">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="hide" @if($product->stock_visibility_state == 'hide') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    @else
                        <input type="hidden" name="stock_visibility_state" value="quantity">
                    @endif
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Cash On Delivery')}}</h5>
                        </div>
                        <div class="card-body">
                            @if (get_setting('cash_payment') == '1')
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                            <div class="col-md-6">
                                                <label class="sk-switch sk-switch-success mb-0">
                                                    <input type="checkbox" name="cash_on_delivery" value="1" @if($product->cash_on_delivery == 1) checked @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Cash On Delivery option is disabled. Activate this feature from here') }}
                                    <a href="{{route('activation.index')}}" class="sk-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                        <span class="sk-side-nav-text">{{translate('Cash Payment Activation')}}</span>
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="cash_on_delivery" value="1">
                    @endif
                    @if ($product->import_from_btms)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Outlet')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="outlet" value="1" @if($product->outlet == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Used Product')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="used" value="1" @if($product->used == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Featured')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="featured" value="1" @if($product->featured == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($product->import_from_btms)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Cyprus Shipping Only')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="cyprus_shipping_only" value="1" @if($product->cyprus_shipping_only == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Published')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="published" value="1" @if($product->published == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Todays Deal')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="checkbox" name="todays_deal" value="1" @if($product->todays_deal == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Flash Deal')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Add To Flash')}}
                                </label>
                                <select class="form-control sk-selectpicker" name="flash_deal_id" id="video_provider">
                                    <option value="">Choose Flash Title</option>
                                    @foreach(\App\FlashDeal::where("status", 1)->get() as $flash_deal)
                                        <option value="{{ $flash_deal->id}}" @if($product->flash_deal_product && $product->flash_deal_product->flash_deal_id == $flash_deal->id) selected @endif>
                                            {{ $flash_deal->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Discount')}}
                                </label>
                                <input type="number" name="flash_discount" value="{{$product->flash_deal_product ? $product->flash_deal_product->discount : '0'}}" min="0" step="1" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Discount Type')}}
                                </label>
                                <select class="form-control sk-selectpicker" name="flash_discount_type" id="">
                                    <option value="">Choose Discount Type</option>
                                    <option value="amount" @if($product->flash_deal_product && $product->flash_deal_product->discount_type == 'amount') selected @endif>
                                        {{translate('Flat')}}
                                    </option>
                                    <option value="percent" @if($product->flash_deal_product && $product->flash_deal_product->discount_type == 'percent') selected @endif>
                                        {{translate('Percent')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Estimate Shipping Time')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Shipping Days')}}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="est_shipping_days" value="{{ $product->est_shipping_days }}" min="1" step="1" placeholder="Shipping Days">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(hasAccessOnContent())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
                        </div>
                        <div class="card-body">
                            @foreach(\App\Tax::where('tax_status', 1)->get() as $tax)
                                <label for="name">
                                    {{$tax->name}}
                                    <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                                </label>

                                @php
                                    $tax_amount = 0;
                                    $tax_type = '';
                                    foreach($tax->product_taxes as $row) {
                                        if($product->id == $row->product_id) {
                                            $tax_amount = $row->tax;
                                            $tax_type = $row->tax_type;
                                        }
                                    }
                                @endphp

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="number" lang="en" min="0" value="{{ $tax_amount }}" step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <select class="form-control sk-selectpicker" name="tax_type[]">
                                            <option value="amount" @if($tax_type == 'amount') selected @endif>
                                                {{translate('Flat')}}
                                            </option>
                                            <option value="percent" @if($tax_type == 'percent') selected @endif>
                                                {{translate('Percent')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-12">
                    <div class="mb-3 text-right">
                        <button type="submit" name="button" class="btn btn-info">{{ translate('Update Product') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
      showSubtitle();
      showVideos();
      function showSubtitle(){
        $.post('{{ route('product.showSubtitle') }}', {_token:'{{ csrf_token() }}', category:$('#category_id').val()}, function(data){
          if(data == 1 || data == 2){
            $('#product-subtitle').hide();
            $('#product-layout').hide();
            $('#short-description').hide();
            $('input[name="product_layout"][value="1"]').prop('checked', true);
          }
          else{
            $('#product-subtitle').show();
            showLayout();
            showShortDescription();
          }
          showVideos();
        });
      }

      $('input[name="product_layout"]').on("change", function (){
        showVideos();
          $('#short-description').hide();
          $.post('{{ route('product.showSubtitle') }}', {_token:'{{ csrf_token() }}', category:$('#category_id').val()}, function(data){
              if(data == 0){
                  showShortDescription();
              }
          });
      });

      $('input[name="used"]').on("change", function (){
          showSubtitle();
      });

      function showVideos(){
        if($('input[name="product_layout"]:checked').val() == 2 && !$('input[name="used"]').prop('checked')) {
          $('#product-videos').show();
        } else {
          $('#product-videos').hide();
        }
      }

      function showShortDescription(){
          if($('input[name="product_layout"]:checked').val() == 2 && !$('input[name="used"]').prop('checked')) {
              $('#short-description').show();
          } else {
              $('#short-description').hide();
          }
      }

      function showLayout(){
          if(!$('input[name="used"]').prop('checked')) {
              $('#product-layout').show();
          } else {
              $('#product-layout').hide();
          }
      }

      $("#category_id").on("change", function (){
        showSubtitle();
      });

      $(document).ready(function (){
        show_hide_shipping_div();
      });

      $("[name=shipping_type]").on("change", function (){
        show_hide_shipping_div();
      });

      function show_hide_shipping_div() {
        var shipping_val = $("[name=shipping_type]:checked").val();

        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if(shipping_val == 'product_wise'){
          $(".product_wise_shipping_div").show();
        }
        if(shipping_val == 'flat_rate'){
          $(".flat_rate_shipping_div").show();
        }
      }

      function add_more_customer_choice_option(i, name){

        $('#customer_choice_options').append('<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control sk-tag-input" name="choice_options_'+i+'[]" placeholder="{{ translate('Enter choice values') }}" data-on-change="update_sku"></div></div>');

        SK.plugins.tagify();
      }

      $('input[name="colors_active"]').on('change', function() {
        if(!$('input[name="colors_active"]').is(':checked')){
          $('#colors').prop('disabled', true);
          SK.plugins.bootstrapSelect('refresh');
        }
        else{
          $('#colors').prop('disabled', false);
          SK.plugins.bootstrapSelect('refresh');
        }
        update_sku();
      });

      $('#colors').on('change', function() {
        update_sku();
      });

      function delete_row(em){
        $(em).closest('.form-group').remove();
        update_sku();
      }

      function delete_variant(em){
        $(em).closest('.variant').remove();
      }

      function update_sku(){
        $.ajax({
          type:"POST",
          url:'{{ route('products.sku_combination_edit') }}',
          data:$('#choice_form').serialize(),
          success: function(data){
            $('#sku_combination').html(data);
            SK.uploader.previewGenerate();
            SK.plugins.fooTable();
            if (data.length > 1) {
              $('#quantity').hide();
            }
            else {
              $('#quantity').show();
            }
          }
        });
      }

      SK.plugins.tagify();

      $(document).ready(function(){
        update_sku();

        $('.remove-files').on('click', function(){
          $(this).parents(".col-md-4").remove();
        });
      });

      $('#choice_attributes').on('change', function() {
        $.each($("#choice_attributes option:selected"), function(j, attribute){
          flag = false;
          $('input[name="choice_no[]"]').each(function(i, choice_no) {
            if($(attribute).val() == $(choice_no).val()){
              flag = true;
            }
          });
          if(!flag){
            add_more_customer_choice_option($(attribute).val(), $(attribute).text());
          }
        });

        var str = @php echo $product->attributes @endphp;

        $.each(str, function(index, value){
          flag = false;
          $.each($("#choice_attributes option:selected"), function(j, attribute){
            if(value == $(attribute).val()){
              flag = true;
            }
          });
          if(!flag){
            $('input[name="choice_no[]"][value="'+value+'"]').parent().parent().remove();
          }
        });

        update_sku();
      });

    </script>
@endsection
