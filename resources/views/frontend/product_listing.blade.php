@extends('frontend.layouts.app')

@if (isset($category_id))
    @php
        $meta_title = \App\Category::find($category_id)->meta_title;
        $meta_description = \App\Category::find($category_id)->meta_description;
    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = \App\Brand::find($brand_id)->meta_title;
        $meta_description = \App\Brand::find($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title = get_setting('meta_title');
        $meta_description = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')
    <div id="product-listing-results">
        @include('frontend.partials.product_listing_ajax')
    </div>
@endsection

@section('script')
    <script type="text/javascript">
      function filter(){
          $.ajax({
              type:"POST",
              url: '{{ route('search_ajax') }}',
              data: $('#search-form').serializeArray(),
              beforeSend: function() {
                  $('#search-form').addClass('loader');
              },
              success: function(data){
                  $('#search-form').removeClass('loader');
                  $('#product-listing-results').html(data.view);
                  $('head title', window.parent.document).text(data.header_title_nam);
              }
          });
      }
      function rangefilter(arg){
        $('input[name=min_price]').val(arg[0]);
        $('input[name=max_price]').val(arg[1]);
        filter();
      }
      $(document).ready(function() {
        $('.results-categories-list .active').parent('ul').parent('li').addClass('active');
      });
      $(document).on('click', '.results-category-toggle', function () {
        $('.results-category-toggle').not(this).parent('li').removeClass('active');
        $(this).parent('li').toggleClass('active');
      });
      $(document).on('click', '.results-categories-list ul .results-categories-link', function () {
          $('.results-categories-list ul .results-categories-link').not(this).parent('li').removeClass('active');
          $(this).parent('li').toggleClass('active');
      });
      $(document).on('click', '#load-more:not(.no-more)', function (e) {
        e.preventDefault();

        var limit = parseInt($('input[name="page"]').val()) * parseInt($('input[name="products_per_page"]').val());

        if (window.location.search && window.location.search.indexOf('limit=') != -1) {
          var url = window.location.search.replace( /limit=\w*\d*/, "limit=" + limit);
        } else if (window.location.search) {
          var url = window.location.search + "&limit=" + limit;
        } else {
          var url = window.location.search + "?limit=" + limit;
        }

        $.ajax({
          type:"POST",
          url: '{{ route('load_search') }}',
          data: $('#search-form').serializeArray(),
          beforeSend: function() {
            $('#load-more').addClass('loader');
          },
          success: function(data){
            var page = $('#load-more').data('page') + 1;
            $('#load-more').removeClass('loader');
            $('#load-more').data('page', page);
            $('[name="page"]').attr('value', page);
            if(data.has_next_products == false) {
              $('#load-more').addClass('no-more');
              $('#load-more').html('{{toUpper(translate("No More Items"))}}');
            }
            $('.products-results-load').append(data.view);
            window.history.replaceState($('title').text(), $('title').text(), url);
          }
        });
      });

      $(document).on('click', '.results-categories-link', function (){
          var url = $(this).data('href');
          var category = $(this).data('category');
          var outlet = $(this).data('outlet');
          window.history.replaceState($('title').text(), $('title').text(), url);
          $('input[name="outlet"]').val(outlet);
          $('input[name="data_category_id"]').val(category);
          filter();
      });
    </script>
@endsection
