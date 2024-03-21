@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Brand Information')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill border-light">
  				@foreach (\App\Language::all() as $key => $language)
  					<li class="nav-item">
  						<a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('brands.edit', ['id'=>$brand->id, 'lang'=> $language->code] ) }}">
  							<img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
  							<span>{{ $language->name }}</span>
  						</a>
  					</li>
	            @endforeach
  			</ul>
            <form class="p-4" action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PATCH">
                <input type="hidden" name="lang" value="{{ $lang }}">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $brand->getTranslation('name', $lang) }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{translate('BTMS Brand')}}</label>
                    <div class="col-md-9">
                        <select class="select2 form-control sk-selectpicker" name="accounting_code" data-toggle="select2" data-placeholder="Choose ..."data-live-search="true" data-selected="{{ $brand->accounting_code }}">
                            <option value="0">{{ translate('Select BTMS Brand') }}</option>
                            @foreach ($btms_brands as $btms_brand)
                                <option value="{{ $btms_brand->{'Category Code'} }}">{{ $btms_brand->{'Name'} }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Logo')}} <small>({{ translate('Height: 85, Width: Depends on the logo') }})</small></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="skuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="logo" value="{{$brand->logo}}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{translate('Header Type')}}</label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-auto">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="type" value="image" @if($brand->type=='image') checked @endif >
                                    <span></span>
                                    {{translate('Image')}}
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="type" value="video" @if($brand->type=='video') checked @endif >
                                    <span></span>
                                    {{translate('Video')}}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="type-image" class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Header')}} <small>({{ translate('Dimensions: 2000 x 1536, Safe Zone 991 x 650') }})</small></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="skuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="header" value="{{$brand->header}}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div id="type-video" class="form-group row">
                    <label class="col-md-3 col-form-label">{{translate('Youtube Video Link')}}</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="video_link" placeholder="{{ translate('Youtube Video Link') }}" value="{{$brand->video_link}}">
                        <small class="text-muted">{{translate("Use proper link from youtube without extra parameter. Don't use short share link/embeded iframe code.")}}</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Banner for About us')}} <small>({{ translate('Dimensions: 1900 x 798, Safe Zone: 780 x 798') }})</small></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="skuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="banner" value="{{$brand->banner}}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('About Description')}}</label>
                    <div class="col-sm-9">
                        <textarea name="about_desc" rows="2" class="form-control" required>{{ $brand->getTranslation('about_desc', $lang) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Banner Description')}}</label>
                    <div class="col-sm-9">
                        <textarea name="banner_desc" rows="2" class="form-control" required>{{ $brand->getTranslation('banner_desc', $lang) }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Slogan Title')}} (1)</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="slogan_title" value="{{ $brand->getTranslation('slogan_title', $lang) }}" placeholder="{{translate('Slogan Title')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Slogan Description')}} (1)</label>
                    <div class="col-sm-9">
                        <textarea name="slogan_description" rows="2" class="form-control">{{ $brand->getTranslation('slogan_description', $lang) }}</textarea>
                        <small>{{translate('This will appear on the Brand Page if the brand has 3 categories or more')}}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Slogan Title')}} (2)</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="slogan_title_2" value="{{ $brand->getTranslation('slogan_title_2', $lang) }}" placeholder="{{translate('Slogan Title')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Slogan Description')}} (2)</label>
                    <div class="col-sm-9">
                        <textarea name="slogan_description_2" rows="2" class="form-control">{{ $brand->getTranslation('slogan_description_2', $lang) }}</textarea>
                        <small>{{translate('This will appear on the Brand Page if the brand has 7 categories or more')}}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        {{translate('Ordering Number')}}
                    </label>
                    <div class="col-md-9">
                        <input type="number" name="order_level" value="{{ $brand->order_level }}" class="form-control" id="order_level" placeholder="{{translate('Order Level')}}">
                        <small>Higher number has high priority</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Meta Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="meta_title" value="{{ $brand->meta_title }}" placeholder="{{translate('Meta Title')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Meta Description')}}</label>
                    <div class="col-sm-9">
                        <textarea name="meta_description" rows="8" class="form-control">{{ $brand->meta_description }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Slug')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug" value="{{ $brand->slug }}" class="form-control">
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        showVideos();

        $('input[name="type"]').on("change", function (){
            showVideos();
        });

        function showVideos(){
            if($('input[name="type"]:checked').val() == 'image') {
                $('#type-image').show();
                $('#type-video').hide();
            } else {
                $('#type-image').hide();
                $('#type-video').show();
            }
        }
    </script>
@endsection
