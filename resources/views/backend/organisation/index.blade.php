@extends('backend.layouts.app')

@section('content')

    <?php

    use App\Models\Card;

    ?>



    @if(count($zero_vending_organisations)>0)
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Zero Vending Organisations') }}</h5>
        </div>
        <div class="card-body">
            <table class="table sk-table mb-0">

                <thead>
                <tr>
                    <th width="20px" data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Cards')}}</th>
                    <th  class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>


                @foreach($zero_vending_organisations as $key => $organisation_z)
                    <tr>
                        <td>{{ ($key+1)  }}</td>
                        <td>{{ $organisation_z->name }}</td>
                        <td>{{ $organisation_z->cards }}</td>
                        <td class="text-right inline-block " >

                            <form class="d-inline" action="{{route('organisations.import', ['organisation_id'=>$organisation_z->id, 'organisation_name'=> $organisation_z->name ])}}" method="POST" enctype="multipart/form-data">
                               @csrf
                                <button type="submit" class="btn btn-soft-primary btn-sm" > Import</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>

            <div class="sk-pagination">

            </div>

        </div>
    </div>

    @endif

    @if($_SERVER['REMOTE_ADDR'] == '82.102.76.201')
    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center justify-content-end">

            <div class="col-md-6 text-md-right">
                <a href="{{route('organisations.create')}}" class="btn btn-primary">
                    <span>{{translate('Add New Organisation')}}</span>
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Organisations') }}</h5>
            <form class="" id="sort_categories" action="" method="GET">
                <span class="fs-17 fw-600">{{translate('Search')}}:</span>
                <div class="d-inline-block box-inline pad-rgt pull-left ml-3">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th width="20px" data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Cards')}}</th>
                    <th  class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($organisations as $key => $organisation)
                    <tr>
                        <?php

                            $cards = Card::where('organisation_id', $organisation->id)->where('deleted_at', '=', null)->count();

                            ?>
                        <td>{{ ($key+1) + ($organisations->currentPage() - 1)*$organisations->perPage() }}</td>
                        <td>{{ $organisation->name }}</td>
                        <td>{{ $cards }}</td>
                        <td class="text-right">

                            @if($organisation->canteen == 1)
                                <a class="btn btn-soft-primary "href="{{route('canteen.index', $organisation->id)}}" title="{{ translate('Canteen') }}">
                                    {{ translate('Canteen') }}
                                </a>
                            @endif

                            @if($organisation->canteen == 1)
                                 <a class="btn btn-soft-primary" href="{{route('catering.index', $organisation->id)}}" title="{{ translate('Catering') }}">
                                     {{ translate('Catering') }}
                                 </a>
                            @endif




{{--                            <a class="btn btn-soft-primary " href="{{route('catering.index', $organisation->id)}}" title="{{ translate('Locations') }}">--}}
{{--                                {{ translate('Locations') }}--}}
{{--                            </a>--}}
{{--                            <a class="btn btn-soft-primary " href="{{route('catering.index', $organisation->id)}}" title="{{ translate('Periods') }}">--}}
{{--                                {{ translate('Periods') }}--}}
{{--                            </a>--}}
                            <a class="btn btn-soft-primary " href="{{route('organisation_cards.index', $organisation->id)}}" title="{{ translate('Cards') }}">
                                {{ translate('Cards') }}
                            </a>
                            <a class="btn btn-soft-secondary " href="{{route('organisations.edit', $organisation->id)}}" title="{{ translate('Edit') }}">
                                {{ translate('Edit') }}
                            </a>
                            <a href="#" class="btn btn-soft-danger  confirm-delete" data-href="{{route('organisations.destroy', $organisation->id)}}" title="{{ translate('Delete') }}">
                                {{ translate('Delete') }}
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
            <div class="sk-pagination">


            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('categories.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Featured categories updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
