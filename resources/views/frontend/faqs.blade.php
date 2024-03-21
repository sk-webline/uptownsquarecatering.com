@extends('frontend.layouts.app')

@section('meta_title'){{ translate('FAQs') }}@stop

@section('content')
    <div id="faqs" class="my-50px mt-md-125px mb-md-150px">
        <div class="bg-account py-20px py-md-35px l-space-1-2 mb-50px mb-md-125px fs-14 md-fs-16 text-default-50">
            <div class="container">
                <div class="mw-1350px mx-auto">
                    <h1 class="fs-30 md-fs-40 fw-700 text-secondary font-play mb-30px">{{ translate('FAQs') }}</h1>
                    <p class="mw-565px">{{translate('Find the answers you are looking for, if your question does not get answered don’t hesitate to contact us.')}}</p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="mw-1350px mx-auto">
                @foreach(\App\Faq::all() as $faq)
                    <div class="faqs-res-item pb-15px pb-md-25px mb-15px mb-md-25px border-bottom border-width-2 border-default-200">
                        <h2 class="font-play fs-20 md-fs-25 fw-700 mb-10px l-space-1-2 text-secondary">{{$faq->getTranslation('title')}}</h2>
                        <div class="text-default-50 fs-14 md-fs-16">
                            {!! $faq->getTranslation('description') !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection