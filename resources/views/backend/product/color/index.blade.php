@extends('backend.layouts.app')

@section('content')

    <div class="sk-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('All Colors') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="@if(auth()->user()->can('add_color')) col-lg-7 @else col-lg-7 @endif">
            <div class="card">
                <form class="" id="sort_colors" action="" method="GET">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Colors') }}</h5>
                        <div class="col-md-5">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm" id="search" name="search"
                                    @isset($sort_search) value="{{ $sort_search }}" @endisset
                                    placeholder="{{ translate('Type color name & Enter') }}">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-body">
                    <table class="table sk-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('ID') }}</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('BTMS Code') }}</th>
                                <th>{{ translate('Code') }}</th>
                                <th>{{ translate('Image') }}</th>
                                <th class="text-right">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colors as $key => $color)
                                <tr>
                                    <td>{{ ($key+1) + ($colors->currentPage() - 1)*$colors->perPage() }}</td>
                                    <td>{{ $color->id }}</td>
                                    <td>{{ $color->name }}</td>
                                    <td>{{ $color->accounting_code }}</td>
                                    <td>{{ $color->code }}</td>
                                    <td>
                                        @if($color->image)
                                            <img src="{{ uploaded_asset($color->image) }}" class="h-50px">
                                        @endif
                                    </td>
                                    <td class="text-right">
                                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                href="{{ route('colors.edit', ['id' => $color->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                                title="{{ translate('Edit') }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                data-href="{{ route('colors.destroy', $color->id) }}"
                                                title="{{ translate('Delete') }}">
                                                <i class="las la-trash"></i>
                                            </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="sk-pagination">
                        {{ $colors->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @if(hasAccessOnContent())
            <div class="col-md-5 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Add New Color') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('colors.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name">{{ translate('Name') }}</label>
                                <input type="text" placeholder="{{ translate('Name') }}" id="name" name="name"
                                    class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="name">{{ translate('Color Code') }}</label>
                                <input type="text" placeholder="{{ translate('Color Code') }}" id="color-code-input" name="code"
                                    class="form-control" value="{{ old('code') }}" >
                            </div>
                            <div class="form-group mb-3">
                                <label for="color_image">{{translate('Color Image')}} <small>({{ translate('Height: 80px, Width: 80px') }})</small></label>
                                <div class="input-group" data-toggle="skuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="color_image" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>


@endsection

@section('script')
    <script type="text/javascript">

        "use strict";

        (() => {
            //  color pickr code
            // Simple example, see optional options for more configuration.
            window.setColorPicker = (elem, defaultValue) => {
                elem = document.querySelector(elem);
                let pickr = Pickr.create({
                    el: elem,
                    default: defaultValue,
                    theme: 'nano', // or 'monolith', or 'nano'
                    useAsButton: true,
                    swatches: [
                        '#217ff3',
                        '#11cdef',
                        '#fb6340',
                        '#f5365c',
                        '#f7fafc',
                        '#212529',
                        '#2dce89'
                    ],
                    components: {
                        // Main components
                        preview: true,
                        opacity: true,
                        hue: true,
                        // Input / output Options
                        interaction: {
                            hex: true,
                            rgba: false,
                            // hsla: true,
                            // hsva: true,
                            // cmyk: true,
                            input: true,
                            clear: true,
                            silent: true,
                            preview: true,
                        }
                    }
                });
                pickr.on('init', pickr => {
                    elem.value = pickr.getSelectedColor().toHEXA().toString(0);
                }).on('change', color => {
                    elem.value = color.toHEXA().toString(0);
                });

                return pickr;

            }
        })();
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            let themeColor = setColorPicker('#color-code-input', document.querySelector('#color-code-input').value);
        });
    </script>
@endsection
@section('modal')
    @include('modals.delete_modal')
@endsection
