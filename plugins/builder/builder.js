jQuery(function($) {

  var tr_builder_toggle = $('#tr_page_type_toggle');

  if(tr_builder_toggle.length > 0) {

    // on load
    if($('#tr_page_builder_control').hasClass('builder-active')) {
      $('#builderStandardEditor').hide();
    } else {
      $('#tr_page_builder').hide();
    }

    // on toggle
    $(tr_builder_toggle).on('click', 'a', function(e) {
      e.preventDefault();
      var that = $(this);
      var other = $(that.siblings()[0]);
      var checkbox = $('#builderSelectRadio input')[1];

      that.addClass('builder-active button-primary');
      other.removeClass('builder-active button-primary');

      $(that.attr('href')).show();
      $(other.attr('href')).hide();

      if(that.attr('id') == 'tr_page_builder_control') {
        $(checkbox).attr('checked', 'checked');
      } else {
        $(checkbox).removeAttr('checked');
        $('#content-html').click();
        $('#content-tmce').click();
      }

    });
  }

});
