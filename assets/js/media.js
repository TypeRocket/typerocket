jQuery(document).ready(function($) {
  $(document).on('click', '.image-picker-button', function() {
    var field = $(this).parent().prev();
    set_image_uploader($(this), field[0])
  });

  $(document).on('click', '.file-picker-button', function() {
    var field = $(this).parent().prev();
    set_file_uploader($(this), field[0])
  });

  $(document).on('click', '.image-picker-clear, .file-picker-clear', function() {
    var field = $(this).parent().prev();
    clear_media($(this), field[0]);
  });

  function set_image_uploader(button, field) {
    var title = 'Select an Image',
      btnTitle = 'Use Image',
      typeInput = 'image';

    // Create the media frame.
    var temp_frame = wp.media({
      title: title,
      button: {
        text: btnTitle
      },
      library: { type: typeInput },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    temp_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      var attachment = temp_frame.state().get('selection').first().toJSON(), url = '';

      if(attachment.sizes.thumbnail) {
        url = attachment.sizes.thumbnail.url;
      } else {
        url = attachment.sizes.full.url;
      }

      $(field).val(attachment.id);
      $(button).parent().next().html('<img src="'+url+'"/>');

    });

    wp.media.frames.image_frame = temp_frame;
    wp.media.frames.image_frame.open();
    return false;
  }

  function set_file_uploader(button, field) {

    var title = 'Select a File',
      btnTitle = 'Use File',
      typeInput = '';

    // Create the media frame.
    var temp_frame = wp.media({
      title: title,
      button: {
        text: btnTitle
      },
      library: { type: typeInput },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    temp_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      var attachment = temp_frame.state().get('selection').first().toJSON();
      var link = '<a target="_blank" href="'+attachment.url+'">'+attachment.url+'</a>';

      $(field).val(attachment.id);
      $(button).parent().next().html(link);

    });

    wp.media.frames.file_frame = temp_frame;
    wp.media.frames.file_frame.open();
    return false;
  }

  function clear_media(button, field) {

    $(field).val('');
    $(button).parent().next().html('');

    return false;
  }

  // Gallery Image Uploader
  // -------------------------------------------------------------------------

  $(document).on('click', '.gallery-picker-button', function() {
    var list = $(this).parent().next();

    set_gallery_uploader($(this), list[0]);
  });

  $(document).on('click', '.gallery-picker-clear', function() {
    var list = $(this).parent().next();
    clear_gallery($(this), list[0]);
  });

  function set_gallery_uploader(button, list) {
    var title = 'Select Images',
      btnTitle = 'Use Images';

    // Create the media frame.
    var temp_frame = wp.media({
      title: title,
      button: {
        text: btnTitle
      },
      library: { type: 'image' },
      multiple: "toggle"  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    temp_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      var attachment = temp_frame.state().get('selection').toJSON();

      var l = attachment.length;

      for(i = 0; i < l; i++) {

        var field = $(button).parent().prev().clone(), use_url = '';

        if(attachment[i].sizes.thumbnail) {
          use_url = attachment[i].sizes.thumbnail.url;
        } else {
          use_url = attachment[i].sizes.full.url;
        }

        var item = $('<li class="image-picker-placeholder"><a href="#remove" class="tr-icon-remove2" title="Remove Image"></a><img src="'+use_url+'"/></li>');

        $(item)
          .append(field.val(attachment[i].id).attr('name', field.attr('name') + '[]'));

        $(list)
          .append(item);

        $(list).find('a').on('click', function(e) {
          e.preventDefault();
          $(this).parent().remove();
        })
      }

    });

    wp.media.frames.gallery_frame = temp_frame;
    wp.media.frames.gallery_frame.open();
    return false;
  }

  function clear_gallery(button, field) {

    if(confirm('Remove all images?')) {
      $(field).html('');
    }

    return false;
  }

  $('.tr-gallery-list a').on('click', function(e) {
    e.preventDefault();
    $(this).parent().remove();
  })

});
