@foreach ($products as $key => $product)
    <div class="col mb-90px product-res-item">
        @include('frontend.partials.product_listing.forsale_listing',['product' => $product, 'type_id' => $type_id , 'brand_id' => $brand_id])
    </div>
@endforeach