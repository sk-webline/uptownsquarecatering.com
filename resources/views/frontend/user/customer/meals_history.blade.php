@extends('frontend.layouts.user_panel')

@section('meta_title'){{ translate('Meal History') }}@stop

@section('panel_content')
    <?php use Carbon\Carbon; ?>
    <h1 class="fs-14 md-fs-16 mb-10px mb-md-15px text-primary-50 fw-700 lh-1-2 xl-lh-1">
        <a class="hov-text-primary" href="{{route('dashboard')}}">
            {{ toUpper(translate('Dashboard')) }}
        </a> /
        <span class="d-inline-block"><span class="border-bottom border-inherit">{{ toUpper(translate('Meal History')) }} - {{toUpper($card->name)}}</span></span>
    </h1>

    <div class="background-brand-grey px-lg-25px fs-14">
        @if(count($card_usages) > 0)
            <div class="pb-lg-20px">
                <div class="d-none d-lg-block">
                    <table class="table sk-table mb-0 history-table">
                        <thead>
                        <tr>
                            <th width="50%">{{toUpper(translate('Date'))}}</th>
                            <th>{{toUpper(translate('Snack'))}} 1</th>
                            <th>{{toUpper(translate('Snack'))}} 2</th>
                            <th>{{toUpper(translate('Snack'))}} 3</th>
                            <th>{{toUpper(translate('Lunch'))}} 1</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($card_usages as $key => $card_usage)
                            <tr>
                                {{--                        <td class="col-6 no-gutters">{{\Carbon\Carbon::create($card_usage->created_at)->format('d/m/y')}}</td>--}}
                                <td>{{Carbon::create($card_usage->purchase_date)->format('d/m/y') }}</td>

                                    <?php
                                    $snacks_array = explode(",", $card_usage->snack_times);
                                    $lunch_array = explode(",", $card_usage->lunch_times);
                                    ?>
                                @if(count($snacks_array)>=1)
                                    <td><span>{{Carbon::create($snacks_array[0])->format('H:i') }}</span></td>
                                @else
                                    <td><span>-</span></td>
                                @endif

                                @if(count($snacks_array)>=2)
                                    <td><span>{{Carbon::create($snacks_array[1])->format('H:i') }}</span></td>
                                @else
                                    <td><span>-</span></td>
                                @endif

                                @if(count($snacks_array)>=3)
                                    <td><span>{{Carbon::create($snacks_array[2])->format('H:i') }}</span></td>
                                @else
                                    <td><span>-</span></td>
                                @endif

                                @if(count($lunch_array)>=1)
                                    <td><span>{{Carbon::create($lunch_array[0])->format('H:i') }}</span></td>
                                @else
                                    <td><span>-</span></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-lg-none">
                    @foreach($card_usages as $key => $card_usage)
                            <?php
                            $snacks_array = explode(",", $card_usage->snack_times);
                            $lunch_array = explode(",", $card_usage->lunch_times);
                            ?>
                        <div class="table-row-results">
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Date'))}}</div>
                                    <div class="col-auto text-primary-50">{{Carbon::create($card_usage->purchase_date)->format('d/m/y') }}</div>
                                </div>
                            </div>

                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Snack'))}} 1</div>
                                    <div class="col-auto text-primary-50">
                                        @if(count($snacks_array)>=1)
                                            {{Carbon::create($snacks_array[0])->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Snack'))}} 2</div>
                                    <div class="col-auto text-primary-50">
                                        @if(count($snacks_array)>=2)
                                            {{Carbon::create($snacks_array[1])->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Snack'))}} 3</div>
                                    <div class="col-auto text-primary-50">
                                        @if(count($snacks_array)>=3)
                                            {{Carbon::create($snacks_array[2])->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="table-row-item">
                                <div class="row gutters-5 align-items-center">
                                    <div class="col fw-700">{{toUpper(translate('Snack'))}} 4</div>
                                    <div class="col-auto text-primary-50">
                                        @if(count($snacks_array)>=4)
                                            {{Carbon::create($snacks_array[3])->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="custom-pagination" class="sk-pagination">
                    {{$card_usages->links()}}
                </div>
            </div>
        @else
            <div class="text-center fw-700 p-30px">{{translate('No meal history yet')}}</div>
        @endif
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
           console.log({!! json_encode($card_usages) !!});
        });
    </script>
@endsection
