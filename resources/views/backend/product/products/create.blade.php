@extends('backend.layouts.app')

@section('content')
<div class="sk-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Not For Sale Product')}}</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-8">
                @csrf
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Product Name')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" placeholder="{{ translate('Product Name') }}" onchange="update_sku()" required>
                            </div>
                        </div>
                        <div class="form-group row" id="category">
                            <label class="col-md-3 col-from-label">{{translate('Category')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control sk-selectpicker" name="category_id" id="category_id" data-live-search="true" required>
                                    @foreach ($categories as $category)
                                        @if($category->for_sale) @continue @endif
                                    <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                    @foreach ($category->childrenCategories as $childCategory)
                                    @include('categories.child_category', ['child_category' => $childCategory, 'show_for_sale_status' => false])
                                    @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="product-subtitle">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Product Subtitle')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="subtitle" placeholder="{{ translate('Product Subtitle') }}">
                                </div>
                            </div>
                        </div>
                        <div id="product-layout">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Product Layout')}}</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-auto">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="radio" name="product_layout" value="1" checked>
                                                <span></span>
                                                {{translate('Standard Layout')}}
                                            </label>
                                        </div>
                                        <div class="col-auto">
                                            <label class="sk-switch sk-switch-success mb-0">
                                                <input type="radio" name="product_layout" value="2">
                                                <span></span>
                                                {{translate('Layout with Bigger Images & Video')}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="brand">
                            <label class="col-md-3 col-from-label">{{translate('Brand')}}</label>
                            <div class="col-md-8">
                                <select class="form-control sk-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                    @foreach (\App\Brand::all() as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="type">
                            <label class="col-md-3 col-from-label">{{translate('Type')}}</label>
                            <div class="col-md-8">
                                <select class="form-control sk-selectpicker" name="type_id" id="type_id" data-live-search="true">
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Weight (gr)')}}</label>
                            <div class="col-md-8">
                                <input type="number" lang="en" class="form-control" name="weight" placeholder="{{ translate('Weight (gr)') }}" value="0" min="0" required>
                            </div>
                        </div>
                        @if (hasAccessOnContent())
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Unit')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Minimum Qty')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" lang="en" class="form-control" name="min_qty" value="1" min="1" required>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="unit" value="Pc">
                            <input type="hidden" name="min_qty" value="1">
                        @endif
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Tags')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control sk-tag-input" name="tags[]" placeholder="{{ translate('Type and hit enter to add a tag') }}" required>
                                <small class="text-muted">{{translate('This is used for search. Input those words by which cutomer can find this product.')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Part Number')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="part_number" placeholder="{{ translate('Part Number') }}" >
                            </div>
                        </div>
                        @if (hasAccessOnContent())
                            @php
                            $pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
                            @endphp
                            @if ($pos_addon != null && $pos_addon->activated == 1)
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Barcode')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}">
                                </div>
                            </div>
                            @endif
                        @endif
                        @php
                        $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
                        @endphp
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Refundable')}}</label>
                            <div class="col-md-8">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="refundable" checked>
                                    <span></span>
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
                                    <input type="hidden" name="photos" class="selected-files">
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
                                    <input type="hidden" name="thumbnail_img" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" id="product-videos">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Videos')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Provider')}}</label>
                            <div class="col-md-8">
                                <select class="form-control sk-selectpicker" name="video_provider" id="video_provider">
                                    <option value="youtube">{{translate('Youtube')}}</option>
                                    <?php /*
                                    <option value="dailymotion">{{translate('Dailymotion')}}</option>
                                    <option value="vimeo">{{translate('Vimeo')}}</option>*/ ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Link')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="video_link" placeholder="{{ translate('Video Link') }}">
                                <small class="text-muted">{{translate("Use proper link without extra parameter. Don't use short share link/embeded iframe code.")}}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @if(hasAccessOnContent())
                    <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Variation')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" value="{{translate('Colors')}}" disabled>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control sk-selectpicker" data-live-search="true" data-selected-text-format="count" name="colors[]" id="colors" multiple disabled>
                                    @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                    <option  value="{{ $color->id }}" data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>"></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input value="1" type="checkbox" name="colors_active">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" value="{{translate('Attributes')}}" disabled>
                            </div>
                            <div class="col-md-8">
                                <select name="choice_attributes[]" id="choice_attributes" class="form-control sk-selectpicker" data-selected-text-format="count" data-live-search="true" multiple data-placeholder="{{ translate('Choose Attributes') }}">
                                    @foreach (\App\Attribute::all() as $key => $attribute)
                                    <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}</p>
                            <br>
                        </div>

                        <div class="customer_choice_options" id="customer_choice_options">

                        </div>
                    </div>
                </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product price')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Unit price')}} <small>{{translate('(without VAT)')}}</small> <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control" required>
                            </div>
                        </div>
                        @if (hasAccessOnContent())
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Wholesale price')}} <small>{{translate('(without VAT)')}}</small> <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Wholesale price') }}" name="wholesale_price" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Purchase price')}} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Purchase price') }}" name="purchase_price" class="form-control" required>
                            </div>
                        </div>

                        {{--
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Tax')}} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Tax') }}" name="tax" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control sk-selectpicker" name="tax_type">
                                    <option value="amount">{{translate('Flat')}}</option>
                                    <option value="percent">{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>
                        --}}
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Discount')}}</label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Discount') }}" name="discount" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control sk-selectpicker" name="discount_type">
                                    <option value="amount">{{translate('Flat')}}</option>
                                    <option value="percent">{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="quantity">
                            <label class="col-md-3 col-from-label">{{translate('Quantity')}} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="1" placeholder="{{ translate('Quantity') }}" name="current_stock" class="form-control" required>
                            </div>
                        </div>
                        @else
                            <input type="hidden"
                                   name="purchase_price"
                                   value="0">
                        @endif
                        <br>
                        <div class="sku_combination" id="sku_combination">

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row" id="short-description">
                            <label class="col-md-3 col-from-label">{{translate('Short Description')}}</label>
                            <div class="col-md-8">
                                <textarea name="short_description" class="form-control" placeholder="{{translate('Short Description')}}"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-md-8">
                                <textarea class="sk-text-editor" name="description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

<!--                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Shipping Cost')}}</h5>
                    </div>
                    <div class="card-body">

                    </div>
                </div>-->
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('PDF Specification')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('PDF Specification')}}</label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="skuploader" data-type="document">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="pdf" class="selected-files">
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
                            <label class="col-md-3 col-from-label">{{translate('Meta Title')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="meta_title" placeholder="{{ translate('Meta Title') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-md-8">
                                <textarea name="meta_description" rows="8" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="skuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="meta_img" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">
                            {{translate('Shipping Configuration')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Free Shipping')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="free" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Flat Rate')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="flat_rate">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="flat_rate_shipping_div" style="display: none">
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Shipping cost')}}</label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Product Wise Shipping')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="product_wise">
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
                                <div class="col-md-6">
                                    <input type="text" class="form-control" min="0" value="0" step="1" name="shipping_cost[{{ $city->name }}]" placeholder="{{ translate('Cost') }}">
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
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Low Stock Quantity Warning')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="name">
                                {{translate('Quantity')}}
                            </label>
                            <input type="number" name="low_stock_quantity" value="1" min="0" step="1" class="form-control">
                        </div>
                    </div>
                </div>
                    @endif
                @if (hasAccessOnContent())
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
                                    <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Show Stock With Text Only')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="stock_visibility_state" value="text">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Hide Stock')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="stock_visibility_state" value="hide">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                @else
                    <input type="hidden" name="stock_visibility_state" value="quantity">
                @endif
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Cash On Delivery')}}</h5>
                    </div>
                    <div class="card-body">
                        @if (get_setting('cash_payment') == '1')
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                <div class="col-md-6">
                                    <label class="sk-switch sk-switch-success mb-0">
                                        <input type="checkbox" name="cash_on_delivery" value="1" checked="">
                                        <span></span>
                                    </label>
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
                @if (hasAccessOnContent())
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
                                            <input type="checkbox" name="outlet" value="1">
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
                                            <input type="checkbox" name="used" value="1">
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
                            <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="featured" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5 class="mb-0 h6">{{translate('Cyprus Shipping Only')}}</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="form-group row">--}}
{{--                            <label class="col-md-6 col-from-label">{{translate('Status')}}</label>--}}
{{--                            <div class="col-md-6">--}}
{{--                                <label class="sk-switch sk-switch-success mb-0">--}}
{{--                                    <input type="checkbox" name="cyprus_shipping_only" value="1">--}}
{{--                                    <span></span>--}}
{{--                                </label>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Todays Deal')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                            <div class="col-md-6">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" name="todays_deal" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if (hasAccessOnContent())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Flash Deal')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="name">
                                {{translate('Add To Flash')}}
                            </label>
                            <select class="form-control sk-selectpicker" name="flash_deal_id" id="flash_deal">
                                <option value="">Choose Flash Title</option>
                                @foreach(\App\FlashDeal::where("status", 1)->get() as $flash_deal)
                                    <option value="{{ $flash_deal->id}}">
                                        {{ $flash_deal->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="name">
                                {{translate('Discount')}}
                            </label>
                            <input type="number" name="flash_discount" value="0" min="0" step="1" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">
                                {{translate('Discount Type')}}
                            </label>
                            <select class="form-control sk-selectpicker" name="flash_discount_type" id="flash_discount_type">
                                <option value="">Choose Discount Type</option>
                                <option value="amount">{{translate('Flat')}}</option>
                                <option value="percent">{{translate('Percent')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif
                @if (hasAccessOnContent())
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
                                <input type="number" class="form-control" name="est_shipping_days" min="1" step="1" placeholder="Shipping Days">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend">Days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if (hasAccessOnContent())
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

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <select class="form-control sk-selectpicker" name="tax_type[]">
                                    <option value="amount">{{translate('Flat')}}</option>
                                    <option value="percent">{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="col-12">
                <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="First group">
                        <button type="submit" name="button" value="draft" class="btn btn-warning">{{ translate('Save As Draft') }}</button>
                    </div>
                    <div class="btn-group mr-2" role="group" aria-label="Third group">
                        <button type="submit" name="button" value="unpublish" class="btn btn-primary">{{ translate('Save & Unpublish') }}</button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Second group">
                        <button type="submit" name="button" value="publish" class="btn btn-success">{{ translate('Save & Publish') }}</button>
                    </div>
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
        } else {
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

    $("[name=shipping_type]").on("change", function (){
        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if($(this).val() == 'product_wise'){
            $(".product_wise_shipping_div").show();
        }
        if($(this).val() == 'flat_rate'){
            $(".flat_rate_shipping_div").show();
        }

    });

    function add_more_customer_choice_option(i, name){
        $('#customer_choice_options').append('<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control sk-tag-input" name="choice_options_'+i+'[]" placeholder="{{ translate('Enter choice values') }}" data-on-change="update_sku"></div></div>');

    	SK.plugins.tagify();
    }

	$('input[name="colors_active"]').on('change', function() {
	    if(!$('input[name="colors_active"]').is(':checked')) {
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

	$('input[name="unit_price"]').on('keyup', function() {
	    update_sku();
	});
	$('input[name="wholesale_price"]').on('keyup', function() {
	    update_sku();
	});

	$('input[name="name"]').on('keyup', function() {
	    update_sku();
	});

	function delete_row(em){
		$(em).closest('.form-group row').remove();
		update_sku();
	}

    function delete_variant(em){
		$(em).closest('.variant').remove();
	}

	function update_sku(){
		$.ajax({
		   type:"POST",
		   url:'{{ route('products.sku_combination') }}',
		   data:$('#choice_form').serialize(),
		   success: function(data){
			   $('#sku_combination').html(data);
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

	$('#choice_attributes').on('change', function() {
		$('#customer_choice_options').html(null);
		$.each($("#choice_attributes option:selected"), function(){
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
		update_sku();
	});


</script>

@endsection
