@if(count($combinations[0]) > 0)
<table class="table table-bordered sk-table">
	<thead>
		<tr>
			<td class="text-center" width="10%">
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
			<td class="text-center" data-breakpoints="lg" width="10%">
				{{translate('Qty')}}
			</td>
            <td class="text-center" data-breakpoints="lg" width="10%">
                {{translate('Weight (gr)')}}
            </td>
			<td class="text-center" data-breakpoints="lg" width="25%">
				{{translate('Photo')}}
			</td>
		</tr>
	</thead>
	<tbody>
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
					<input type="number" lang="en" name="price_{{ $str }}" value="{{ $unit_price }}" min="0" step="0.01" class="form-control" required>
				</td>
				<td>
					<input type="number" lang="en" name="whole_price_{{ $str }}" value="{{ $whole_price }}" min="0" step="0.01" class="form-control" required>
				</td>
				<td>
					<input type="text" name="part_number_{{ $str }}" class="form-control" value="{{ $part_number }}" required>
				</td>
				<td>
					<input type="number" lang="en" name="qty_{{ $str }}" value="10" min="0" step="1" class="form-control" required>
				</td>
                <td>
                    <label>&nbsp;</label>
                    <input type="number" lang="en" name="weight_{{ $str }}" value="" min="0" step="1" class="form-control" required>
                </td>
				<td>
					<div class=" input-group " data-toggle="skuploader" data-type="image">
						<div class="input-group-prepend">
							<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
						</div>
						<div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
						<input type="hidden" name="img_{{ $str }}" class="selected-files">
					</div>
					<div class="file-preview"></div>
				</td>
			</tr>
		@endif
	@endforeach
	</tbody>
</table>
@endif
