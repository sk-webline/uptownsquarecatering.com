@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0 h6">{{translate('Countries')}}</h3>
        </div>
        <div class="card-body">
            <table class="table sk-table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>{{translate('Name')}}</th>
                        <th data-breakpoints="lg">{{translate('Code')}}</th>
                        <th>{{translate('Show')}}</th>
                        <th>{{translate('VAT Included')}}</th>
                        <th>{{translate('VAT Percentage')}}</th>
                        <th class="text-right" data-breakpoints="lg">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($countries as $key => $country)
                        <tr>
                            <td>{{ ($key+1) + ($countries->currentPage() - 1)*$countries->perPage() }}</td>
                            <td>{{ $country->name }}</td>
                            <td>{{ $country->code }}</td>
                            <td>
                              <label class="sk-switch sk-switch-success mb-0">
                                <input onchange="update_status(this)" value="{{ $country->id }}" type="checkbox" <?php if($country->status == 1) echo "checked";?> >
                                <span class="slider round"></span>
                              </label>
                            </td>
                            <td>
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input onchange="update_inc_vat(this)" value="{{ $country->id }}" type="checkbox" <?php if($country->vat_included == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{ $country->vat_percentage }}%</td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('countries.edit',$country->id)}}" title="{{ translate('Edit') }}">
                                    <i class="las la-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="sk-pagination">
                {{ $countries->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('countries.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('Country status updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_inc_vat(el){
          if(el.checked){
            var status = 1;
          }
          else{
            var status = 0;
          }
          $.post('{{ route('countries.vat_include') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data == 1){
              SK.plugins.notify('success', '{{ translate('Country VAT updated successfully') }}');
            }
            else{
              SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
          });
        }

    </script>
@endsection
