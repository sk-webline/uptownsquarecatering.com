<h1>{{ translate('Request for Spare Parts') }}</h1>
<p>{{ translate('You have a new product request. See below for more details.') }}</p>
<h2>{{toUpper(translate('Bike Info'))}}</h2>
<p><b>{{ translate('Brand') }}:</b> {{ \App\Brand::find($brand)->getTranslation('name') }}</p>
<p><b>{{ translate('Model / Code') }}:</b> {{ $model_code }}</p>
<p><b>{{ translate('Model / Year') }}:</b> {{ $model_year }}</p>
<p><b>{{ translate('Chassis No.') }}:</b> {{ $chassis_no }}</p>
<p><b>{{ translate('Color Code') }}:</b> {{ $color_code }}</p>
<h2>{{toUpper(translate('Personal Info'))}}</h2>
<p><b>{{ translate('Sender') }}:</b> {{ $name }}</p>
<p><b>{{ translate('Email') }}:</b> {{ $sender }}</p>
<p><b>{{ translate('Country') }}:</b> {{\App\Country::find($country)->name}}</p>
<p><b>{{ translate('Message') }}:</b> {{ $details }}</p>
