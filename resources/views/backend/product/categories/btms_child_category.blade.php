@php
    $value = null;
    $level = $child_category->Level - 2;
    for ($i=0; $i < $level; $i++){
        $value .= '--';
    }
@endphp
<option value="{{ $child_category->{'Category Code'} }}">{{ $value." ".$child_category->{'Name'} }}</option>
{{--@if ($child_category->childrenCategories)--}}
{{--    @foreach ($child_category->childrenCategories as $childCategory)--}}
{{--        @include('backend.product.categories.btms_child_category', ['child_category' => $childCategory])--}}
{{--    @endforeach--}}
{{--@endif--}}
