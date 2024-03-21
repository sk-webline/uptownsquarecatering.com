<h1>{{ translate('Partnership Request') }}</h1>
<p>{{ translate('You have a new partnership request. See below for more details.') }}</p>
<p><b>{{ translate('Sender') }}:</b> {{ $name }}</p>
<p><b>{{ translate('Company') }}:</b> {{ $company }}</p>
<p><b>{{ translate('Email') }}:</b> {{ $sender }}</p>
<p><b>{{ translate('Country') }}:</b> {{ \App\Country::find($country)->name }}</p>
<p><b>{{ translate('City') }}:</b> {{ \App\City::find($city)->name }}</p>
<p><b>{{ translate('Phone') }}:</b> {{ $phone }}</p>
<p><b>{{ translate('Interested in') }}:</b> @php echo implode(", ", $interests); @endphp</p>
<p><b>{{ translate('Message') }}:</b> {{ $details }}</p>
