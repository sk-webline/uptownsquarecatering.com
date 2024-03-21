<h1>{{ translate('Used Product Request') }}</h1>
<p>{{ translate('You have a request for a used product. See below for more details.') }}</p>
<p><b>{{ translate('Product') }}:</b> <a href="{{ route('product',\App\Product::find($product)->slug) }}">{{ route('product',\App\Product::find($product)->slug) }}</a></p>
<p><b>{{ translate('Sender') }}:</b> {{ $name }}</p>
<p><b>{{ translate('Email') }}:</b> {{ $sender }}</p>
<p><b>{{ translate('Country') }}:</b> {{\App\Country::find($country)->name}}</p>
<p><b>{{ translate('Message') }}:</b> {{ $details }}</p>
