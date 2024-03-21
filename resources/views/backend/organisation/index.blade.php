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
                            <a class="btn btn-soft-primary " href="{{route('organisation_locations.index', $organisation->id)}}" title="{{ translate('Locations') }}">
                                Locations
                            </a>
                            <a class="btn btn-soft-primary " href="{{route('organisation_settings.index', $organisation->id)}}" title="{{ translate('Periods') }}">
                               Periods
                            </a>
                            <a class="btn btn-soft-primary " href="{{route('organisation_cards.index', $organisation->id)}}" title="{{ translate('Cards') }}">
                                Cards
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
