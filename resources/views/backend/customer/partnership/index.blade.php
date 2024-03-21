@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
			<h1 class="h3">{{translate('All Partnership Requests')}}</h1>
	</div>
</div>


<div class="card">
    <div class="card-header d-block d-lg-flex">
        <h5 class="mb-0 h6">{{translate('Partnership Requests')}}</h5>
        <div class="">
            <form class="" id="sort_customers" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="table sk-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Email Address')}}</th>
                    <th data-breakpoints="lg">{{translate('Company')}}</th>
                    <th data-breakpoints="lg">{{translate('Country')}}</th>
                    <th data-breakpoints="lg">{{translate('City')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{translate('Interests')}}</th>
                    <th data-breakpoints="lg">{{translate('Accept')}}</th>
                    <th>{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $key => $user)
                    @php
                    $city = \App\City::find($user->city);
                    if($city!=null) {
                      $city_name = $city->name;
                    } else {
                      $city_name = $user->city;
                    }
                    @endphp
                    <tr>
                        <td>{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->company}}</td>
                        <td>{{\App\Country::find($user->country)->name}}</td>
                        <td>{{$city_name}}</td>
                        <td>+{{$user->phone_code}} {{$user->phone}}</td>
                        <td>{{$user->interests}}</td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_accept(this)" value="{{ $user->id }}" type="checkbox" <?php if ($user->accept == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('partnership-user.destroy', $user->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="sk-pagination">
            {{ $users->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function update_accept(el){
          if(el.checked){
            var status = 1;
          }
          else{
            var status = 0;
          }
          $.post('{{ route('partnership-user.accept_partner_request') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data.status){
                let edit_customer_link = '{{route('customers.index')}}/edit/'+data.customer_id;
                window.location.href = edit_customer_link;
                SK.plugins.notify('success', '{{ translate('Partnership Users updated successfully') }}');
            }
            else{
              SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
          });
        }
    </script>
@endsection
