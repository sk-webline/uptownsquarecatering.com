@extends('application.layouts.app')

@section('meta_title'){{ ucwords(translate('History')) }}@endsection

@section('content')

    @php

        use Illuminate\Support\Facades\Session;

        $user = auth()->guard('application')->user(); // canteen user
        $rfid_card = $user->card;
        $organisation = $rfid_card->organisation;

        $orders = \App\Models\AppOrder::where('user_id', $user->id)->get();


    @endphp
    <div id="history" class="mt-20px">
        <div class="container">
            <h1 class="fs-14 fw-300 text-black-50 border-bottom border-black-100 pb-5px mb-20px">{{toUpper(translate('History'))}}</h1>
        </div>
        <div class="history-table-container">
            <div class="bg-login-box pb-20px overflow-hidden">
            <div class="container">
                <div class="custom-table">
                    <div class="header">
                        <div class="tr">
                            <div class="row gutters-5">
                                <div class="col td">{{toUpper(translate('Order Date'))}}</div>
                                <div class="col td">{{toUpper(translate('Order Code'))}}</div>
                                <div class="col-60px td">{{toUpper(translate('Cost'))}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="body">

                        @if(count($orders)>0)
                            @foreach($orders as $key => $order)
                                <div class="tr" data-orderID="{{$order->id}}">
                                    <div class="row gutters-5">
                                        <div class="col td">{{ \Carbon\Carbon::create($order->created_at)->format('d/m/Y') }}</div>
                                        <div class="col td">{{$order->code}}</div>
                                        <div class="col-60px td">{{single_price($order->grand_total)}}</div>
                                    </div>
                                </div>

                            @endforeach
                        @else
                            <div class="tr no-results">
                                {{translate('There is no order history.')}}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
            <div class="history-table-sticky"></div>
        </div>
    </div>
@endsection

@section('modal')
{{--   @include('application.modals.history_modal')--}}

   <div id="history-popup" class="bottom-popup">
       <div class="bottom-popup-scroll no-scroll c-scrollbar">
           <div class="bottom-popup-container py-40px">
               <div class="bottom-popup-close">
                   <svg class="h-15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                       <use xlink:href="{{static_asset('assets/img/icons/close-icon.svg')}}#content"></use>
                   </svg>
               </div>
               <div class="container">

               </div>
           </div>
       </div>
   </div>

@endsection

@section('script')
    <script>
        $(document).on('click', '.custom-table .body .tr:not(.no-results)', function (){
            $('html,body').addClass('bottom-popup-opened');
            setScrollHeightOnBottomPopup();
            $('#history-popup').addClass('active');

            $('#history-popup .container').addClass('loader');

            var order_id = $(this).attr('data-orderID');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('application.order_details')}}",
                type: 'post',
                data: {
                    order_id: order_id
                },
                success: function (data) {

                    // console.log('response: ', data);

                    // var data = JSON.parse(response);

                    if (data.status == 1) {

                        $('#history-popup .container').removeClass('loader');
                        $('#history-popup .container').html(data.view);


                    } else if (data.status == 0) {

                    }

                }
            });




        });
    </script>
@endsection
