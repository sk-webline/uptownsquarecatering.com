@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Edit Product Category')}}</h5>
                </div>

                <div class="card-body" id="settings-form">
                    <form class="form-horizontal"
                          action="{{ route('canteen_product_categories.update', $category->id) }}" method="post"
                          enctype="multipart/form-data">

                        @csrf

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{translate('Name')}}</label>
                            <div class="col-md-9">
                                <input type="text" id="name" name="name"
                                       value="{{$category->name}}" class="form-control" required>
                            </div>
                        </div>


{{--                        @foreach(\App\Models\CanteenLanguage::all() as $lang)--}}

{{--                            @if($lang->code != 'en')--}}

{{--                                @php--}}

{{--                                    $translation = $category->translations->where('lang', $lang->code)->first();--}}

{{--                                    if($translation!=null){--}}
{{--                                        $translation = $translation->name;--}}
{{--                                    }--}}

{{--                                @endphp--}}
{{--                                <div class="form-group row">--}}
{{--                                    <label class="col-md-3 col-form-label"> {{translate('Name')}}--}}
{{--                                        ({{toUpper($lang->code)}})</label>--}}
{{--                                    <div class="col-md-9">--}}
{{--                                        <input type="text" placeholder="{{translate('Name')}}" name="{{$lang->code}}" value="{{ $translation }}"--}}
{{--                                               class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endif--}}

{{--                        @endforeach--}}

                        <div class="form-group mb-0 text-right">
                            <a href="{{route('canteen_product_categories.index')}}">
                                <button type="button" class="btn btn-soft-danger">{{translate('Cancel')}}</button>
                            </a>
                            <button type="submit"
                                    class="btn btn-primary save_holidays">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('script')


    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('organisation-link').classList.add('active');
        });
    </script>
@endsection

