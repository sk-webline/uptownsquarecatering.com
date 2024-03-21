
<html>
<head>

    <?php use Carbon\Carbon;
    use App\Models\Card;
    use App\Models\CanteenPurchase;
    use App\Models\Organisation;

//    $rowsPerPage = 70; // Set the maximum number of rows per page
//    $rowCount = ceil(count($purchases));
//    $pageCount = ceil($rowCount / $rowsPerPage);

    $start1 = null;
    $start2 = null;


    $carbon_break_date = Carbon::create($carbon_date->format('Y-m-d') . ' ' . $break->hour_from);
    $show_receive_time = false;

    if($carbon_break_date->lte(Carbon::now()) || true){
        $show_receive_time = true;
    }

//    dd($purchases);

    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--    <title>{{ config('app.name') }} Order: #{{$order->code}}</title>--}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-adds.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/canteen_cashier.css') }}">

    <style media="all">
        @page {
            margin-top: 10mm; /* Set your desired top margin in millimeters */
            margin-right: 10mm; /* Set your desired right margin in millimeters */
            margin-bottom: 10mm; /* Set your desired bottom margin in millimeters */
            margin-left: 10mm; /* Set your desired left margin in millimeters */
        }

        body {
            font-size: 0.875rem;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
            padding-top: 10px;
            /*margin: 0;*/
            margin-top: 10mm; /* Set your desired top margin in millimeters */
            margin-right: 10mm; /* Set your desired right margin in millimeters */
            margin-bottom: 10mm; /* Set your desired bottom margin in millimeters */
            margin-left: 10mm; /* Set your desired left margin in millimeters */

        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: <?php echo  $text_align ?>;
        }

        .text-right {
            text-align: <?php echo  $not_text_align ?>;
        }

        .text-center{
            text-align: center!important;
        }

        .d-block{
            display: block;
        }

        table{
            width: 100%!important;
            border-collapse: collapse;
            padding: 2px 7px;
            border: 1px solid black;
            /*display: inline-block!important;*/
            /*display: inline-block;*/
        }

        th, td{
            border: 1px solid black;
            padding: 3px;
        }


    </style>
</head>
<body>
<div class="py-20px fs-17 text-black px-30px">
    <div class="text-center fs-20">
        <h4>Meal Codes Report {{$carbon_date->format('d/m/Y')}} - {{ordinal($break->break_num)}} {{translate('Break')}}</h4>
    </div>

    <div class="text-right">
        <span class="d-block">
            <span class="fw-600"> {{translate('Break Time')}}: </span> {{substr($break->hour_from, 0, 5) . ' - ' . substr($break->hour_to, 0, 5)}} <br>
            <span class="fw-600"> {{translate('Organisation')}}: </span> {{$organisation->name}}
        </span>
    </div>
{{--    @for ($page = 1; $page <= $pageCount; $page++)--}}

{{--        @if ($page > 1)--}}
{{--            <div class="page-break"></div>--}}
{{--        @endif--}}

{{--        @php--}}

{{--            if($page == 1){--}}
{{--                $start1 = 0 ;--}}
{{--            }else{--}}
{{--                $start1= (($page-1) * $rowsPerPage);--}}
{{--            }--}}

{{--            $end1 = $start1+$rowsPerPage;--}}

{{--            if($end1>count($purchases)){--}}
{{--               $end1 = count($purchases);--}}
{{--            }--}}

{{--        @endphp--}}



    <div style="display: flex; color: black; padding-top: 10px">

        <table style="width: 100%; margin-top: 50px">

            <thead>
            <tr>
                <th width="35" class="text-center fw-600"><h4>#</h4></th>
                <th class="text-left fw-600" style="width: 25%"><h4>{{translate('Customer Name')}}</h4></th>
                <th class="fw-500"><h4>{{translate('Meal Code')}}</h4></th>
                <th class="fw-500"><h4>{{translate('Qty')}}</h4></th>
                <th class="fw-500" style="width: 22%"><h4>{{translate('Meal Items')}}</h4></th>
                @if($show_receive_time)
                    <th class="fw-500"><h4>{{translate('Received Time')}}</h4></th>
                @endif
            </tr>
            </thead>
            <tbody>

            @for($i=0; $i<count($purchases); $i++)

                @php

                    if($i-1 >= 0){

                        if ($purchases[$i-1]->canteen_app_user_id != $purchases[$i]->canteen_app_user_id){
                            if($purchases[$i]->card_name !=null){
                                $name = $purchases[$i]->card_name;
                            }else{
                                $name = $purchases[$i]->username;
                            }
                        }else{
                            $name = '';
                        }

                    }else{
                        if($purchases[$i]->card_name !=null){
                            $name = $purchases[$i]->card_name;
                        }else{
                            $name = $purchases[$i]->username;
                        }
                    }
                @endphp

                <tr>
                    <td class="text-center">{{$i+1}}</td>
                    <td class="text-left fw-600" style="width: 25%">
                        {{$name}}
                    </td>
                    <td class="text-center">{{$purchases[$i]->meal_code}}</td>
                    <td class="text-center">{{$purchases[$i]->total_quantity}}</td>
                    <td class="text-center" style="width: 22%">{{$purchases[$i]->product_name}}</td>
                    @if($show_receive_time)
                        @php
                            $delivery = \App\Models\CanteenPurchaseDelivery::select('canteen_purchase_deliveries.canteen_app_user_id', 'canteen_purchase_deliveries.created_at', 'canteen_purchases.id', 'canteen_purchases.meal_code')
                            ->join('canteen_purchases', 'canteen_purchases.id', '=', 'canteen_purchase_deliveries.canteen_purchase_id')
                            ->where('canteen_purchases.date', '=', $carbon_date->format('Y-m-d'))
                            ->where('canteen_purchases.break_num', '=', $break->break_num)
                            ->where('canteen_purchases.canteen_setting_id', $canteen_setting->id)
                            ->where('canteen_purchases.canteen_app_user_id', $purchases[$i]->canteen_app_user_id)
                            ->first();

//                            dd($delivery, $carbon_date->format('Y-m-d'), $break->break_num,  $canteen_setting->id, $purchases[$i]->canteen_app_user_id);
                        @endphp

                        <td class="text-center">
                            @if($delivery!=null)
                                {{substr($delivery->created_at, 0, strlen($delivery->created_at)-3)}}
                            @else
                                -
                            @endif
                        </td>
                    @endif
                </tr>

            @endfor
            </tbody>
        </table>


    </div>
{{--    @endfor--}}

</div>
</body>
</html>

