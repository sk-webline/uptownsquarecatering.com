@extends('frontend.layouts.app')

@section('content')
    <div id="home-header">
        <div class="container-left">
            <div class="py-50px py-md-90px py-xxl-130px">
                <div class="row align-items-end">
                    <div class="col-12 col-lg-55per">
                        <div class="home-header-grey py-35px pt-md-65px pt-xxl-100px pb-md-50px pb-xxl-70px">
                            <h1 class="text-secondary fs-14 md-fs-16 fw-700 mb-15px mb-md-40px" data-aos="fade-left">{{toUpper(translate('Embrace Cashless Convenience'))}}</h1>
                            <h2 class="fs-33 md-fs-55 xxl-fs-78 fw-300 l-space-02 m-0" data-aos="fade-left" data-aos-delay="300">
                                <span class="fw-700">{{translate('Eat')}}</span> <span class="opacity-80">{{translate('Smart')}}</span> <br>
                                <span class="fw-700">{{translate('Pay')}}</span> <span class="opacity-80">{{translate('Effortlessly')}}</span>
                            </h2>
                        </div>
                        <a href="{{ route('card.register_new_card') }}" class="btn btn-outline-secondary btn-b-width-2 mt-20px mt-md-30px mt-xxl-45px mb-30px mb-md-75px mb-xxl-125px fs-15 md-fs-17 xxl-fs-20 l-space-02 fw-500 p-10px py-md-13px" data-aos="fade-left" data-aos-delay="600">{{toUpper(translate('Register your Card'))}}</a>
                    </div>
                    <div class="col-9 ml-auto col-lg-45per z-1">
                        <img class="img-fit" src="{{static_asset('assets/img/hand.png')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col-12 col-lg-55per">
                    <div class="container-right pr-lg-0" data-aos="fade-right">
                        <div class="border-top border-width-2 border-tertiary-200 line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="my-75px my-sm-100px my-md-150px my-xxl-225px text-black-50 fs-18 md-fs-24 xxl-fs-30" data-aos="fade-up">
        <div class="container">
            <h2 class="text-secondary fs-14 md-fs-16 fw-700 mb-10px mb-md-20px mb-xxl-35px">{{toUpper(translate('Unleash the Cashless Meal Revolution'))}}</h2>
            <div class="lh-2 mw-900px">
                <p class="m-0">{{translate("Where convenience meets culinary delight! As pioneers in cashless meals, we've designed the perfect platform for schools and businesses, bringing you scrumptious snacks and lunches with an effortless touch…")}}</p>
                <p class="text-black fw-700">{{translate('The cashless way.')}}</p>
            </div>
        </div>
    </div>
    <div class="mb-55px mb-sm-100px mb-md-175px mb-xxl-300px fs-14 md-fs-16 xl-fs-14 xxxl-fs-17 text-black-50">
        <div class="container">
            <div class="row xxl-gutters-35">
                <div class="col-xl-4" data-aos="fade">
                    <div class="border-top border-tertiary-200 border-width-2 py-20px py-md-35px py-lg-50px">
                        <div class="row md-gutters-50 xl-gutters-15 xxxl-gutters-50 align-items-center align-items-xl-stretch">
                            <div class="col col-100px col-md-250px col-xl-100px col-xxxl-250px">
                                <img class="h-70px h-md-120px h-xl-70px h-xxxl-120px" src="{{static_asset('assets/img/icons/step-1.svg')}}" alt="">
                            </div>
                            <div class="col col-grow-100px col-md-grow-250px col-xl-grow-100px col-xxxl-grow-250px lh-1-4 md-lh-1-6">
                                <h3 class="text-black fs-14 md-fs-16 xl-fs-14 xxxl-fs-16 fw-700">01. {{toUpper(translate('Register Your Card'))}}</h3>
                                <p>{{translate('Join the cashless revolution! Sign up and link your RFID bracelet to our platform effortlessly.')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4" data-aos="fade" data-aos-delay="300">
                    <div class="border-top border-tertiary-200 border-width-2 py-20px py-md-35px py-lg-50px">
                        <div class="row md-gutters-50 xl-gutters-15 xxxl-gutters-50 align-items-center align-items-xl-stretch">
                            <div class="col col-100px col-md-250px col-xl-100px col-xxxl-250px">
                                <img class="h-60px h-md-120px h-xl-60px h-xxxl-120px" src="{{static_asset('assets/img/icons/step-2.svg')}}" alt="">
                            </div>
                            <div class="col col-grow-100px col-md-grow-250px col-xl-grow-100px col-xxxl-grow-250px lh-1-4 md-lh-1-6">
                                <h3 class="text-black fs-14 md-fs-16 xl-fs-14 xxxl-fs-16 fw-700">02. {{toUpper(translate('Choose your Meal Plan'))}}</h3>
                                <p>{{translate('Explore a variety of meal plans, expertly curated for students and professionals.')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4" data-aos="fade" data-aos-delay="600">
                    <div class="border-top border-tertiary-200 border-width-2 py-20px py-md-35px py-lg-50px">
                        <div class="row md-gutters-50 xl-gutters-15 xxxl-gutters-50 align-items-center align-items-xl-stretch">
                            <div class="col col-100px col-md-250px col-xl-100px col-xxxl-250px">
                                <img class="h-70px h-md-120px h-xl-70px h-xxxl-120px" src="{{static_asset('assets/img/icons/step-3.svg')}}" alt="">
                            </div>
                            <div class="col col-grow-100px col-md-grow-250px col-xl-grow-100px col-xxxl-grow-250px lh-1-4 md-lh-1-6">
                                <h3 class="text-black fs-14 md-fs-16 xl-fs-14 xxxl-fs-16 fw-700">03. {{toUpper(translate('Top up Effortlessly'))}}</h3>
                                <p>{{translate("Keep your RFID loaded and ready for delightful dining experiences. With secure and quick top-up  options, you'll never miss a meal.")}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-top border-tertiary-200 border-width-2 d-xl-none" data-aos="fade-up"></div>
            <a href="{{ route('card.register_new_card') }}" class="btn btn-outline-secondary btn-b-width-2 btn-block fs-15 md-fs-17 xxl-fs-20 l-space-02 fw-500 p-10px py-md-13px mt-45px mt-md-85px mt-xxl-125px" data-aos="fade-up"><span class="d-none d-md-inline">{{toUpper(translate('Start Today by Registering your Card'))}}</span><span class="d-md-none">{{toUpper(translate('Register your Card'))}}</span></a>
        </div>
    </div>
@endsection
