@extends('backend.layouts.app')

@section('content')

    @php
        $organisations= \App\Models\Organisation::all();

         $all_organisations= array();

        foreach ($organisations as $key => $organisation) {

            $all_organisations[] = $organisation->id;
        }

        $all_organisations = json_encode($all_organisations);

    @endphp

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{translate('All Customers')}}</h1>
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Filters') }}</h5>
            </div>
        </div>

        <div class="card-body">
            <form class="" id="search_form" action="{{route('customers.index')}}" method="GET">
                <input type="hidden" name="timestamp" value="{{\Carbon\Carbon::now()->format('d/m/y H:i:S')}}">
                <div class="row gutters-5 align-items-end ">
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <label class="h6"> {{translate('Organisation')}} </label>
                            <select id="demo-ease" class="sk-selectpicker w-300px d-block" name="organisation[]"
                                    multiple onchange="all_selected()">
                                <option value="all">{{ translate('All') }}</option>
                                @foreach ($organisations as $key => $organisation)
                                    <option
                                        value="{{ $organisation->id }}">{{ $organisation->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <label class="h6"> {{translate('Register Date')}} </label>
                            <input type="text" class="sk-date-range form-control d-block"
                                   @if(isset($date)) value="{{$date}}" @endif
                                   name="date"
                                   placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                   data-separator=" to " data-advanced-range="true" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group mb-0">
                            <label class="h6"> {{ translate('Type Email or Name or RFID No')}} </label>
                            <input type="text" class="form-control" id="search" name="search"
                                   @isset($sort_search) value="{{ $sort_search }}"
                                   @endisset placeholder="{{ translate('Type Email or Name or RFID No') }}">

                        </div>
                    </div>

                    <div class="col-auto px-4">

                        <div class="pt-30px">
                            <label class="pr-5px">{{translate('Show All')}}</label>
                            <label class="sk-switch sk-custom-switch sk-switch-grey  pt-5px">
                                <input type="checkbox" name="customers_with_no_purchase"
                                       @if(\Illuminate\Support\Facades\Session::has('customers_with_no_purchase')) checked @endif >
                                <span></span>
                            </label>
                            <label class="pl-5px w-150px">{{translate('Show Customers without Plan Purchase')}}</label>
                        </div>

                    </div>

                    <div class="col-auto text-right">
                        <div class="pr-5px mb-1 d-block h6"> {{ translate('Export By RFID') }} <span class="fs-10">({{ translate('Only for Export') }}) </span>
                        </div>

                        <div class="sk-switch-inline d-flex align-items-center pt-10px text-left pl-1">
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" name="rfid_filtered">
                                <span class="d-block mb-1 fs-10"></span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="row gutters-5 pt-15px justify-content-end">

                    <input type="hidden" value="" name="form_type" id="form_type">

                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="button" class="btn btn-primary"
                                    onclick="filter_form()">{{ translate('Filter') }}</button>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="button" class="btn btn-soft-primary w-200px" onclick="excel_export()"
                                    id="downloadexcel">
                                {{translate('Export to Excel')}}
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-header d-block d-lg-flex">
            <h5 class="mb-0 h6">{{translate('Customers')}}</h5>
            <div class="">
                <form class="" id="sort_customers" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <span class="h6">{{translate('Total Customers')}}: </span>
                        <span class="h6 fw-400 pl-5px pr-15px">{{$total_customers}}</span>
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
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($customers as $key => $customer)

                    @if ($customer->user != null)
                        @php
                            $partner_user = \App\PartnershipUser::where('email', $customer->user->email)->first();
                        @endphp
                        <tr>
                            <td>{{ ($key+1) + ($customers->currentPage() - 1)*$customers->perPage() }}</td>
                            <td>@if($customer->user->banned == 1)
                                    <i class="fa fa-ban text-danger" aria-hidden="true"></i>
                                @endif {{$customer->user->name}}</td>
                            <td>{{$customer->user->email}}</td>
                            {{--                            <td>{{$customer->user->phone}}</td>--}}


                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                   href="{{route('customers.view_catering_plans', encrypt($customer->user->id))}}"
                                   title="{{ translate('View Catering Plans') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                <a href="{{route('customers.edit', encrypt($customer->id))}}"
                                   id="customer_code_{{ $customer->id }}"
                                   class="btn btn-soft-primary btn-icon btn-circle btn-sm {{ ($partner_user && $partner_user->accept ? '' : 'd-none') }}"
                                   title="{{ translate('Add/Edit BTMS customer code') }}">
                                    <i class="las la-user-cog"></i>
                                </a>
                                <a href="{{route('customers.login', encrypt($customer->id))}}"
                                   class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                   title="{{ translate('Log in as this Customer') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                @if($customer->user->banned != 1)
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                       onclick="confirm_ban('{{route('customers.ban', $customer->id)}}');"
                                       title="{{ translate('Ban this Customer') }}">
                                        <i class="las la-user-slash"></i>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                       onclick="confirm_unban('{{route('customers.ban', $customer->id)}}');"
                                       title="{{ translate('Unban this Customer') }}">
                                        <i class="las la-user-check"></i>
                                    </a>
                                @endif
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                   data-href="{{route('customers.destroy', $customer->id)}}"
                                   title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <div class="sk-pagination">
                {{ $customers->links() }}
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to unban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
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
        function sort_customers(el) {
            $('#sort_customers').submit();
        }

        function confirm_ban(url) {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href', url);
        }

        function confirm_unban(url) {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href', url);
        }

        function update_pay_on_credit(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.pay_on_credit') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Users Payment Methods updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_pay_on_delivery(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.pay_on_delivery') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Users Payment Methods updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_accept(el) {
            var customer_id = $(el).attr('data-customer');
            console.log('customer_id: ' + customer_id);
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('partnership-user.change-accept') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Partnership Users updated successfully') }}');
                    setTimeout(function () {
                        $('input[name="excluded_vat_' + el.value + '"]').prop('checked', false).parents('label').remove();

                        if (status === 1) {
                            $('#customer_code_' + customer_id).removeClass('d-none');
                        } else {
                            $('#customer_code_' + customer_id).addClass('d-none');
                        }
                    }, 500);
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_excluded_vat(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('customers.excluded_vat') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    SK.plugins.notify('success', '{{ translate('Updated successfully') }}');
                } else {
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function filter_form() {

            $('#form_type').val('filter');
            $("#search_form").attr('target', '');
            $('#search_form').submit();

        }

        function excel_export() {

            $('#form_type').val('export');
            $("#search_form").attr('target', '_blank');

            $('#search_form').submit();

        }

        let old_select = [];

        function all_selected() {

            var new_val = [];

            if (old_select.includes('all')) {

                for (var i = 0; i < ($('#demo-ease').val()).length; i++) {
                    if (i > 0) {
                        new_val.push(($('#demo-ease').val())[i]);
                    }
                }

                $('#demo-ease').val(new_val);
                old_select = $('#demo-ease').val();
                $('#demo-ease').selectpicker('refresh');


            } else if (($('#demo-ease').val()).includes('all')) {
                $('#demo-ease').val(['all']);

                $('#demo-ease').selectpicker('refresh');

                old_select = $('#demo-ease').val();

            }

        }

        $('#demo-ease').on('change', function (e) {

            if (($('#demo-ease').val()).length == 0) {
                $('.filter-option-inner-inner').text('{{translate('Filter By Organisation')}}');
            }

        });

        @if(isset($selected_organisations) && count($selected_organisations)>0)

        $(function () {

            @if(in_array('all', $selected_organisations))
            $('#demo-ease').val(['all']);
            @else

            var values = [];

            @foreach($selected_organisations as $selected_organisation)
            values.push('{{$selected_organisation}}');
            @endforeach
            $('#demo-ease').val(values);
            @endif
        });

        @endif
    </script>
@endsection
