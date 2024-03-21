
@extends('backend.layouts.app')

@section('content')


    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ucwords(translate('Edit Product'))}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('canteen_products.update', $product->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" value="{{$product->name}}" placeholder="{{translate('Name')}}" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" required>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @foreach(\App\Models\CanteenLanguage::all() as $lang)

                            @if($lang->code != 'en')

                                @php
                                    $translation = \App\Models\CanteenProductTranslation::where('canteen_product_id', $product->id)->where('lang', '=',  $lang->code)->first();

                                    if($translation !=null){
                                       $translation = $translation->name;
                                    }
                                @endphp
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label"> {{translate('Name')}}
                                        ({{toUpper($lang->code)}})</label>
                                    <div class="col-md-9">
                                        <input type="text" placeholder="{{translate('Name')}}" name="{{$lang->code}}" value="{{$translation}}"
                                               class="form-control ">
                                    </div>
                                </div>
                            @endif

                        @endforeach

                        @php
                            $categories = \App\Models\CanteenProductCategory::all();
                        @endphp

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Category')}}</label>
                            <div class="col-md-9">
                                <select class="select2 form-control sk-selectpicker {{ $errors->has('category') ? ' is-invalid' : '' }}" name="category" data-toggle="select2" data-placeholder="Choose ..." required>
                                    <option hidden value="">{{translate('Select Product Category')}}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}" @if($product->canteen_product_category_id==$category->id) selected @endif>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('category'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Price')}}</label>
                            <div class="col-md-9">

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium px-2">€</div>
                                    </div>
                                    <input type="number" min="0.01" step="0.01" max="100" value="{{$product->price}}" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" placeholder="{{translate('Price')}}" required>
                                </div>
                                @if ($errors->has('price'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Thumbnail Img')}}</label>
                            <div class="col-md-9">
                                <div class="input-group" data-toggle="skuploader" data-type="image" data-multiple="false">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="thumbnail_img" class="selected-files" value="{{$product->thumbnail_img}}">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('These images are visible in product details page gallery. Use 600x600 sizes images.')}}</small>
                                @if ($errors->has('thumbnail_img'))
                                    <span class="d-block invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('thumbnail_img') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Status')}}</label>
                            <div class="col-md-9">
                                <label class="sk-switch sk-switch-success mb-0 pt-1 pl-3">
                                    <input type="checkbox" name="status" @if($product->status == 1) checked @endif>
                                    <span></span>
                                </label>

                                @if ($errors->has('status'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <a href="{{route('canteen_products.index')}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">



    </script>
@endsection

