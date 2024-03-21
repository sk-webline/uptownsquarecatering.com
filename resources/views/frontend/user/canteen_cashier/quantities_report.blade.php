
<html>
<head>

    <?php use Carbon\Carbon;
    use App\Models\Card;
    use App\Models\CateringPlanPurchase;
    use App\Models\OrganisationSetting;
    use App\Models\Organisation;


    $rowsPerPage = 45; // Set the maximum number of rows per page
    $rowCount = ceil(count($canteen_products)/2);
    $pageCount = ceil($rowCount / $rowsPerPage);

    $start1 = null;
    $start2 = null;

    $quantities = [];

    foreach ($purchases as $purchase){
        $quantities[$purchase->canteen_product_id] = $purchase->total_quantity;
    }


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
            /*width: 100%;*/
            padding: 2px 7px;
            /*display: inline-block!important;*/
            /*display: inline-block;*/
        }

        table.borders{
            width: 100%!important;
            border-collapse: collapse;
            padding: 2px 7px;
            border: 1px solid black;
        }

        .borders th, .borders td{
            border: 1px solid black;
            padding: 3px;
        }



    </style>
</head>
<body>
<div class="py-20px fs-17 text-black px-30px">
    <div class="text-center fs-20">
        <h4>Report {{$carbon_date->format('d/m/Y')}} - {{ordinal($break->break_num)}} {{translate('Break')}}</h4>
    </div>

    <div class="text-right">
        <span class="d-block">
            <span class="fw-600"> {{translate('Break Time')}}: </span> {{substr($break->hour_from, 0, 5) . ' - ' . substr($break->hour_to, 0, 5)}} <br>
            <span class="fw-600"> {{translate('Organisation')}}: </span> {{$organisation->name}}
        </span>
    </div>

    <div style="display: flex; color: black;">
        @for ($page = 1; $page <= $pageCount; $page++)

            @if ($page > 1)
                <div class="page-break"></div>
            @endif

        <table style="width: 100%; border-width: 0!important;">
            <tbody>
            <tr>
                <td style="display: flex; justify-content: start!important; vertical-align: top; align-content: start; align-items: start; flex-direction: column ">

                    @php

                        if($page == 1){
                              $start1 = ($page-1)  * $rowsPerPage ;
                        }else{
                            $start1 += (2 * $rowsPerPage);
                        }

                         $end1 = $start1  + $rowsPerPage;

                        if($end1>count($canteen_products)){
                            $end1 = count($canteen_products);
                        }
                        $start2 = $start1 + $rowsPerPage;
                        $end2 = $start2 + $rowsPerPage;

                        if($end2>count($canteen_products)){
                           $end2 = count($canteen_products);
                        }

                    @endphp

                    @if($start1<=count($canteen_products))

                        <table class="borders" @if($start2>count($canteen_products)) style="width: 50%" @else style="width: 95%" @endif>
                            <thead>
                            <tr>
                                <th width="35" class="text-center fw-600"><h4>#</h4></th>
                                <th class="text-left fw-600" style="width: 60%"><h4>{{translate('Product Name')}}</h4></th>
                                <th class="fw-500"><h4>{{translate('Total Quantity')}}</h4></th>
                            </tr>
                            </thead>
                            <tbody>

                            @for($i=$start1; $i<$end1; $i++ )
                                <tr>
                                    <td class="text-center">{{$i+1}}</td>
                                    <td style="width: 60%">{{$canteen_products[$i]->name}}</td>
                                    <td class="text-center">
                                        @if(isset($quantities[$canteen_products[$i]->id]))
                                            {{$quantities[$canteen_products[$i]->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                </tr>


                            @endfor
                            </tbody>
                        </table>
                    @endif
                </td>

                <td style="display: flex; justify-content: start!important; vertical-align: top; align-content: start; align-items: start; flex-direction: column ">

                    @if($start2<=count($canteen_products))
                        <table class="borders" style="width: 95%">
                            <thead>
                            <tr>
                                <th width="35" class="text-center fw-600"><h4>#</h4></th>
                                <th class="text-left fw-600" style="width: 60%"><h4>{{translate('Product Name')}}</h4></th>
                                <th class="fw-500"><h4>{{translate('Total Quantity')}}</h4></th>
                            </tr>
                            </thead>
                            <tbody>

                            @for($i=$start2; $i<$end2; $i++)
                                <tr>
                                    <td class="text-center">{{$i+1}}</td>
                                    <td style="width: 60%">{{$canteen_products[$i]->name}}</td>
                                    <td class="text-center">
                                        @if(isset($quantities[$canteen_products[$i]->id]))
                                            {{$quantities[$canteen_products[$i]->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                </tr>

                            @endfor
                            </tbody>
                        </table>
                    @endif

                </td>

            </tr>
            </tbody>
        </table>
        @endfor

    </div>

</div>
</body>
</html>

