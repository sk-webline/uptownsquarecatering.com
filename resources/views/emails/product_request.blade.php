<h1>{{ translate('Product Request') }}</h1>
<p>{{ translate('You have a new product request. See below for more details.') }}</p>
<p><b>{{ translate('Product') }}:</b> <a href="{{ route('product',\App\Product::find($product)->slug) }}">{{ route('product',\App\Product::find($product)->slug) }}</a></p>
@if($color)
    <p><b>{{ translate('Color') }}:</b> {{$color}}</p>
@endif
@if($attributes)
    @foreach($attributes as $key => $value)
        @php
            $attr_id = explode('attribute_id_',$key);
        @endphp
        <p><b>{{ \App\Attribute::find($attr_id[1])->getTranslation('name') }}:</b> {{$value}}</p>
    @endforeach
@endif
<p><b>{{ translate('Quantity') }}:</b> {{$quantity}}</p>
<p><b>{{ translate('Sender') }}:</b> {{ $name }}</p>
<p><b>{{ translate('Email') }}:</b> {{ $sender }}</p>
<p><b>{{ translate('Country') }}:</b> {{\App\Country::find($country)->name}}</p>
<p><b>{{ translate('Message') }}:</b> {{ $details }}</p>
