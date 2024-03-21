{{--

$key = 0;

Start_range[$key]
End_range[$key]
Snack_prices
Snack_prices[$key][1]
Snack_prices[$key][2]
Snack_prices[$key][3]
Snack_prices[$key][4]
Meal_prices
meal_prices[$key][1]
meal _prices[$key][2]

$key = 1;

Start_range[1]
End_range[1]
Snack_prices
Snack_prices[1][1]
Snack_prices[1][2]
Snack_prices[][3]
Snack_prices[][4]
Meal_prices
meal_prices[][1]
meal _prices[][2]

start_range[0]
end_range[0]

<!--snack_prices-->
snack_prices[0][1]
snack_prices[0][2]
snack_prices[0][3]
snack_prices[0][4]

<!--meal_prices-->
meal_prices[0][1]
meal _prices[0][2]


foreach ($request->start_range as $key => $start_range) {
    $end_range = $request->End_range[$key];

    foreach ($request->snack_prices[$key] as $key => $snack_price) {

    }

    foreach ($request->meal_prices[$key] as $key => $meal_price) {

    }
}

--}}
@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal" id="formId" action="{{ route('organisation_prices.store', ['organisation_setting_id'=>$organisation_setting->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="card">
                    @csrf

                    <?php

                        $max_snack = $organisation_setting->max_snack_quantity;
                        $max_meal = $organisation_setting->max_meal_quantity;
                        $temp_snack=0;
                        $temp_meal=0;

                    ?>

                    <div class="card-header">
                        <h5 class="mb-0 h6">{{$organisation->name}} > {{translate('Organisation Prices')}}</h5>
                        <div class=" mr-2 text-right">
                            <button type="button" id="duplicate_card_btn" class="btn btn-soft-primary btn-icon btn-circle btn-sm fs-15 p-1 " title="{{ translate('Duplicate card') }}">
                                +
                            </button>

                            <button type="button" id="delete_div_btn" class="btn btn-soft-danger btn-icon btn-circle btn-sm fs-15 p-1 " title="{{ translate('Delete card') }}"> - </button>

                        </div>
                    </div>

                    <div class="card-body" id="card_prices">

                        <div class="col-auto pb-4" id="price_range_card">

                            <div class="form-group row">
                                <label class="col-md-2 col-form-label fs-15 day-range font-weight-bold">{{translate('1. Day Range')}}</label>
                            </div>

                            <div class="form-group row">

                                <div class="col-md-1">
                                    <input type="number" class="form-control px-12px start-day" value="1" name="start_range[]" required readonly>
                                </div>

                                <label class="px-3 my-auto col-form-label text-center font-weight-bold fs-15">{{translate('-')}}</label>

                                <div class="col-md-1 ">
                                    <input type="number" class="form-control px-12px end-day"  name="end_range[]" required>
                                </div>

                            </div>

                            @if($max_snack>0)

                            <div class="form-group row">
                                <label class="col-md-2 col-form-label fs-15  ">{{translate('Snack Prices')}}</label>
                            </div>


                            <div class="row row-cols-6">
                                @while($temp_snack<$max_snack)
                                        <?php $temp_snack = $temp_snack+1; ?>
                                    <div class="col">
                                        <div class="form-group row gutters-10">
                                            <label class="col-md-2 fs-15">{{$temp_snack}}</label>
                                            <div class="col-md-10">
                                                <div class="input-group" >
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium px-1">€</div>
                                                    </div>
                                                    <input type="number" class="form-control px-10px snack-price" step="0.1" name="snack_prices[{{$temp_snack-1}}][]" min="0.1" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                @endwhile
                            </div>

                            @endif




                            @if($max_meal>0)
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label fs-15">{{translate('Lunch Prices')}}</label>
                            </div>

                            <div class="form-group row row-cols-6">

                                @while($temp_meal<$max_meal)
                                        <?php
                                        $temp_meal = $temp_meal+1;

                                        ?>

                                    <div class="col">
                                        <div class="form-group row gutters-10">
                                            <label class="col-md-2 fs-15">{{$temp_meal}}</label>
                                            <div class="col-md-10">
                                                <div class="input-group" >
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium px-1">€</div>
                                                    </div>
                                                    <input type="number" class="form-control px-10px meal-price" step="0.1" name="meal_prices[{{$temp_meal-1}}][]" min="1" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endwhile

                            </div>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="form-group mb-2  mr-2 text-right">
                    <button type="submit" class="btn btn-primary" >{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        let pr_cards=1;
        let counter=2;

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('organisation-link').classList.add('active');
        });


        $("#duplicate_card_btn").click(function (e) {

            // function duplicate_card(){}
            // e.preventDefault();

            var price_range_div = document.getElementById("price_range_card");

            var clone = price_range_div.cloneNode(true);

            clone.getElementsByClassName("day-range")[0].innerHTML = counter + '. Day Range';
            clone.getElementsByClassName("start-day")[0].value = '';
            clone.getElementsByClassName("start-day")[0].removeAttribute("readonly");
            clone.getElementsByClassName("end-day")[0].value = '';

            for(var i=0; i< clone.getElementsByClassName("snack-price").length; i++){
                clone.getElementsByClassName("snack-price")[i].value = '';
            }

            for(var i=0; i< clone.getElementsByClassName("meal-price").length; i++){
                clone.getElementsByClassName("meal-price")[i].value = '';
            }

            counter++;

            document.getElementById("card_prices").appendChild(clone);

        });

        $("#delete_div_btn").click(function (e) {
            e.preventDefault();

            var price_range_div = document.getElementById("card_prices");

            if(price_range_div.childElementCount>1){
                counter--;
                price_range_div.removeChild(price_range_div.lastChild);
            }

        });


    </script>
@endsection



