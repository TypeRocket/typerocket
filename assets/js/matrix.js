jQuery(document).ready(function($) {

  if(typeof window.trRepeaterCallback === 'object') {
  } else {
    window.trRepeaterCallback = [];
  }

  $('.typerocket-container').on('click', '.matrix-button', function(e) {
    var $that = $(this);

    if(!$that.is(":disabled")) {
      var mxid = $that.data('id'), folder = $that.data('folder');
      var $fields = $( '.matrix-fields-' + mxid ), $select = $( '.matrix-select-' + mxid );
      var button_txt = $that.val();
      var callbacks = window.trRepeaterCallback;

      $that.attr("disabled", "disabled").val('Adding...');

      var url = '/typerocket_matrix_api/v1/' + folder + '/' + $select.val(), $option = $select.find('option[value="' +$select.val()+ '"]');

      $.ajax({
        url:  url,
        dataType: 'html',
        data: { form_group: $option.data('group') },
        success: function(data) {
          data = $( data );

          if( $.isFunction($.fn.datepicker) ) {
            data.find('.date-picker[name]').each(function(){
              $(this).datepicker();
            });
          }

          if( $.isFunction($.fn.wpColorPicker) ) {
            data.find('.color-picker[name]').each(function(){
              var pal = $(this).attr('id') + '_color_palette',
                settings = { palettes: window[pal] };
              $(this).wpColorPicker(settings);
            });
          }

          if( $.isFunction($.fn.datepicker) ) {
            data.find('.time-picker[name]').each(function(){
              $(this).timepicker({
                timeFormat: 'hh:mm tt'
              });
            });
          }

          // callback group
          for(var ri = 0; callbacks.length > ri; ri++) {
            if (typeof callbacks[ri] === "function") {
              // Call it, since we have confirmed it is callable​
              callbacks[ri](data);
            }
          }

          data.prependTo($fields).hide().delay(10).slideDown(300).scrollTop('100%');


          if( $.isFunction($.fn.sortable) ) {
            var $sortables = $fields.find('.tr-gallery-list'),
              $items_list = $fields.find('.tr-items-list'),
              $repeater_fields = $fields.find('.tr-repeater-fields');
            if($sortables.length > 0) {
              $sortables.sortable();
            }

            if($repeater_fields.length > 0) {
              $repeater_fields.sortable({
                connectWith: ".tr-repeater-group",
                handle: ".repeater-controls"
              });
            }

            if($items_list.length > 0) {
              $items_list.sortable({
                connectWith: ".item",
                handle: ".move"
              });
            }

          }

          $that.val(button_txt).removeAttr("disabled", "disabled");

        },
        error: function(jqXHR) {
          $that.val('Try again - Error ' + jqXHR.status).removeAttr("disabled", "disabled");
        },
        complete: function(jqXHR) {
          // nothing right now
        }
      });
    }

  });
});
