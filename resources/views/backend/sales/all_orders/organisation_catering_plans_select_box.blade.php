
    @foreach($organisations as $key => $organisation)
        @php
            $organisation_settings = $organisation->currentSettings();
        @endphp
        @if($organisation_settings!=null)
            <optgroup class="text-black" label="{{$organisation->name}}">
                @foreach ($organisation_settings->catering_plans as $key => $catering_plan)
                    <option
                        value="{{$catering_plan->id}}">{{$catering_plan->name}}</option>
                @endforeach
            </optgroup>
        @endif
    @endforeach


