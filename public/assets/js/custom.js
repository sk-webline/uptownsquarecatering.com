if($('header .header-menu-list > li > a').length > 0){
  $(document).on('click', 'header .header-menu-list > li > a', function(e) {
    e.preventDefault();
    $(this).parent('li').toggleClass('active').removeClass('inactive');
    if($(this).parent('li').hasClass('active')){
      $('body').addClass('header-menu-opened');
      $('header .header-menu-list > li > a').not(this).parent('li').addClass('inactive').removeClass('active');
    } else {
      $('body').removeClass('header-menu-opened');
      $('header .header-menu-list > li > a').not(this).parent('li').removeClass('inactive').removeClass('active');
    }

  });

}

if($('.fixed-header .header-menu-list > li > a').length > 0){
  $(document).on('click', '.fixed-header .header-menu-list > li > a', function(e) {
    e.preventDefault();
    $(this).parent('li').toggleClass('active').removeClass('inactive');
    if($(this).parent('li').hasClass('active')){
      $('body').addClass('fixed-header-menu-opened');
      $('.fixed-header .header-menu-list > li > a').not(this).parent('li').addClass('inactive').removeClass('active');
    } else {
      $('body').removeClass('fixed-header-menu-opened');
      $('.fixed-header .header-menu-list > li > a').not(this).parent('li').removeClass('inactive').removeClass('active');
    }
  });
}

$(document).mouseup(function(e) {
  if($('body').hasClass('header-menu-opened')) {
    var container = $("header .header-menu-list > li");
    if(!container.is(e.target) && container.has(e.target).length === 0) {
      $('body').removeClass('header-menu-opened');
      $('header .header-menu-list > li').removeClass('inactive').removeClass('active');
    }
  }

  if($('body').hasClass('fixed-header-menu-opened')) {
    var fixed_container = $(".fixed-header .header-menu-list > li");
    if(!fixed_container.is(e.target) && fixed_container.has(e.target).length === 0) {
      $('body').removeClass('fixed-header-menu-opened');
      $('.fixed-header .header-menu-list > li').removeClass('inactive').removeClass('active');
    }
  }

  if($('body').hasClass('side-popup-opened')) {
    var side_container = $(".side-popup-box");
    if(!side_container.is(e.target) && side_container.has(e.target).length === 0) {
      $('body').removeClass('side-popup-opened');
      $('.side-popup').removeClass('active');
    }
  }

});

if($('.megamenu-bottom .megamenu-categories-btn').length > 0){
  $(document).on('click', '.megamenu-bottom .megamenu-categories-btn', function() {
    var type = $(this).data('type');
    $('.megamenu-bottom .megamenu-categories-btn').not(this).removeClass('active');
    $('.megamenu-bottom .megamenu-sidebar-cat-content, .megamenu-bottom .megamenu-body-cat-content, .megamenu-bottom .megamenu-body-subcat-content, .megamenu-bottom .megamenu-sidebar-cat-item').removeClass('active');
    $(this).addClass('active');
    $('.megamenu-bottom .megamenu-sidebar-cat-content[data-type="' + type + '"], .megamenu-bottom .megamenu-body-cat-content[data-type="' + type + '"]').addClass('active');
  });
}

if($('.megamenu-sidebar-cat-item a').length > 0){
  $(document).on('click mouseenter', '.megamenu-sidebar-cat-item a', function() {
    $('.megamenu-sidebar-cat-item a').not(this).parent('.megamenu-sidebar-cat-item').removeClass('active');
    $('.megamenu-body-subcat-content').removeClass('active');
    $(this).parent('.megamenu-sidebar-cat-item').addClass('active');
    $('.megamenu-body-subcat-content[data-id="' + $(this).parent('.megamenu-sidebar-cat-item').data('id') + '"]').addClass('active');
  });
}

if($('.megamenu-bottom-phone .megamenu-body .megamenu-categories-btn').length > 0){
  $(document).on('click', '.megamenu-bottom-phone .megamenu-body .megamenu-categories-btn', function() {
    var type = $(this).data('type');
    $('.megamenu-bottom-phone .megamenu-body .megamenu-categories-btn').not(this).removeClass('active');
    $('.megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-sidebar-cat-content, .megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-body-cat-content, .megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-body-subcat-content, .megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-sidebar-cat-item, .megamenu-bottom-phone .megamenu-phone-general-tabs .megamenu-categories-btn, .megamenu-phone-general-categories, .megamenu-phone-general-categories .megamenu-sidebar-cat-content').removeClass('active');
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {
      $('.megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-sidebar-cat-content[data-type="' + type + '"], .megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-body-cat-content[data-type="' + type + '"]').addClass('active');
    } else {
      $('.megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-sidebar-cat-content[data-type="' + type + '"], .megamenu-bottom-phone .megamenu-phone-brand-categories .megamenu-body-cat-content[data-type="' + type + '"]').removeClass('active');
    }
    if($('.megamenu-bottom-phone .megamenu-body .megamenu-categories-btn.active').length > 0) {
      $('.megamenu-phone-brand-categories').addClass('active');
    } else {
      $('.megamenu-phone-brand-categories').removeClass('active');
    }
  });
}


if($('.megamenu-bottom-phone .megamenu-phone-general-tabs .megamenu-categories-btn').length > 0){
  $(document).on('click', '.megamenu-bottom-phone .megamenu-phone-general-tabs .megamenu-categories-btn', function() {
    var type = $(this).data('type');
    $('.megamenu-bottom-phone .megamenu-phone-general-tabs .megamenu-categories-btn').not(this).removeClass('active');
    $('.megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-sidebar-cat-content, .megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-body-cat-content, .megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-body-subcat-content, .megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-sidebar-cat-item, .megamenu-bottom-phone .megamenu-body .megamenu-categories-btn, .megamenu-phone-brand-categories, .megamenu-phone-brand-categories .megamenu-sidebar-cat-content').removeClass('active');
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {
      $('.megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-sidebar-cat-content[data-type="' + type + '"], .megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-body-cat-content[data-type="' + type + '"]').addClass('active');
    } else {
      $('.megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-sidebar-cat-content[data-type="' + type + '"], .megamenu-bottom-phone .megamenu-phone-general-categories .megamenu-body-cat-content[data-type="' + type + '"]').removeClass('active');
    }
    if($('.megamenu-bottom-phone .megamenu-phone-general-tabs .megamenu-categories-btn.active').length > 0) {
      $('.megamenu-phone-general-categories').addClass('active');
    } else {
      $('.megamenu-phone-general-categories').removeClass('active');
    }
  });
}

if($('.megamenu-phone-general-categories .megamenu-sidebar-cat-item-title .toggle').length > 0){
  $(document).on('click', '.megamenu-phone-general-categories .megamenu-sidebar-cat-item-title .toggle', function() {
    $('.megamenu-phone-general-categories .megamenu-sidebar-cat-item-title .toggle').not(this).parent('.megamenu-sidebar-cat-item-title').removeClass('active');
    $(this).parent('.megamenu-sidebar-cat-item-title').toggleClass('active');
  });
}

if($('.megamenu-phone-brand-categories .megamenu-sidebar-cat-item-title .toggle').length > 0){
  $(document).on('click', '.megamenu-phone-brand-categories .megamenu-sidebar-cat-item-title .toggle', function() {
    $('.megamenu-phone-brand-categories .megamenu-sidebar-cat-item-title .toggle').not(this).parent('.megamenu-sidebar-cat-item-title').removeClass('active');
    $(this).parent('.megamenu-sidebar-cat-item-title').toggleClass('active');
  });
}

if($('.side-popup-toggle').length > 0){
  $(document).on('click', '.side-popup-toggle', function () {
    if($(this).data('rel') !== undefined) {
      $('body').addClass('side-popup-opened');
      $('#'+ $(this).data('rel')).addClass('active');
    }
  });
}

if($('.side-popup-close').length > 0){
  $(document).on('click', '.side-popup-close', function () {
    $('body').removeClass('side-popup-opened');
    $('.side-popup').removeClass('active');
  });
}

if($('.feature-cat-res-wrap').length > 0){
  $(document).on('click', '.feature-cat-res-wrap', function () {
    $('.feature-cat-res-wrap').not(this).removeClass('active');
    $(this).toggleClass('active');
    $('.feature-cat-res-row, .feature-cat-res-row-tablet, .feature-cat-res-dropdown').removeClass('active');
    if($(this).hasClass('active')){
      $('.feature-cat-res-row[data-row="' + $(this).data('row') + '"]').addClass('active');
      $('.feature-cat-res-row-tablet[data-tablet-row="' + $(this).data('tablet-row') + '"]').addClass('active');
      $('.feature-cat-res-dropdown[data-id="' + $(this).data('id') + '"]').addClass('active');
    }
  });
}

$('.feature-cat-res-dropdown.desktop').each(function() {
  var cat_id = $(this).data('id');
  var cat_element = $('.feature-cat-res-wrap[data-id="' + cat_id + '"]');
  if(cat_element.length > 0){
    var row = cat_element.data('row');
    $(this).appendTo('.feature-cat-res-row[data-row="' + row + '"]');
  }
  if($('.feature-cat-res-row .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel').length > 0){
    $('.feature-cat-res-row .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel').sly({
      horizontal: 1,
      itemNav: 'basic',
      smart: 1,
      activateMiddle: 1,
      activateOn: 'click',
      mouseDragging: 1,
      touchDragging: 1,
      releaseSwing: 1,
      startAt: 0,
      scrollBar: $('.feature-cat-res-row .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel-scrollbar'),
      scrollBy: 0,
      speed: 300,
      elasticBounds: 1,
      easing: 'easeOutExpo',
      dragHandle: 1,
      dynamicHandle: 1,
      clickBar: 1,
    });
  }
});

$('.feature-cat-res-dropdown.tablet').each(function() {
  var cat_id = $(this).data('id');
  var cat_element = $('.feature-cat-res-wrap[data-id="' + cat_id + '"]');
  if(cat_element.length > 0){
    var tablet_row = cat_element.data('tablet-row');
    console.log(tablet_row);
    $(this).appendTo('.feature-cat-res-row-tablet[data-tablet-row="' + tablet_row + '"]');
  }
  if($('.feature-cat-res-row-tablet .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel').length > 0){
    $('.feature-cat-res-row-tablet .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel').sly({
      horizontal: 1,
      itemNav: 'basic',
      smart: 1,
      activateMiddle: 1,
      activateOn: 'click',
      mouseDragging: 1,
      touchDragging: 1,
      releaseSwing: 1,
      startAt: 0,
      scrollBar: $('.feature-cat-res-row-tablet .feature-cat-res-dropdown[data-id="' + cat_id + '"] .feature-carousel-scrollbar'),
      scrollBy: 0,
      speed: 300,
      elasticBounds: 1,
      easing: 'easeOutExpo',
      dragHandle: 1,
      dynamicHandle: 1,
      clickBar: 1,
    });
  }
});

$(document).on('click', '.services-res-wrap:not(.clickable) .services-res-toggle', function () {
  $('.services-res-toggle').not(this).closest('.services-res-item').removeClass('active');
  $(this).closest('.services-res-item').toggleClass('active');
});

$(document).on('click', '.services-res-wrap.clickable', function () {
  $('.services-res-wrap.clickable').not(this).closest('.services-res-item').removeClass('active');
  $(this).closest('.services-res-item').toggleClass('active');
});

if($('.form-no-space').length > 0) {
  $(function() {
    $('.form-no-space').each(function(index) {
      var field_txt = $(this);
      var removeSpace = function() {
        field_txt.val(field_txt.val().replace(/\s/g, ''));
      }
      field_txt.keyup(removeSpace).blur(removeSpace);
    });
  });
}

if($('.form-control-with-label').length > 0) {
  $('.form-control-with-label:not(.always-focused)').each(function () {
    if ($(this).find('.form-control').val()) {
      $(this).addClass('focused');
    } else {
      $(this).removeClass('focused');
    }
  });

  $(document).on('focusin', '.form-control-with-label:not(.always-focused) .form-control', function () {
    $(this).closest('.form-control-with-label').addClass('focused');
  });

  $(document).on('focusout', '.form-control-with-label:not(.always-focused) .form-control', function () {
    if ($(this).val()) {
      $(this).closest('.form-control-with-label').addClass('focused');
    } else {
      $(this).closest('.form-control-with-label').removeClass('focused');
    }
  });
}

/*Points Dots*/
$(document).ready(function(){
  setPointsPositions();
});
$( window ).on("load", function() {
  setPointsPositions();
});
$(window).resize(function () {
  setTimeout(function(){
    setPointsPositions();
  }, 500);
});
$(document).on('click', '.point-location-toggle', function () {
  $('.point-location-toggle').not(this).parent('.point-location').removeClass('active');
  $(this).parent('.point-location').toggleClass('active');
});

$(document).mouseup(function(e) {
  var container = $(".point-location");
  if(!container.is(e.target) && container.has(e.target).length === 0) {
    $('.point-location').removeClass('active');
  }
});

function setPointsPositions() {
  $('.point-location-popup').each(function() {
    $(this).removeClass('bottom left right');
    var popup_offset = $(this).offset().top - 190;
    var popup_offset_left = $(this).offset().left - 90;
    if (popup_offset < $(this).closest('.stores-map-over').offset().top) {
      $(this).addClass('bottom');
    }
    if(popup_offset_left < 0) {
      $(this).addClass('left');
    } else if(popup_offset_left + 180 > $('body').outerWidth()) {
      $(this).addClass('right');
    }
  });
}

if($('.footer-list-toggle').length > 0){
  $(document).on('click', '.footer-list-toggle', function () {
    $('.footer-list-toggle').not(this).removeClass('active');
    $(this).toggleClass('active');
  });
}


if($('.footer-list-dropdown .list-unstyled a').length > 0) {
  $(document).on('click mouseenter', '.footer-list-dropdown .list-unstyled a', function () {
    $('.footer-list-dropdown .list-unstyled a').not(this).removeClass('active').addClass('opacity-50');
    $(this).removeClass('opacity-50').addClass('active');
  });
  $(document).on('click mouseleave', '.footer-list-dropdown .list-unstyled a', function () {
    $('.footer-list-dropdown .list-unstyled a').removeClass('active').removeClass('opacity-50');
  });
}

function addLoader() {
    $('body').addClass('loader');
}
function removeLoader() {
    $('body').removeClass('loader');
}

function addLoaderToModal() {
    $('#calendar-date-range').addClass('loader');
}
function removeLoaderFromModal() {
    $('#calendar-date-range').removeClass('loader');
}

function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }


$('.remove-last-space').on("change keyup keypress", function() {

    // console.log('alooo');

    var input = $(this).val();
    if (input.substring(input.length-1, input.length)==' ') {
        input = input.substring(0, input.length-1);
        $(this).val( input);
    }

});



$('.remove-all-spaces').on("change keyup keypress", function() {

    var input = $(this).val();

    var new_string = input.replace(/\s+/g, '');

    $(this).val(new_string);

});


/*Notification*/
$(document).on('click', '[data-notification]', function (){
    $('#' + $(this).data('notification')).addClass('active');
});

$(document).mouseup(function(e) {
    if(!$('.notification-pop-box').is(e.target) && $('.notification-pop-box').has(e.target).length === 0) {
        $('.notification-pop').removeClass('active');
    }
});

// $('.dd_mm_formatted').on("change keyup keypress", function() {
//     if (this.value.length > 0) {
//         this.setAttribute(
//             "data-date",
//             moment(this.value, "YYYY-MM-DD").format(this.getAttribute("data-date-format"))
//         );
//     }else{
//         this.setAttribute("data-date", "");
//     }
//
// }).trigger("change");


