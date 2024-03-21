@extends('backend.layouts.app')

@section('content')
<div class="sk-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All categories')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <span>{{translate('Add New category')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Categories') }}</h5>
        <form class="" id="sort_categories" action="" method="GET">
            <div class="box-inline pad-rgt pull-left">
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
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{ translate('Parent Category') }}</th>
                    <th data-breakpoints="lg">{{ translate('Products') }}</th>
{{--                    <th data-breakpoints="lg">{{ translate('BTMS Category') }}</th>--}}
{{--                    <th data-breakpoints="lg">{{ translate('BTMS Sub Category') }}</th>--}}
                    <th data-breakpoints="lg">{{ translate('Order Level') }}</th>
                    <th data-breakpoints="lg">{{ translate('Level') }}</th>
                    <th data-breakpoints="lg">{{translate('Banner')}}</th>
                    <th data-breakpoints="lg">{{translate('Icon')}}</th>
                    <th data-breakpoints="lg">{{translate('For Sale')}}</th>
                    <th data-breakpoints="lg">{{translate('Featured')}}</th>
                    <th data-breakpoints="lg">{{translate('Show on Partnership Page')}}</th>
                    <th data-breakpoints="lg">{{translate('Show on Header Menu')}}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                    <tr>
                        <td>{{ ($key+1) + ($categories->currentPage() - 1)*$categories->perPage() }}</td>
                        <td>{{ $category->getTranslation('name') }}</td>
                        <td>
                            @php
                                $parent = \App\Category::where('id', $category->parent_id)->first();
                            @endphp
                            @if ($parent != null)
                                {{ $parent->getTranslation('name') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $category->countProducts() }}</td>
{{--                        @php--}}
{{--                            $btms_category_title = '';--}}
{{--                            $btms_subcategory_title = '';--}}
{{--                            if (!empty($category->btms_category_code)) {--}}
{{--                                if ($category->btms_category_level == 3) {--}}
{{--                                    $btms_subcategory = \App\Models\Btms\ItemSubCategory::where('Category Code', $category->btms_category_code)->first();--}}
{{--                                    if (!empty($btms_subcategory)) {--}}
{{--                                        $btms_subcategory_title = $btms_subcategory->{'Name'}." (".$category->btms_category_code.")";--}}
{{--                                        /* Find Category */--}}
{{--                                        $btms_category = \App\Models\Btms\ItemCategory::where('Category Code', $btms_subcategory->{'Related Category Code'})->first();--}}
{{--                                        if (!empty($btms_category)) {--}}
{{--                                            $btms_category_title = $btms_category->{'Name'}." (".$btms_category->{'Category Code'}.")";--}}
{{--                                        }--}}
{{--                                    }--}}
{{--                                }--}}
{{--                                if ($category->btms_category_level == 2) {--}}
{{--                                    $btms_category = \App\Models\Btms\ItemCategory::where('Category Code', $category->btms_category_code)->first();--}}
{{--                                    if (!empty($btms_category)) {--}}
{{--                                        $btms_category_title = $btms_category->{'Name'}." (".$category->btms_category_code.")";--}}
{{--                                    }--}}
{{--                                }--}}
{{--                            }--}}
{{--                        @endphp--}}
{{--                        <td>{{ $btms_category_title }}</td>--}}
{{--                        <td>{{ $btms_subcategory_title }}</td>--}}
                        <td>{{ $category->order_level }}</td>
                        <td>{{ $category->level }}</td>
                        <td>
                            @if($category->banner != null)
                                <img src="{{ uploaded_asset($category->banner) }}" alt="{{translate('Banner')}}" class="h-50px">
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($category->icon != null)
                                <span class="avatar avatar-square avatar-xs">
                                    <img src="{{ uploaded_asset($category->icon) }}" alt="{{translate('icon')}}">
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" onchange="update_for_sale(this)" value="{{ $category->id }}" <?php if($category->for_sale == 1) echo "checked";?>>
                                <span></span>
                            </label>
                        </td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" onchange="update_featured(this)" value="{{ $category->id }}" <?php if($category->featured == 1) echo "checked";?>>
                                <span></span>
                            </label>
                        </td>
                        <td>
                            <label class="sk-switch sk-switch-success mb-0">
                                <input type="checkbox" onchange="update_b2b(this)" value="{{ $category->id }}" <?php if($category->show_b2b == 1) echo "checked";?>>
                                <span></span>
                            </label>
                        </td>
                        <td>
                            @if ($parent == null)
                                <label class="sk-switch sk-switch-success mb-0">
                                    <input type="checkbox" onchange="update_header_menu(this)" value="{{ $category->id }}" <?php if($category->show_header == 1) echo "checked";?>>
                                    <span></span>
                                </label>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($category->parent_id==0)
                                <a class="btn btn-soft-secondary btn-icon btn-circle btn-sm" href="{{route('categories.brands_edit', ['id'=>$category->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Brand Descriptions Edit') }}">
                                    <i class="la la-gear"></i>
                                </a>
                            @endif
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('categories.edit', ['id'=>$category->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('categories.destroy', $category->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="sk-pagination">
            {{ $categories->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function update_for_sale(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('categories.forsale') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    SK.plugins.notify('success', '{{ translate('For sale categories updated successfully') }}');
                }
                else{
                    SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
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
        function update_b2b(el){
          if(el.checked){
            var status = 1;
          }
          else{
            var status = 0;
          }
          $.post('{{ route('categories.show_b2b') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data == 1){
              SK.plugins.notify('success', '{{ translate('B2B categories updated successfully') }}');
            }
            else{
              SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
          });
        }

        function update_header_menu(el){
          if(el.checked){
            var status = 1;
          }
          else{
            var status = 0;
          }
          $.post('{{ route('categories.show_header') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data == 1){
              SK.plugins.notify('success', '{{ translate('B2B categories updated successfully') }}');
            }
            else{
              SK.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
          });
        }
    </script>
@endsection
