@php

@endphp
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <style>
        /*table.padding th{
			padding: .5rem .7rem;
		}*/
        table th{
			text-align: left;
		}
        table {
            width: 700px;
            margin-left: 1.5rem;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        h3 {
            margin-left: 1.5rem;
        }
    </style>
</head>
<body>
    <div style="width: 900px">
        <table bgcolor="#eceff4">
            <tr>
                <td style="height: 1.5rem;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="height: 1.5rem;">
                    <table>
                        <tr>
                            <td>
                                &nbsp;&nbsp;<img alt="Sport Marine" title="Sport Marine" src="{{ static_asset('assets/img/black-logo.png') }}" width="158" height="40" style="display:inline-block;">
                            </td>
                            <td class="text-right small"><span class="gry-color small" style="color:#878f9c;">{{  translate('Report Date') }}:</span> <span class=" strong">{{ date('d-m-Y H:i') }}</span>&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="height: 1.5rem;">
                    &nbsp;
                </td>
            </tr>
        </table>

        @isset($errors['Colors'])
            <h3>Colors</h3>
            @foreach($errors['Colors'] as $color_id => $color_errors)
                @if(count($color_errors) == 1 && array_key_exists("Name", $color_errors)) @continue @endif
                <table class="margin-top">
                    <thead>
                        <tr>
                            <th>Color: {{ $color_errors['Name'] }} #{{ @$color_id ?? '' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    @foreach($color_errors as $key => $color_error)
                                        @if($key !== 'Name')
                                        <li>{{ $color_error }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
{{--                @if($color_id == '760') @dd($color_errors, count($color_errors)) @endif--}}
            @endforeach
        @endisset

        @isset($errors['Brands'])
            <h3>Brands</h3>
            @foreach($errors['Brands'] as $brand_id => $brand_errors)
                <table class="margin-top">
                    <thead>
                        <tr>
                            <th>Brand: #{{ $brand_id }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    @foreach($brand_errors as $brand_error)
                                        <li>{{ $brand_error }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endisset

        @isset($errors['Parent_Categories'])
            <h3>Parent Categories</h3>
            @foreach($errors['Parent_Categories'] as $parent_category_id => $parent_category_errors)
                <table class="margin-top">
                    <thead>
                        <tr>
                            <th>Parent Category: #{{ $parent_category_id }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    @if(is_array($parent_category_errors))
                                        @foreach($parent_category_errors as $parent_category_error)
                                            <li>{{ $parent_category_error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endisset

        @isset($errors['Categories'])
            <h3>Categories</h3>
            @foreach($errors['Categories'] as $category_id => $category_errors)
                <table class="margin-top">
                    <thead>
                        <tr>
                            <th>Category: #{{ $category_id }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    @if(is_array($category_errors))
                                        @foreach($category_errors as $category_error)
                                            <li>{{ $category_error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endisset

        @if(isset($errors['Not_For_Sale_Categories']) && is_array($errors['Not_For_Sale_Categories']))
            <h3>Not for Sale Categories</h3>
            <p>The below categories has products for webshop but the category is not for sale</p>
            <table class="margin-top">
                <thead>
                <tr>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <ul>
                            @foreach($errors['Not_For_Sale_Categories'] as $category_id => $category_name)
                                <li>{{ $category_name }}({{ $category_id }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
        @endif

        @isset($errors['Products'])
            <h3>Products</h3>
            @foreach($errors['Products'] as $item_code => $product_errors)
            <table class="margin-top">
                <thead>
                    <tr>
                        <th>Product: #{{ $item_code }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <ul>
                                @foreach($product_errors as $product_error)
                                    <li>{{ $product_error }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endforeach
        @endisset
    </div>
</body>
</html>
