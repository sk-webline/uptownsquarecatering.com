@extends('backend.layouts.app')

@section('content')

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 text-capitalize"><a href="{{route('organisations.index')}}"
                                                  class="text-black">{{translate('Organisations')}} </a>
                    > {{$organisation->name}} > {{translate('Cards')}}</h1>
            </div>


            <div class="col-md-6 text-md-right">
                <button id="generate_custom_cards_btn" class="btn btn-soft-primary">
                    <span>{{translate('Generate Virtual Cards')}}</span>
                </button>
                <a href="{{route('organisation_cards.sync', $organisation->id)}}" class="btn btn-primary">
                    <span>{{translate('Synchronize')}}</span>
                </a>
            </div>
        </div>
    </div>

    {{--    <div class="sk-titlebar text-left mt-2 mb-3">--}}
    {{--        <div class="row align-items-center">--}}
    {{--            <div class="col-md-6">--}}
    {{--                <h1 class="h3 text-capitalize">{{translate('Organisations')}}</h1>--}}
    {{--            </div>--}}
    {{--            <div class="col-md-6 text-md-right">--}}
    {{--                <a href="{{ route('organisations.create')}}" class="btn btn-primary">--}}
    {{--                    <span>{{translate('Add New Organisation')}}</span>--}}
    {{--                </a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Organisation Cards') }}</h5>
            <form class="" id="sort_categories" action="" method="GET">
                <span class="fs-17 fw-600">{{translate('Search')}}:</span>
                <div class="d-inline-block box-inline pad-rgt pull-left ml-3">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control remove-all-spaces remove-last-space toUpperCase" id="search"
                               name="search"
                               @isset($sort_search) value="{{ $sort_search }}"
                               @endisset placeholder="{{ translate('Type RFID & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table sk-table mb-0">
                <thead>
                <tr>
                    <th width="20px" data-breakpoints="lg">#</th>
                    <th>{{translate('Reverse Dec')}}</th>
                    <th>{{toUpper(translate('Dec'))}}</th>
                    <th>{{translate('Card Name')}}</th>
                    <th>{{translate('Customer Name')}}</th>
                    <th>{{translate('Customer Email')}}</th>
                    <th>{{translate('Import/Created Date')}}</th>
                    {{--                    <th  class="text-right">{{translate('Options')}}</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($cards as $key => $card)
                    <tr>
                        <td>{{ ($key+1) + ($cards->currentPage() - 1)*$cards->perPage() }}</td>
                        <td>{{ $card->rfid_no }}</td>

                        @if($card->rfid_no_dec!=null)
                            <td>{{ $card->rfid_no_dec }}</td>
                        @else
                            <td>-</td>
                        @endif
                        @if($card->name!=null)
                            <td>{{ $card->name }}</td>
                        @else
                            <td>-</td>
                        @endif

                        @if($card->user_id!=null)
                            @php
                                $user = \App\User::findorfail($card->user_id);
                            @endphp
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email}}</td>
                        @else
                            <td>-</td>
                            <td>-</td>
                        @endif
                        <td>
                            {{ Carbon\Carbon::createFromTimestamp(strtotime($card->created_at))->format('d/m/Y H:i:s') ?? $card->created_at }}
                        </td>
                        <td class="text-right">
                            {{--                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="" title="{{ translate('Edit') }}">--}}
                            {{--                                <i class="las la-edit"></i>--}}
                            {{--                            </a>--}}
                            {{--                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="" title="{{ translate('Delete') }}">--}}
                            {{--                                <i class="las la-trash"></i>--}}
                            {{--                            </a>--}}
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
            <div class="sk-pagination">
                {{$cards->links()}}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
    @include('modals.generate_custom_cards_modal')
@endsection


@section('script')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('organisation-link').classList.add('active');
        });

        function update_featured(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('categories.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Featured categories updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $('#generate_custom_cards_btn').on("click", function(){
            $('#generate-custom-cards-modal-content').removeClass('loader');
            $("#generate-custom-cards").modal("show");

        });


    </script>
@endsection
