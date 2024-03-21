@extends('application.layouts.app')

@section('meta_title'){{ ucwords(translate('Upcoming Meals')) }}@endsection

@section('content')



    <div id="upcoming-meals">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 my-15px border-bottom border-black-100 pb-5px">{{toUpper(translate('Upcoming Meals'))}}</h1>
        </div>
        <div class="snack-all-results">
           @include('application.partials.upcoming_meals_table')
        </div>
    </div>
@endsection

@section('modal')
    @include('application.modals.refund_modal')
@endsection

@section('script')
    <script>
        /*Show Quantity Controls*/
        var inactivityTimeout;
        $(document).on('click', '.cart-break-add .added', function (){
            $('.cart-break-add .added').not(this).parent('.cart-break-add').removeClass('open-quantity');
            $(this).parent('.cart-break-add').addClass('open-quantity');
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                $('.cart-break-add').removeClass('open-quantity');
            }, 5000);
        });
        $(document).on('click', '.cart-break-add .quantity .control', function (){
            /*If the quantity of the products change the button will appear*/
            $('.snack-total-cart .btn').show();
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                $('.cart-break-add').removeClass('open-quantity');
            }, 5000);
        });
        $(document).mouseup(function(e) {
            if(!$('.cart-break-add').is(e.target) && $('.cart-break-add').has(e.target).length === 0) {
                $('.cart-break-add').removeClass('open-quantity');
            }
        });




        // Returns a function, that, as long as it continues to be invoked, will not
        // be triggered. The function will be called after it stops being called for
        // N milliseconds. If `immediate` is passed, trigger the function on the
        // leading edge, instead of the trailing.
        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };
    </script>
@endsection
