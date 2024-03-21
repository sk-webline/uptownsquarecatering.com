@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill border-light">
                    @foreach (\App\Language::all() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('faq.edit', ['id'=>$faq->id, 'lang'=> $language->code] ) }}">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                <span>{{$language->name}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <form class="p-4" id="add_form" class="form-horizontal" action="{{ route('faq.update',$faq->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            {{translate('Question')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Question')}}" onkeyup="makeSlug(this.value)" id="title" name="title" value="{{ $faq->getTranslation('title', $lang) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">
                            {{translate('Description')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9">
                            <textarea class="sk-text-editor form-control" data-buttons='[["font", ["bold", "underline", "italic", "clear"]],["para", ["ul", "ol", "paragraph"]],["style", ["style"]],["color", ["color"]],["table", ["table"]],["insert", ["link", "picture", "video"]],["view", ["fullscreen", "codeview", "undo", "redo"]]]' name="description" required>{{ $faq->getTranslation('description', $lang) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">
                            {{translate('Save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
