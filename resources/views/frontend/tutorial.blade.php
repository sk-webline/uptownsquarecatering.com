@extends('frontend.layouts.app')

@section('meta_title'){{ translate('Tutorial') }}@stop

@section('content')
    <div id="tutorial" class="mt-50px mt-md-85px mt-xl-125px mb-100px mb-md-175px mb-xl-250px">
        <div class="container">
            <div class="mx-auto mw-1330px">
                <h1 class="fs-14 sm-fs-20 lg-fs-25 fw-700 mb-10px lh-1">{{toUpper(translate('I need Help'))}}</h1>
                <p class="fs-14 sm-fs-16 text-primary-50">{{toUpper(translate('Find clear solutions to common queries and discover easy-to-follow instructions'))}}</p>
                <div class="border-top border-width-2 border-primary-100 mt-20px mt-sm-30px"></div>
                <div class="tutorial-res-item" data-id="1">
                    <div class="tutorial-res-title">
                        1) How to register my child's RFID?
                    </div>
                    <div class="tutorial-res-description">
                        <p>Before you begin, make sure you have purchased the RFID card from your child's school.</p>
                        <p>- Click the <img class="h-15px h-md-17px align-baseline" src="{{static_asset('assets/img/tutorials/user.svg')}}" alt=""> icon on the header and start typing the RFID from the card you have purchased at school, then click the <strong>"SUBMIT"</strong> button. After the first 6 digits are entered the submit button will appear.</p>
                        <div class="mw-745px">
                            <div class="tutorial-res-image">
                                <img class="d-none d-md-block" src="{{static_asset('assets/img/tutorials/1.jpg')}}" alt="">
                                <img class="d-md-none" src="{{static_asset('assets/img/tutorials/1-res.jpg')}}" alt="">
                            </div>
                        </div>
                        <p>- If your RFID number is correct, you can proceed to create your account by clicking <strong>“CREATE ACCOUNT”</strong>. Fill all the appearing fields, agree with the policies and complete the process by clicking <strong>“REGISTER YOUR CARD & CREATE ACCOUNT”</strong></p>
                        <p>- If you already have an account, continue by logging in.</p>
                        <div class="mw-745px">
                            <div class="row gutters-20">
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/2.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/3.jpg')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-res-item" data-id="2">
                    <div class="tutorial-res-title">
                        2) How to create account?
                    </div>
                    <div class="tutorial-res-description">
                        <p>To register on our platform, owning an RFID code is necessary. This unique code is exclusively available in the devices sold by the schools or the RFID codes which we send for Heritage ASK students and the Island nursery. </p>
                        <p>Once you acquire the RFID card, bracelet, key fob or code you can seamlessly proceed to both submit your RFID code and create your account simultaneously by following the steps in the question 1.</p>
                    </div>
                </div>
                <div class="tutorial-res-item" data-id="3">
                    <div class="tutorial-res-title">
                        3) How to login to my account?
                    </div>
                    <div class="tutorial-res-description">
                        <p>Simply click on the <img class="h-15px h-md-17px align-baseline" src="{{static_asset('assets/img/tutorials/user.svg')}}" alt=""> icon and enter your email and password in the designated fields. If you don’t have email and password, you need to follow the steps in question 1.</p>
                        <div class="mw-745px">
                            <div class="row gutters-20">
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/4.jpg')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-res-item" data-id="4">
                    <div class="tutorial-res-title">
                        4) How to purchase a Meal Plan?
                    </div>
                    <div class="tutorial-res-description">
                        <p>After purchasing your RFID card from our school, submitting the unique code, and setting up your account, you're all set to select your meal plan. Access your created account by simply clicking on the <img class="h-15px h-md-17px align-baseline" src="{{static_asset('assets/img/tutorials/user.svg')}}" alt=""> icon and entering your email and password in the designated fields.</p>
                        <div class="mw-745px">
                            <div class="row gutters-20">
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/5.jpg')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p>- From your dashboard at your account simply click the big blue button <strong>"ADD NEW SUBSCRIPTION"</strong>. You will be directed to the meal packages page, where you can choose your plan by clicking <strong>"ADD TO CART"</strong> and then <strong>"PROCEED TO CHECKOUT"</strong></p>
                        <div class="mw-1000px">
                            <div class="tutorial-res-image">
                                <img class="d-none d-md-block" src="{{static_asset('assets/img/tutorials/6.jpg')}}" alt="">
                                <img class="d-md-none" src="{{static_asset('assets/img/tutorials/6-res.jpg')}}" alt="">
                            </div>
                        </div>
                        <div class="mw-1000px">
                            <div class="row gutters-20">
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/7.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="tutorial-res-image">
                                        <img src="{{static_asset('assets/img/tutorials/8.jpg')}}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p>- Finally, after accepting the policies, click <strong>"CONTINUE TO PAYMENT"</strong>. Complete the payment process by entering your card information, and your meal plan will be purchased and ready to use!</p>
                        <div class="mw-1000px">
                            <div class="tutorial-res-image">
                                <img class="d-none d-md-block" src="{{static_asset('assets/img/tutorials/9.jpg')}}" alt="">
                                <img class="d-md-none" src="{{static_asset('assets/img/tutorials/9-res.jpg')}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-res-item" data-id="5">
                    <div class="tutorial-res-title">
                        5) How to purchase an extra Meal Plan?
                    </div>
                    <div class="tutorial-res-description">
                        <p>To extend your subscription beyond the current period, follow these straightforward steps:</p>
                        <p>Simply click the prominent <strong>"ADD NEW SUBSCRIPTION"</strong> button.</p>
                        <div class="mw-1000px">
                            <div class="tutorial-res-image">
                                <img class="d-none d-md-block" src="{{static_asset('assets/img/tutorials/10.jpg')}}" alt="">
                                <img class="d-md-none" src="{{static_asset('assets/img/tutorials/10-res.jpg')}}" alt="">
                            </div>
                        </div>
                        <p>Then, proceed by following the purchase steps outlined in Question 4: <strong>"ADD TO CART"</strong> ,<strong>"PROCEED TO CHECKOUT"</strong> and finally, <strong>"CONTINUE TO PAYMENT"</strong></p>
                    </div>
                </div>
                @if(false)
                    <div class="tutorial-res-item" data-id="6">
                        <div class="tutorial-res-title">
                            6) How to make your own plan?
                        </div>
                        <div class="tutorial-res-description">
                            <p>To customize your meal plan according to specific dates, desired snacks, and the number of lunches, follow these simple steps:</p>
                            <p>- Begin by selecting your preferred dates using the <img class="h-15px h-md-17px align-baseline" src="{{static_asset('assets/img/tutorials/calendar.svg')}}" alt=""> icon. After choosing your dates, click <strong>"SELECT DATES"</strong></p>
                            <p>- Once you've finalized the dates, indicate the quantity of snacks and meals required, then click <strong>"ADD TO CART"</strong></p>
                            <p>- Proceed to the checkout by clicking <strong>"PROCEED TO CHECKOUT"</strong></p>
                            <div class="row justify-content-between">
                                <div class="col-sm-auto">
                                    <div class="tutorial-res-image">
                                        <img class="img-contain h-sm-210px" src="{{static_asset('assets/img/tutorials/11.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="tutorial-res-image">
                                        <img class="img-contain h-sm-210px" src="{{static_asset('assets/img/tutorials/12.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="tutorial-res-image">
                                        <img class="img-contain h-sm-210px" src="{{static_asset('assets/img/tutorials/13.jpg')}}" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="tutorial-res-image">
                                        <img class="img-contain h-sm-210px" src="{{static_asset('assets/img/tutorials/14.jpg')}}" alt="">
                                    </div>
                                </div>
                            </div>
                            <p>Ensure to agree with the Terms & Policies by checking the checkbox and then click <strong>"CONTINUE TO PAYMENT"</strong> to complete your order.</p>
                            <div class="mw-1000px">
                                <div class="tutorial-res-image">
                                    <img class="d-none d-md-block" src="{{static_asset('assets/img/tutorials/15.jpg')}}" alt="">
                                    <img class="d-md-none" src="{{static_asset('assets/img/tutorials/15-res.jpg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-res-item" data-id="7">
                        <div class="tutorial-res-title">
                            7) I have lost my bracelet or key fob, what can i do now?
                        </div>
                        <div class="tutorial-res-description">
                            <p>In case the RFID bracelet or fob has been lost, it is important to buy a new one as soon as possible and you will need to update the RFID Number on your account, on your already paid subscription.</p>
                            <p>To do so, log in to your account and edit the RFID Number on the correct subscription by clicking on the PENCIL, as shown in the photo below. Delete the old number and write the new number.</p>
                            <p>Please make sure you DO NOT register the new RFID bracelet/fob as a new card!</p>
                            <div class="mw-1000px">
                                <div class="tutorial-res-image">
                                    <img src="{{static_asset('assets/img/tutorials/16.jpg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-res-item" data-id="8">
                        <div class="tutorial-res-title">
                            8) What is the difference between Early years, Lower primary and Upper primary.
                        </div>
                        <div class="tutorial-res-description">
                            <ul>
                                <li>Early years - is Nursery to Reception</li>
                                <li>Lower primary - is year 1-3</li>
                                <li>Upper primary - is year 4-6</li>
                            </ul>
                            <p>Early years, Lower primary and Upper primary classes for ASK Heritage school</p>
                        </div>
                    </div>
                @else
                    <div class="tutorial-res-item" data-id="7">
                        <div class="tutorial-res-title">
                            6) I have lost my bracelet or key fob, what can i do now?
                        </div>
                        <div class="tutorial-res-description">
                            <p>In case the RFID bracelet or fob has been lost, it is important to buy a new one as soon as possible and you will need to update the RFID Number on your account, on your already paid subscription.</p>
                            <p>To do so, log in to your account and edit the RFID Number on the correct subscription by clicking on the PENCIL, as shown in the photo below. Delete the old number and write the new number.</p>
                            <p>Please make sure you DO NOT register the new RFID bracelet/fob as a new card!</p>
                            <div class="mw-1000px">
                                <div class="tutorial-res-image">
                                    <img src="{{static_asset('assets/img/tutorials/16.jpg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-res-item" data-id="8">
                        <div class="tutorial-res-title">
                            7) What is the difference between Early years, Lower primary and Upper primary.
                        </div>
                        <div class="tutorial-res-description">
                            <ul>
                                <li>Early years - is Nursery to Reception</li>
                                <li>Lower primary - is year 1-3</li>
                                <li>Upper primary - is year 4-6</li>
                            </ul>
                            <p>Early years, Lower primary and Upper primary classes for ASK Heritage school</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('click', '.tutorial-res-title', function (){
            var parent = $(this).parent('.tutorial-res-item');
            $('.tutorial-res-title').not(this).parent('.tutorial-res-item').removeClass('active');
            parent.toggleClass('active');
            $('html, body').animate({
                scrollTop: parent.offset().top - 70
            }, 500);
        });

        @if(isset($_GET['question']))
            $('.tutorial-res-item[data-id="{{$_GET['question']}}"] .tutorial-res-title').trigger('click');
        @endif
    </script>
@endsection
