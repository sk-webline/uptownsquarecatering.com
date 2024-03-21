@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
			<h1 class="h3">{{translate('All Brands')}}</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-7">
		<div class="card">
		    <div class="card-header row gutters-5">
				<div class="col text-center text-md-left">
					<h5 class="mb-md-0 h6">{{ translate('Brands') }}</h5>
				</div>
				<div class="col-md-4">
					<form class="" id="sort_brands" action="" method="GET">
						<div class="input-group input-group-sm">
					  		<input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
						</div>
					</form>
				</div>
		    </div>
		    <div class="card-body">
		        <table class="table sk-table mb-0">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>{{translate('Name')}}</th>
		                    <th>{{translate('Logo')}}</th>
		                    <th class="text-right">{{translate('Options')}}</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($brands as $key => $brand)
		                    <tr>
		                        <td>{{ ($key+1) + ($brands->currentPage() - 1)*$brands->perPage() }}</td>
		                        <td>{{ $brand->getTranslation('name') }}</td>
														<td>
		                            <img src="{{ uploaded_asset($brand->logo) }}" alt="{{translate('Brand')}}" class="h-50px">
		                        </td>
		                        <td class="text-right">
		                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('brands.edit', ['id'=>$brand->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
		                                <i class="las la-edit"></i>
		                            </a>
		                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('brands.destroy', $brand->id)}}" title="{{ translate('Delete') }}">
		                                <i class="las la-trash"></i>
		                            </a>
		                        </td>
		                    </tr>
		                @endforeach
		            </tbody>
		        </table>
		        <div class="sk-pagination">
                	{{ $brands->appends(request()->input())->links() }}
            	</div>
		    </div>
		</div>
	</div>
	<div class="col-md-5">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0 h6">{{ translate('Add New Brand') }}</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('brands.store') }}" method="POST">
					@csrf
					<div class="form-group mb-3">
						<label for="name">{{translate('Name')}}</label>
						<input type="text" placeholder="{{translate('Name')}}" name="name" class="form-control" required>
					</div>
                    <div class="form-group mb-3">
                        <label >{{translate('BTMS Brand')}}</label>
                        <div>
                            <select class="select2 form-control sk-selectpicker" name="accounting_code" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                <option value="0">{{ translate('Select BTMS Brand') }}</option>
                                @foreach ($btms_brands as $btms_brand)
                                    <option value="{{ $btms_brand->{'Category Code'} }}">{{ $btms_brand->{'Name'} }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
					<div class="form-group mb-3">
						<label for="name">{{translate('Logo')}} <small>({{ translate('Height: 85, Width: Depends on the logo') }})</small></label>
						<div class="input-group" data-toggle="skuploader" data-type="image">
							<div class="input-group-prepend">
									<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose File') }}</div>
							<input type="hidden" name="logo" class="selected-files">
						</div>
						<div class="file-preview box sm">
						</div>
					</div>
                    <div class="form-group mb-3">
                        <label>{{translate('Header Type')}}</label>
                        <div class="row">
                            <div class="col-auto">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="type" value="image" checked>
                                    <span></span>
                                    {{translate('Image')}}
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="radio" name="type" value="video">
                                    <span></span>
                                    {{translate('Video')}}
                                </label>
                            </div>
                        </div>
                    </div>
					<div id="type-image" class="form-group mb-3">
						<label for="name">{{translate('Header')}} <small>({{ translate('Dimensions: 2000 x 1536, Safe Zone 991 x 650') }})</small></label>
						<div class="input-group" data-toggle="skuploader" data-type="image">
							<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose File') }}</div>
							<input type="hidden" name="header" class="selected-files">
						</div>
						<div class="file-preview box sm">
						</div>
					</div>
                    <div id="type-video" class="form-group mb-3">
                        <label>{{translate('Youtube Video Link')}}</label>
                        <input type="text" class="form-control" name="video_link" placeholder="{{ translate('Youtube Video Link') }}">
                        <small class="text-muted">{{translate("Use proper link from youtube without extra parameter. Don't use short share link/embeded iframe code.")}}</small>
                    </div>
					<div class="form-group mb-3">
						<label for="name">{{translate('Banner for About us')}} <small>({{ translate('Dimensions: 1900 x 798, Safe Zone: 780 x 798') }})</small></label>
						<div class="input-group" data-toggle="skuploader" data-type="image">
							<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose File') }}</div>
							<input type="hidden" name="banner" class="selected-files">
						</div>
						<div class="file-preview box sm">
						</div>
					</div>
					<div class="form-group mb-3">
						<label for="name">{{translate('About Description')}}</label>
						<textarea name="about_desc" rows="3" class="form-control" required></textarea>
					</div>
					<div class="form-group mb-3">
						<label for="name">{{translate('Banner Description')}}</label>
						<textarea name="banner_desc" rows="3" class="form-control" required></textarea>
					</div>

					<div class="form-group mb-3">
						<label>{{translate('Slogan Title')}} (1)</label>
						<input type="text" class="form-control" name="slogan_title" placeholder="{{translate('Slogan Title')}}">
					</div>
					<div class="form-group mb-3">
						<label>{{translate('Slogan Description')}} (1)</label>
						<textarea name="slogan_description" rows="3" class="form-control"></textarea>
						<small>{{translate('This will appear on the Brand Page if the brand has 3 categories or more')}}</small>
					</div>

					<div class="form-group mb-3">
						<label>{{translate('Slogan Title')}} (2)</label>
						<input type="text" class="form-control" name="slogan_title_2" placeholder="{{translate('Slogan Title')}}">
					</div>
					<div class="form-group mb-3">
						<label>{{translate('Slogan Description')}} (2)</label>
						<textarea name="slogan_description_2" rows="3" class="form-control"></textarea>
						<small>{{translate('This will appear on the Brand Page if the brand has 7 categories or more')}}</small>
					</div>
					<div class="form-group mb-3">
						<label>{{translate('Ordering Number')}}</label>
						<input type="number" name="order_level" class="form-control" id="order_level" placeholder="{{translate('Order Level')}}">
						<small>Higher number has high priority</small>
					</div>
					<div class="form-group mb-3">
						<label for="name">{{translate('Meta Title')}}</label>
						<input type="text" class="form-control" name="meta_title" placeholder="{{translate('Meta Title')}}">
					</div>
					<div class="form-group mb-3">
						<label for="name">{{translate('Meta Description')}}</label>
						<textarea name="meta_description" rows="5" class="form-control"></textarea>
					</div>
					<div class="form-group mb-3 text-right">
						<button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_brands(el){
        $('#sort_brands').submit();
    }

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
