jQuery(document).ready ($) ->

  set_image_uploader = (button, field) ->
    title = 'Select an Image'
    btnTitle = 'Use Image'
    typeInput = 'image'
    # Create the media frame.
    temp_frame = wp.media(
      title: title
      button: text: btnTitle
      library: type: typeInput
      multiple: false)
    # When an image is selected, run a callback.
    temp_frame.on 'select', ->
# We set multiple to false so only get one image from the uploader
      attachment = temp_frame.state().get('selection').first().toJSON()
      url = ''
      if attachment.sizes.thumbnail
        url = attachment.sizes.thumbnail.url
      else
        url = attachment.sizes.full.url
      $(field).val attachment.id
      $(button).parent().next().html '<img src="' + url + '"/>'
      return
    wp.media.frames.image_frame = temp_frame
    wp.media.frames.image_frame.open()
    false

  set_file_uploader = (button, field) ->
    title = 'Select a File'
    btnTitle = 'Use File'
    typeInput = ''
    # Create the media frame.
    temp_frame = wp.media(
      title: title
      button: text: btnTitle
      library: type: typeInput
      multiple: false)
    # When an image is selected, run a callback.
    temp_frame.on 'select', ->
# We set multiple to false so only get one image from the uploader
      attachment = temp_frame.state().get('selection').first().toJSON()
      link = '<a target="_blank" href="' + attachment.url + '">' + attachment.url + '</a>'
      $(field).val attachment.id
      $(button).parent().next().html link
      return
    wp.media.frames.file_frame = temp_frame
    wp.media.frames.file_frame.open()
    false

  clear_media = (button, field) ->
    $(field).val ''
    $(button).parent().next().html ''
    false

  set_gallery_uploader = (button, list) ->
    title = 'Select Images'
    btnTitle = 'Use Images'
    # Create the media frame.
    temp_frame = wp.media(
      title: title
      button: text: btnTitle
      library: type: 'image'
      multiple: 'toggle')
    # When an image is selected, run a callback.
    temp_frame.on 'select', ->
# We set multiple to false so only get one image from the uploader
      attachment = temp_frame.state().get('selection').toJSON()
      l = attachment.length
      i = 0
      while i < l
        field = $(button).parent().prev().clone()
        use_url = ''
        if attachment[i].sizes.thumbnail
          use_url = attachment[i].sizes.thumbnail.url
        else
          use_url = attachment[i].sizes.full.url
        item = $('<li class="image-picker-placeholder"><a href="#remove" class="tr-icon-remove2" title="Remove Image"></a><img src="' + use_url + '"/></li>')
        $(item).append field.val(attachment[i].id).attr('name', field.attr('name') + '[]')
        $(list).append item
        $(list).find('a').on 'click', (e) ->
          e.preventDefault()
          $(this).parent().remove()
          return
        i++
      return
    wp.media.frames.gallery_frame = temp_frame
    wp.media.frames.gallery_frame.open()
    false

  clear_gallery = (button, field) ->
    if confirm('Remove all images?')
      $(field).html ''
    false

  $(document).on 'click', '.image-picker-button', ->
    field = $(this).parent().prev()
    set_image_uploader $(this), field[0]
    return
  $(document).on 'click', '.file-picker-button', ->
    field = $(this).parent().prev()
    set_file_uploader $(this), field[0]
    return
  $(document).on 'click', '.image-picker-clear, .file-picker-clear', ->
    field = $(this).parent().prev()
    clear_media $(this), field[0]
    return
  # Gallery Image Uploader
  # -------------------------------------------------------------------------
  $(document).on 'click', '.gallery-picker-button', ->
    list = $(this).parent().next()
    set_gallery_uploader $(this), list[0]
    return
  $(document).on 'click', '.gallery-picker-clear', ->
    list = $(this).parent().next()
    clear_gallery $(this), list[0]
    return
  $('.tr-gallery-list a').on 'click', (e) ->
    e.preventDefault()
    $(this).parent().remove()
    return
  return