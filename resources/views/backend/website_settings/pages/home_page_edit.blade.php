@extends('backend.layouts.app')
@section('content')

<div class="row">
	<div class="col-xl-10 mx-auto">
		<h6 class="fw-600">{{ translate('Home Page Settings') }}</h6>

		{{-- Home Slider --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Slider') }}</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-info">
					{{ translate('We have limited banner height to maintain UI. We had to crop from both left & right side in view for different devices to make it responsive. Before designing banner keep these points in mind.') }}
				</div>
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Photos & Links') }} <small>({{ translate('Dimensions: 2000 x 1536, Safe Zone 991 x 650') }})</small></label>
						<div class="home-slider-target">
							<input type="hidden" name="types[]" value="home_slider_type">
							<input type="hidden" name="types[]" value="home_slider_images">
							<input type="hidden" name="types[]" value="home_slider_videos">
							<input type="hidden" name="types[]" value="home_slider_titles">
							<input type="hidden" name="types[]" value="home_slider_links">
							<input type="hidden" name="types[]" value="home_slider_links_texts">
							@if (get_setting('home_slider_images') != null)
								@foreach (json_decode(get_setting('home_slider_images'), true) as $key => $value)
									<div class="row gutters-5 this-slide">
										<div class="col-md">
											<div class="row gutters-5">
												<div class="col-md-6">
													<input type="hidden" name="types[]" value="home_slider_type">
													<input type="hidden" name="home_slider_type[]" value="{{json_decode(get_setting('home_slider_type'), true)[$key]}}">
													<label class="sk-switch sk-switch-success mb-3">
														<input type="checkbox" name="home_slider_type_check" value="video" @if(json_decode(get_setting('home_slider_type'), true)[$key] == "video") checked @endif >
														<span></span>
														{{translate('Video Type')}}
													</label>
												</div>
												<div class="col-md-6"></div>
												<div class="col-md-6">
													<div class="form-group" data-media="image">
														<div class="input-group" data-toggle="skuploader" data-type="image">
															<div class="input-group-prepend">
																<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
															</div>
															<div class="form-control file-amount">{{ translate('Choose File') }}</div>
															<input type="hidden" name="types[]" value="home_slider_images">
															<input type="hidden" name="home_slider_images[]" class="selected-files" value="{{json_decode(get_setting('home_slider_images'), true)[$key]}}">
														</div>
														<div class="file-preview box sm">
														</div>
													</div>
													<div class="form-group" data-media="video">
                                                        <div class="form-group">
                                                            <input type="hidden" name="types[]" value="home_slider_videos">
                                                            <input type="text" class="form-control" placeholder="{{translate('Youtube Video ID')}}" name="home_slider_videos[]" value="{{ json_decode(get_setting('home_slider_videos'), true)[$key] }}" title="Please insert the youtube video id">
                                                            <small>Please insert the youtube video id. e.g. https://www.youtube.com/watch?v=<b>sjdDR9dlbJM</b></small>
                                                        </div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<input type="hidden" name="types[]" value="home_slider_titles">
														<input type="text" class="form-control" placeholder="{{translate('Slide Slogan')}}" name="home_slider_titles[]" value="{{ json_decode(get_setting('home_slider_titles'), true)[$key] }}" required>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<input type="hidden" name="types[]" value="home_slider_links">
														<input type="text" class="form-control" placeholder="http://" name="home_slider_links[]" value="{{ json_decode(get_setting('home_slider_links'), true)[$key] }}">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<input type="hidden" name="types[]" value="home_slider_links_texts">
														<input type="text" class="form-control" placeholder="{{translate('Slide Link Text')}}" name="home_slider_links_texts[]" value="{{ json_decode(get_setting('home_slider_links_texts'), true)[$key] }}">
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm add-media"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5 this-slide">
								<div class="col-md">
									<div class="row gutters-5">
										<div class="col-md-6">
											<input type="hidden" name="types[]" value="home_slider_type">
											<input type="hidden" name="home_slider_type[]" value="image">
											<label class="sk-switch sk-switch-success mb-3">
												<input type="checkbox" name="home_slider_type_check" value="video">
												<span></span>
												{{translate('Video Type')}}
											</label>
										</div>
										<div class="col-md-6"></div>
										<div class="col-md-6">
											<div class="form-group" data-media="image">
												<div class="input-group" data-toggle="skuploader" data-type="image">
													<div class="input-group-prepend">
														<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
													</div>
													<div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="home_slider_images">
													<input type="hidden" name="home_slider_images[]" class="selected-files">
												</div>
												<div class="file-preview box sm">
												</div>
											</div>
											<div class="form-group" data-media="video" style="display:none">
											    <div class="form-group">
                                                    <input type="hidden" name="types[]" value="home_slider_videos">
                                                    <input type="text" class="form-control" placeholder="{{translate('Youtube Video ID')}}" name="home_slider_videos[]" title="Please insert the youtube video id">
                                                    <small>Please insert the youtube video id. e.g. https://www.youtube.com/watch?v=<b>sjdDR9dlbJM</b></small>
                                                </div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_slider_titles">
												<input type="text" class="form-control" placeholder="{{translate('Slide Slogan')}}" name="home_slider_titles[]" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_slider_links">
												<input type="text" class="form-control" placeholder="http://" name="home_slider_links[]">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_slider_links_texts">
												<input type="text" class="form-control" placeholder="{{translate('Slide Link Text')}}" name="home_slider_links_texts[]">
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-slider-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Feature Products Slogan --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Feature Products Title & Slogan') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group row">
						<label class="col-md-2 col-from-label">{{translate('Slogan')}}</label>
						<div class="col-md-10">
							<input type="hidden" name="types[]" value="feat_prods_slogan">
							<input type="text" name="feat_prods_slogan" class="form-control" value="{{ get_setting('feat_prods_slogan') }}" required>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-2 col-from-label">{{translate('Title')}}</label>
						<div class="col-md-10">
							<input type="hidden" name="types[]" value="feat_prods_title">
							<input type="text" name="feat_prods_title" class="form-control" value="{{ get_setting('feat_prods_title') }}" required>
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

        @if(hasAccessOnContent())
		{{-- Home Banner 1 --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Banner 1 (Max 3)') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Banner & Links') }}</label>
						<div class="home-banner1-target">
							<input type="hidden" name="types[]" value="home_banner1_images">
							<input type="hidden" name="types[]" value="home_banner1_links">
							@if (get_setting('home_banner1_images') != null)
								@foreach (json_decode(get_setting('home_banner1_images'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="skuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="home_banner1_images">
					                                <input type="hidden" name="home_banner1_images[]" class="selected-files" value="{{ json_decode(get_setting('home_banner1_images'), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_banner1_links">
												<input type="text" class="form-control" placeholder="http://" name="home_banner1_links[]" value="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="skuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[]" value="home_banner1_images">
											<input type="hidden" name="home_banner1_images[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[]" value="home_banner1_links">
										<input type="text" class="form-control" placeholder="http://" name="home_banner1_links[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-banner1-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Home Banner 2 --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Banner 2 (Max 3)') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Banner & Links') }}</label>
						<div class="home-banner2-target">
							<input type="hidden" name="types[]" value="home_banner2_images">
							<input type="hidden" name="types[]" value="home_banner2_links">
							@if (get_setting('home_banner2_images') != null)
								@foreach (json_decode(get_setting('home_banner2_images'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="skuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="home_banner2_images">
					                                <input type="hidden" name="home_banner2_images[]" class="selected-files" value="{{ json_decode(get_setting('home_banner2_images'), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_banner2_links">
												<input type="text" class="form-control" placeholder="http://" name="home_banner2_links[]" value="{{ json_decode(get_setting('home_banner2_links'), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="skuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[]" value="home_banner2_images">
											<input type="hidden" name="home_banner2_images[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[]" value="home_banner2_links">
										<input type="text" class="form-control" placeholder="http://" name="home_banner2_links[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-banner2-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Home categories--}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Categories') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Categories') }}</label>
						<div class="home-categories-target">
							<input type="hidden" name="types[]" value="home_categories">
							@if (get_setting('home_categories') != null)
								@foreach (json_decode(get_setting('home_categories'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col">
											<div class="form-group">
												<select class="form-control sk-selectpicker" name="home_categories[]" data-live-search="true" data-selected={{ $value }} required>
													@foreach (\App\Category::where('parent_id', 0)->with('childrenCategories')->get() as $category)
														<option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
														@foreach ($category->childrenCategories as $childCategory)
															@include('categories.child_category', ['child_category' => $childCategory])
														@endforeach
													@endforeach
					                            </select>
											</div>
										</div>
										<div class="col-auto">
											<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
												<i class="las la-times"></i>
											</button>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='<div class="row gutters-5">
								<div class="col">
									<div class="form-group">
										<select class="form-control sk-selectpicker" name="home_categories[]" data-live-search="true" required>
											@foreach (\App\Category::all() as $key => $category)
												<option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-auto">
									<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
										<i class="las la-times"></i>
									</button>
								</div>
							</div>'
							data-target=".home-categories-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>


		{{-- Home Banner 3 --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Home Banner 3 (Max 3)') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Banner & Links') }}</label>
						<div class="home-banner3-target">
							<input type="hidden" name="types[]" value="home_banner3_images">
							<input type="hidden" name="types[]" value="home_banner3_links">
							@if (get_setting('home_banner3_images') != null)
								@foreach (json_decode(get_setting('home_banner3_images'), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="skuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[]" value="home_banner3_images">
					                                <input type="hidden" name="home_banner3_images[]" class="selected-files" value="{{ json_decode(get_setting('home_banner3_images'), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[]" value="home_banner3_links">
												<input type="text" class="form-control" placeholder="http://" name="home_banner3_links[]" value="{{ json_decode(get_setting('home_banner3_links'), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="skuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[]" value="home_banner3_images">
											<input type="hidden" name="home_banner3_images[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[]" value="home_banner3_links">
										<input type="text" class="form-control" placeholder="http://" name="home_banner3_links[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-banner3-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- Top 10 --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Top 10') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group row">
						<label class="col-md-2 col-from-label">{{translate('Top Categories (Max 10)')}}</label>
						<div class="col-md-10">
							<input type="hidden" name="types[]" value="top10_categories">
							<select name="top10_categories[]" class="form-control sk-selectpicker" multiple data-max-options="10" data-live-search="true" data-selected={{ get_setting('top10_categories') }} required>
								@foreach (\App\Category::where('parent_id', 0)->with('childrenCategories')->get() as $category)
									<option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
									@foreach ($category->childrenCategories as $childCategory)
										@include('categories.child_category', ['child_category' => $childCategory])
									@endforeach
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-2 col-from-label">{{translate('Top Brands (Max 10)')}}</label>
						<div class="col-md-10">
							<input type="hidden" name="types[]" value="top10_brands">
							<select name="top10_brands[]" class="form-control sk-selectpicker" multiple data-max-options="10" data-live-search="true" required>
								@foreach (\App\Brand::all() as $key => $brand)
									<option value="{{ $brand->id }}" @if(in_array($brand->id, json_decode(get_setting('top10_brands')))) selected @endif>{{ $brand->getTranslation('name') }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>
        @endif
	</div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
		$(document).ready(function(){
		    SK.plugins.bootstrapSelect('refresh');
		});
		$(document).on('click', '[name="home_slider_type_check"]', function () {
			toggleMedia($(this));

		});
		function toggleMedia(el) {
			var parent = el.closest('.this-slide');
			if(el.is(':checked')){
				parent.find('.form-group[data-media="image"]').hide();
				parent.find('.form-group[data-media="video"]').show();
				parent.find('[name="home_slider_type[]"]').attr('value', 'video');
			} else {
				parent.find('.form-group[data-media="image"]').show();
				parent.find('.form-group[data-media="video"]').hide();
				parent.find('[name="home_slider_type[]"]').attr('value', 'image');
			}
		}
		$('[name="home_slider_type_check"]').each(function() {
			toggleMedia($(this));
		});

    </script>
@endsection
