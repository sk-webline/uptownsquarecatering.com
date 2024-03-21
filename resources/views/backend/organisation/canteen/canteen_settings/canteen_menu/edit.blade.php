@extends('backend.layouts.app')

@section('content')

    @php
        use \App\Models\CanteenMenu;
    @endphp


    <div class="d-flex justify-content-between align-items-end py-10px">
        <span class="text-red">*{{translate('Inactive Days - Set Menu only for added extra days on the period calendar')}}</span>
        <a href="{{route('canteen_menu.index',$canteen_setting->id )}}">
            <button type="button" class="btn btn-soft-danger btn-sm">Back</button>
        </a>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6"> {{ $canteen_setting->organisation->name }} > {{ translate('Canteen Menu') }}</h5>

            <form id="filter_products" name="" action="" method="GET" enctype="multipart/form-data">
                @csrf
                <div class="d-block d-md-flex justify-content-end">
                    <div class="box-inline pad-rgt pull-left">

                            <div class="row gutters-5 align-items-end">
                                <div class="col-auto ">
                                    <div class="" style="min-width: 200px;">
                                        <select class="form-control form-control-sm sk-selectpicker mb-2 mb-md-0"
                                                name="category_filter">
                                            <option value="">{{ translate('All Categories') }}</option>
                                            @foreach (\App\Models\CanteenProductCategory::all() as $key => $category)
                                                <option value="{{ $category->id }}"
                                                        @if (isset($category_filter) && $category->id == $category_filter) selected @endif> {{ $category->name }}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="" style="min-width: 200px;">
                                        <input type="text" class="form-control" id="search" name="search"
                                               @isset($search) value="{{ $search }}"
                                               @endisset placeholder="{{ translate('Type name & Enter') }}">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-soft-primary btn-sm">{{translate('Search')}}</button>
                                </div>
                                <div class="col-auto">
                                    <input type="hidden" name="reset" value="">
                                    <button type="button" class="btn btn-soft-secondary reset-button btn-sm">{{translate('Reset')}}</button>
                                </div>
                            </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="card-body">


            @if((isset($search) && $search!=null) || (isset($category_filter) && $category_filter!=null))

            <div class="d-inline-block d-md-flex justify-content-end pb-10px  fs-14">

                    <div class="box-inline pad-rgt pull-left">
                        <div class="d-inline-block pr-15px">
                            <a class="select_all_page hov-text-primary c-pointer">{{translate('Select All products of this page')}} </a>
                        </div>
                        <div class="d-inline-block ">
                            <a class="select_all_filters hov-text-primary c-pointer"> {{translate('Select All products matching the filters')}} </a>
                        </div>
                    </div>

            </div>

            @endif

            <div id="product_table">

                @php
                    if(!isset($search)){
                        $search = null;
                    }

                     if(!isset($category_filter)){
                        $category_filter = null;
                    }
                @endphp

                @include('backend.organisation.canteen.canteen_settings.canteen_menu.update_menu_table')

            </div>

        </div>
    </div>

@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

        $(document).on('click', 'button.reset-button', function(){
            $('input[name=reset]').val('true');
            $('#filter_products').submit();
        });



        $(document).on('click', '.select_all_page', function () {

            var page_products = $('input[name=products_of_page]').val();

            $('#product_table').addClass('loader');


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:'{{ route('canteen_menu.select_all_products_of_page') }}',
                    type: 'POST',
                    data: {
                        canteen_setting_id: '{{$canteen_setting->id}}',
                        page_products: page_products,
                        search: '{{$search}}',
                        category_filter: '{{$category_filter}}'
                    },
                    success: function (data) {

                        console.log('data select_all_products_of_page ajax: ', data);

                        if (data.status == 1) {

                            $('#product_table').html(data.view);

                            SK.plugins.notify('success', data.msg);

                        } else {

                            SK.plugins.notify('danger', data.msg);
                            location.reload();

                        }

                        $('#product_table').removeClass('loader');

                    },
                    error: function () {

                    }
                });

        });

        $(document).on('click', '.select_all_filters', function () {

            $('#product_table').addClass('loader');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:'{{ route('canteen_menu.select_all_products_of_filters') }}',
                type: 'POST',
                data: {
                    canteen_setting_id: '{{$canteen_setting->id}}',
                    search: '{{$search}}',
                    category_filter: '{{$category_filter}}'
                },
                success: function (data) {

                    console.log('data select_all_products_of_filters ajax: ', data);

                    if (data.status == 1) {

                        $('#product_table').html(data.view);

                        SK.plugins.notify('success', data.msg);

                    } else {

                        SK.plugins.notify('danger', data.msg);
                        location.reload();

                    }

                    $('#product_table').removeClass('loader');

                },
                error: function () {

                }
            });

        });


    </script>
@endsection
