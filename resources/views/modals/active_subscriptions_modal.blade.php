<div class="px-15px px-lg-35px text-center">

    <div class="pt-10px text-center">
        <span class="fs-15"> {{translate('To delete this card please delete the following active subscriptions')}}</span>
    </div>

    <table class="table my-3 mb-0 fs-14">
        <thead>
        <tr>
            <th>{{ ucwords(translate('Plan Name'))}}</th>
            <th data-breakpoints="lg">{{translate('Period')}}</th>
            <th data-breakpoints="lg">{{translate('Purchase Date')}}</th>
        </tr>
        </thead>
        <tbody>
          @foreach($subs as $key => $sub)
            <tr>
                <td>{{$sub->name}}</td>
                <td>{{ \Carbon\Carbon::create($sub->from_date)->format('d/m/Y') }}
                      - {{ \Carbon\Carbon::create($sub->to_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::create($sub->created_at)->format('d/m/Y') }}</td>
            </tr>
         @endforeach
        </tbody>
    </table>

    <div class="pt-10px text-center">
    </div>

</div>
