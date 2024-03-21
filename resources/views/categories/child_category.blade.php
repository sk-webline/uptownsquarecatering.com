@php
    $value = null;
    for ($i=0; $i < $child_category->level; $i++){
        $value .= '--';
    }
    if (!isset($show_for_sale_status)) {
        $show_for_sale_status = true;
    }
@endphp
<option value="{{ $child_category->id }}">{{ $value." ".$child_category->getTranslation('name') }} @if($show_for_sale_status) - {{ $category->for_sale ? 'For Sale' : 'Not For Sale' }} @endif</option>
@if ($child_category->categories)
    @foreach ($child_category->categories as $childCategory)
        @include('categories.child_category', ['child_category' => $childCategory, 'show_for_sale_status' => $show_for_sale_status])
    @endforeach
@endif
