/*Dots Mapping*/
$(document).ready(function() {
  if ($('.dot-info-row').length > 0) {
    $('.dot-info-row').each(function() {
      var $this =  $(this);
      var xpos = $this.find('.dot-info-x').val();
      var ypos = $this.find('.dot-info-y').val();
      var key = $this.data('key');
      $($this.data('bind')).append('<div data-key="' + key + '" class="point-location d-none d-md-block" style="left: ' + xpos + '%;top: ' + ypos + '%;"><div class="point-location-toggle"></div></div>');
    });
  }
});

$(document).on('keyup mouseup change', '.dot-info-x', function () {
  var $dot = $(this).closest('.dot-info-row');
  $($dot.data('bind')).find('.point-location[data-key="' + $dot.data('key') + '"]').css('left', $(this).val() + '%');
  console.log($(this).val());
});

$(document).on('keyup mouseup change', '.dot-info-y', function () {
  var $dot = $(this).closest('.dot-info-row');
  $($dot.data('bind')).find('.point-location[data-key="' + $dot.data('key') + '"]').css('top', $(this).val() + '%');
});

$(document).on('click', '.add-another-dot', function () {
  var $this = $(this);
  var key = Math.random();
  $($this.data('bind')).append('<div data-key="' + key + '" class="point-location d-none d-md-block" style="left: 0%;top: 0%;"><div class="point-location-toggle"></div></div>');
  $('.dot-info-row[data-bind="' + $this.data('bind') + '"]:last-child').attr('data-key', key);
});

$(document).on('click', '.remove-another-dot', function () {
  var $dot = $(this).closest('.dot-info-row');
  $($dot.data('bind')).find('.point-location[data-key="' + $dot.data('key') + '"]').remove();
});

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

$('.dd_mm_formatted').on("change keyup keypress", function() {

    console.log('alooo');
    if (this.value.length > 0) {
        this.setAttribute(
            "data-date",
            moment(this.value, "YYYY-MM-DD").format(this.getAttribute("data-date-format"))
        );
    }

}).trigger("change");

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



$('.toUpperCase').on("change keyup keypress", function() {

    var input = $(this).val();

    var result = input.toUpperCase();

    $(this).val(result);

});

function addLoader() {
    $('body').addClass('loader');
}

function removeLoader() {
    $('body').removeClass('loader');
}







