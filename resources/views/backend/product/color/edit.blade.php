@extends('backend.layouts.app')

@section('content')

<div class="sk-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Color Information')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="p-4" action="{{ route('colors.update', $color->id) }}" method="POST">
                <input name="_method" type="hidden" value="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">
                        {{ translate('Name')}}
                    </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{ translate('Name')}}" id="name" name="name" class="form-control" required value="{{ $color->name }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="code">
                        {{ translate('Color Code')}}
                    </label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{ translate('Color Code')}}" id="color-code-input" name="code" class="form-control" value="{{ $color->code }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="color_image">{{translate('Color Image')}} <small>({{ translate('Height: 80px, Width: 80px') }})</small></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="skuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="color_image" value="{{$color->image}}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
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
                    },
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
