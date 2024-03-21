@extends('backend.layouts.app')

@section('content')

    <?php

    use App\Models\Card;

    ?>

    <div class="row">

        <div class="col-12 col-lg">
            <div class="row align-items-center no-gutters pb-10px">
                <div class="col">
                    <h6 class="text-capitalize"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > <a class="text-black" href="{{ route('canteen.index', ['organisation_id'=>$organisation->id]  )}}">{{$organisation->name}}</a> > {{translate('Canteen Periods')}}</h6>
                </div>
                <div class="col-auto text-md-right">
                    <a href=" {{ route('canteen_settings.create', ['organisation_id'=>$organisation->id]  )}}" class="btn btn-primary fs-12">
                        <span>{{translate('Add New Period')}}</span>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-block d-md-flex">
                    <h5 class="mb-0 h6">{{ translate('Canteen Periods') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table sk-table mb-0">
                        <thead>
                        <tr>
                            <th width="20px" data-breakpoints="lg">#</th>
                            <th>{{translate('Period')}}</th>
                            <th  class="text-right">{{translate('Options')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($canteen_periods as $key => $period)
                            <tr>
                                <td>{{ ($key+1) + ($canteen_periods->currentPage() - 1)*$canteen_periods->perPage() }}</td>
                                <td> {{date("d/m/Y", strtotime($period->date_from))}} - {{date("d/m/Y", strtotime($period->date_to))}}</td>
                                <td class="text-right">
                                    <a class="btn btn-soft-primary fs-12" href="{{route('canteen_menu.index', $period->id)}}" title="{{ translate('Catering Plans') }}">
                                        {{translate('Menu')}}
                                    </a>
                                    <a class="btn btn-soft-secondary fs-12" href="{{route('canteen_settings.edit', $period->id)}}" title="{{ translate('Edit') }}">
                                        {{ translate('Edit') }}
                                    </a>
                                    <a href="#" class="btn btn-soft-danger confirm-delete fs-12" data-href="{{route('canteen_settings.destroy', $period->id)}}" title="{{ translate('Delete') }}">
                                        {{ translate('Delete') }}
                                    </a>


                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <div class="sk-pagination">
                        {{$canteen_periods->links()}}
                    </div>
                </div>
            </div>


        </div>

        <div class="col">
            <div class="row align-items-center no-gutters pb-10px">
                <div class="col">
                    <h6 class="text-capitalize"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > <a class="text-black" href="{{ route('canteen.index', ['organisation_id'=>$organisation->id]  )}}">{{$organisation->name}}</a> > {{translate('Canteen Locations')}}</h6>
                </div>
                <div class="col-auto text-md-right">
                    <a href="{{ route('canteen_locations.create', ['organisation_id'=>$organisation->id]  )}}" class="btn btn-primary fs-12">
                        <span>{{translate('Add New Location')}}</span>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-block d-md-flex">
                    <h5 class="mb-0 h6">{{ translate('Canteen Locations') }}</h5>
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
                            <th  class="text-right">{{translate('Options')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($canteen_locations as $key => $location)
                            <tr>
                                <td>{{ ($key+1) + ($canteen_locations->currentPage() - 1)*$canteen_locations->perPage() }}</td>
                                <td> {{$location->name}}</td>
                                <td class="text-right">
                                    <a class="btn btn-soft-secondary fs-12" href="{{route('canteen_locations.edit', $location->id)}}" title="{{ translate('Edit') }}">
                                        {{ translate('Edit') }}
                                    </a>
                                    <a href="#" class="btn btn-soft-danger confirm-delete fs-12" data-href="{{route('canteen_locations.destroy', $location->id)}}" title="{{ translate('Delete') }}">
                                        {{ translate('Delete') }}
                                    </a>

                                </td>

                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <div class="sk-pagination">
                        {{$canteen_locations->links()}}
                    </div>
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

    </script>
@endsection
