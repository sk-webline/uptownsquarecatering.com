@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('City Information')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('store_cities.store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            {{translate('Ordering Number')}}
                        </label>
                        <div class="col-md-9">
                            <input type="number" name="order_level" class="form-control" id="order_level" placeholder="{{translate('Order Level')}}">
                            <small>Higher number has high priority</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Image')}} <small>({{ translate('990 x 570') }})</small></label>
                        <div class="col-md-9">
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

                    <div class="form-group mb-0 text-right">
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
      $(document).ready(function(){
        show_current_video_type();
      });

      $(document).on('change', 'input[name="video_type"]', function(){
        show_current_video_type();
      });
      function show_current_video_type() {
        var vid_type = $('input[name="video_type"]:checked').val();
        if(vid_type=='youtube'){
          $('#vid-type-youtube').removeClass('d-none');
          $('#vid-type-upload').addClass('d-none');
        } else {
          $('#vid-type-youtube').addClass('d-none');
          $('#vid-type-upload').removeClass('d-none');
        }
      }
    </script>
@endsection
