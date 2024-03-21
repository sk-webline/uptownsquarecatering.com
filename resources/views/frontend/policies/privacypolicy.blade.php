@extends('frontend.layouts.app')

@section('content')
@php
    $privacy_policy =  \App\Page::where('type', 'privacy_policy_page')->first();
@endphp
<section class="my-50px my-md-100px">
    <div class="container">
        <h1 class="fs-24 md-fs-35 xl-fs-42 xxl-fs-53 fw-600 text-primary mb-50px mb-md-100px text-center">{{ $privacy_policy->getTranslation('title') }}</h1>
        <div class="text-left">
            @php
                echo $privacy_policy->getTranslation('content');
            @endphp
        </div>
    </div>
</section>
@endsection
