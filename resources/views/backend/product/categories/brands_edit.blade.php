@extends('backend.layouts.app')

@section('content')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{translate('Category Brands Descriptions')}}</h5>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-fill border-light">
                        @foreach (\App\Language::all() as $key => $language)
                            <li class="nav-item">
                                <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('categories.brands_edit', ['id'=>$category->id, 'lang'=> $language->code] ) }}">
                                    <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                    <span>{{$language->name}}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <form class="p-4" action="{{ route('categories.brand_update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="lang" value="{{ $lang }}">
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        @csrf
                        @if(count($brands) > 0)
                            <div class="alert alert-info">{{"*If you want to edit the category description of a specific brand you have to link a product with the brand you want and the current category or the subcategories that are fall this category"}}</div>
                            @foreach ($brands as $brand)
                                @php
                                    $description = \App\CategoryBrand::where('category_id', $category->id)->where('brand_id', $brand->id)->first();
                                @endphp
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{translate('Description for')}} <span class="fw-700">{{$brand->getTranslation('name')}}</span></label>
                                    <div class="col-md-9">
                                        <input type="hidden" name="ids[]" value="{{$brand->id}}">
                                        <textarea name="descriptions[]" rows="5" class="form-control" required>@php if($description!=null) { echo $description->getTranslation('description', $lang);} @endphp</textarea>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="py-50px text-center fw-700">{{"You have to link a product with this category or the category's children in order to view the settings in this page"}}</div>
                        @endif
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection