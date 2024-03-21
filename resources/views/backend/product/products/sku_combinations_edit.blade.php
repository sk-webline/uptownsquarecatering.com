@if(count($product->stocks) > 0 && $product->variant_product)
<table class="table table-bordered sk-table">
	<thead>
		<tr>
			<td class="text-center" width="10%"  data-breakpoints="lg">
				{{translate('Variant')}}
			</td>
			<td class="text-center" width="10%">
				{{translate('Variant Price')}} <small>{{translate('(without VAT)')}}</small>
			</td>
			<td class="text-center" width="10%">
				{{translate('Wholesale Price')}} <small>{{translate('(without VAT)')}}</small>
			</td>
			<td class="text-center" data-breakpoints="lg" width="15%">
				{{translate('Part Number')}}
			</td>
			<td class="text-center" data-breakpoints="sm" width="10%">
				{{translate('Qty')}}
			</td>
            <td class="text-center" data-breakpoints="lg" width="10%">
                {{translate('Weight (gr)')}}
            </td>
			<td class="text-center" data-breakpoints="lg" width="25%">
				{{translate('Upload Image')}}
			</td>
            <td class="text-center" data-breakpoints="lg">
                {{translate('Image')}}
            </td>
		</tr>
	</thead>
	<tbody>
        @if($product->import_from_btms)

            @php
                $colors_active = (count(json_decode($product->colors, true)) > 0) ? 1 : 0;
                $attributes = json_decode($product->attributes, true);
                $size_active = (count($attributes) > 0 && $attributes[0] == 1) ? 1 : 0;
            @endphp
            @if($product->variant_product)
                @foreach ($product->stocks as $stock)
                    @php
                        $str = '';
                        if($colors_active && $size_active){
                            list($color, $size) = explode('-', $stock->variant);
                            $color_name = \App\Color::where('id', $color)->first()->name;
                            $str = $color_name."-".getSizeName($size);
                        }
                        elseif ($colors_active && !$size_active) {
                            $color_name = \App\Color::where('id', $stock->variant)->first()->name;
                            $str = $color_name;
                        }
                        elseif (!$colors_active && $size_active) {
                            $str = getSizeName($stock->variant);
                        }
                    @endphp
                    <tr class="variant">
                        <td>
                            <label for="" class="control-label">{{ $str }}</label>
                        </td>
                        <td>
                            <input type="number" lang="en" name="price_{{ $str }}" value="{{ $stock->price }}" min="0" step="0.01" class="form-control" required
                                   {{ disableInputForBtmsProducts($product) }}>
                        </td>
                        <td>
                            <input type="number" lang="en" name="whole_price_{{ $str }}" value="{{ $stock->whole_price }}" min="0" step="0.01" class="form-control" required {{ disableInputForBtmsProducts($product) }}>
                        </td>
                        <td>
                            <input type="text" name="part_number_{{ $str }}" value="{{ $stock->part_number }}" class="form-control" required {{ disableInputForBtmsProducts($product) }}>
                        </td>
                        <td>
                            <input type="number" lang="en" name="qty_{{ $str }}" value="{{ $stock->qty }}" min="0" step="1" class="form-control" required {{ disableInputForBtmsProducts($product) }}>
                        </td>
                        <td>
                            <input type="number" lang="en" name="weight_{{ $str }}" value="{{ $stock->weight }}" min="0" step="1" class="form-control" required {{ disableInputForBtmsProducts($product) }}>
                        </td>
                        <td>
                            <div class=" input-group " data-toggle="skuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="img_{{ $stock->variant }}" class="selected-files" value="{{ $stock->image ?? null }}">
                            </div>
                            <div class="file-preview"></div>
                        </td>
                        <td class="text-center align-middle">
                            <img src="{{ uploaded_asset($stock->image) }}" height="80px">
                        </td>
                    </tr>
                @endforeach
            @endif
        @else
            @foreach ($combinations as $key => $combination)
                @php
                    $sku = '';
                    foreach (explode(' ', $product_name) as $key => $value) {
                        $sku .= substr($value, 0, 1);
                    }

                    $str = '';
                    $label = '';
                    foreach ($combination as $key => $item){
                        if($key > 0 ){
                            $str .= '-'.str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                            $label .='-'.str_replace(' ', '', $item);
                        }
                        else{
                            if($colors_active == 1){
                                $color_name = \App\Color::where('id', $item)->first()->name;
                                //$str .= $color_name;
                                //$sku .='-'.$color_name;
                                $label .= $color_name;
                                $str .= $item;
                                $sku .='-'.$item;
                            }
                            else{
                                $str .= str_replace(' ', '', $item);
                                $sku .='-'.str_replace(' ', '', $item);
                            }
                        }
                    }
                @endphp
                @if(strlen($str) > 0)
                    <tr class="variant">
                        <td>
                            <label for="" class="control-label">{{ $label }}</label>
                        </td>
                        <td>
                            <input type="number" lang="en" name="price_{{ $str }}" value="@php
                                if ($product->unit_price == $unit_price) {
                                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                        echo $stock->price;
                                    }
                                    else{
                                        echo $unit_price;
                                    }
                                }
                                else{
                                    echo $unit_price;
                                }
                            @endphp" min="0" step="0.01" class="form-control" required>
                        </td>
                        <td>
                            <input type="number" lang="en" name="whole_price_{{ $str }}" value="@php
                                if ($product->wholesale_price == $whole_price) {
                                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                        echo $stock->whole_price;
                                    }
                                    else{
                                        echo $whole_price;
                                    }
                                }
                                else{
                                    echo $whole_price;
                                }
                            @endphp" min="0" step="0.01" class="form-control" required>
                        </td>
                        <td>
                            <input type="text" name="part_number_{{ $str }}" value="@php
                                if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                    echo $stock->part_number;
                                }
                            @endphp" class="form-control" required>
                        </td>
                        <td>
                            <input type="number" lang="en" name="qty_{{ $str }}" value="@php
                                if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                    echo $stock->qty;
                                }
                                else{
                                    echo '10';
                                }
                            @endphp" min="0" step="1" class="form-control" required>
                        </td>
                        <td>
                            <input type="number" lang="en" name="weight_{{ $str }}" value="@php
                                if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                    echo $stock->weight;
                                }
                                else{
                                    echo '0';
                                }
                            @endphp" min="0" step="1" class="form-control" required>
                        </td>
                        <td>
                            <div class=" input-group " data-toggle="skuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="img_{{ $str }}" class="selected-files" value="@php
                                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                                        echo $stock->image;
                                    }
                                    else{
                                        echo null;
                                    }
                                @endphp">
                            </div>
                            <div class="file-preview"></div>
                        </td>

                    </tr>
                @endif
            @endforeach
        @endif
	</tbody>
</table>
@endif
