jQuery(document).ready ($) ->
  val = ''
  desc = ''
  orig_desc = $('#tr-seo-preview-google-desc-orig').text()
  orig_title = $('#tr-seo-preview-google-title-orig').text()
  $('#tr_title').keyup ->
    val = $(this).val().substring(0, 59)
    title = $('#tr-seo-preview-google-title')
    title.text val
    console.log orig_desc
    if val.length > 0
      title.text val
    else
      title.text orig_title
    return
  $('#tr_description').keyup ->
    desc = $(this).val().substring(0, 156)
    if desc.length > 0
      $('#tr-seo-preview-google-desc').text desc
    else
      $('#tr-seo-preview-google-desc').text orig_desc
    return
  $('#tr_redirect_lock').click (e) ->
    $($(this).attr('href')).removeAttr('readonly').focus()
    $(this).fadeOut()
    e.preventDefault()
    return
  return