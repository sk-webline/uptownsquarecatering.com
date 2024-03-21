@extends('application.layouts.user_panel')

@section('meta_title'){{ translate('Credit Card') }}@endsection

@section('panel_content')
    <div id="available-balance" class="mb-20px">
        <div class="container">
            <h1 class="fs-19 fw-700 mb-10px">{{translate('Credit Card')}}</h1>
            <div class="bg-login-box rounded-10px px-5px py-15px fs-12 text-black-50 min-h-180px">
                <p>{{translate('Card Nickname')}}: <span class="fw-700 fs-16 text-black">AnnasRevolut</span></p>
                <div class="fs-14 rounded-10px px-5px py-10px bg-light-green text-white mb-3">
                    {{translate('Credit Card')}}: <span class="fw-700">**** **** **** 2356</span>
                </div>
                <p>{{translate('Expiration Date')}}: <span class="fw-700 fs-16 text-black">12/25</span></p>
            </div>
        </div>
    </div>
@endsection
