@extends('backend.layouts.app')

@section('content')

    <?php
    use Carbon\Carbon;
    $organisation = \App\Models\Organisation::findorfail($organisation_setting->organisation_id);
    ?>

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 text-capitalize"> <a href="{{route('organisations.index')}}" class="text-black" >{{translate('Organisations')}} </a> > {{$organisation->name}} >
                    <a href="{{route('organisation_settings.index', $organisation->id)}}" class="text-black" >{{translate('Periods')}} </a> >
                    <a class="text-black" href="{{ route('organisation_settings.index', ['organisation_id'=>$organisation->id]  )}}"> {{date("d/m/Y", strtotime($organisation_setting->date_from))}}
                    - {{date("d/m/Y", strtotime($organisation_setting->date_to))}} </a> > {{translate('Catering Plans')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('catering_plans.create', ['organisation_setting_id'=>$organisation_setting->id])}}"
                   class="btn btn-primary">
                    <span>{{translate('Add New Catering Plan')}}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('Catering Plans') }}</h5>
            <form class="" id="sort_categories" action="" method="GET">
                <span class="fs-17 fw-600">{{translate('Search')}}:</span>
                <div class="d-inline-block box-inline pad-rgt pull-left ml-3">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"
                               @isset($sort_search) value="{{ $sort_search }}"
                               @endisset placeholder="{{ translate('Type name & Enter') }}">
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
                    <th>{{translate('Start Date')}}</th>
                    <th>{{translate('End Date')}}</th>
                    <th>{{translate('Snack Number')}}</th>
                    <th>{{translate('Lunch Number')}}</th>
                    <th>{{translate('Price')}}</th>
                    <th>{{translate('Publish Date')}}</th>
                    <th>{{translate('Number of Sales')}}</th>
                    <th>{{translate('Status')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($catering_plans as $key => $plan)
                    <tr>
                        <td>{{ ($key+1) + ($catering_plans->currentPage() - 1)*$catering_plans->perPage() }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>{{ Carbon::create($plan->from_date)->format('d/m/Y') }}</td>
                        <td>{{ Carbon::create($plan->to_date)->format('d/m/Y')  }}</td>
                        <td>{{ $plan->snack_num }}</td>
                        <td>{{ $plan->meal_num }}</td>
                        <td>{{ format_price($plan->price) }}</td>
                        <td>{{ Carbon::create($plan->publish_date)->format('d/m/Y')}}</td>
                        @php

                        $sales = \App\Models\CateringPlanPurchase::where('catering_plan_id', $plan->id)->count();

                            @endphp

                        <td>{{ $sales}}</td>
                        @if($plan->active==1)
                            <td>Active</td>
                        @else
                            <td>Inactive</td>
                        @endif
                        <td class="text-right">
                            <a class="btn btn-soft-secondary" href="{{route('catering_plans.edit', $plan->id)}}"
                               title="{{ translate('Edit') }}">
                                {{ translate('Edit') }}
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
            <div class="sk-pagination">
                {{$catering_plans->links()}}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')
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
    </script>
@endsection

