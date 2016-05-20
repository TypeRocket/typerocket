jQuery.fn.TypeRocketLink = (type = 'any', taxonomy = '') ->

  that = this
  search = encodeURI this.val()
  param = 'post_type='+type+'&s='+search

  if taxonomy
    param += '&taxonomy='+taxonomy

  jQuery.getJSON '/wp-json/typerocket/v1/search?'+param, (data) ->
    if data
      that.next().next().next().html ''
      that.next().next().next().append '<li class="tr-link-search-result-title">Results'
      for item in data
        if item.post_title
          title = item.post_title + '(' + item.post_type + ')'
          id = item.ID
        else
          title = item.name
          id = item.term_id

        that.next().next().next().append '<li class="tr-link-search-result" data-id="'+id+'" >'+title

  @

tr_delay = do ->
  timer = 0
  (callback, ms) ->
    clearTimeout timer
    timer = setTimeout(callback, ms)
    return

jQuery(document).ready ($) ->
  $('.typerocket-container').on 'keyup', '.tr-link-search-input', ->
    that = $(this)
    type = $(this).data 'posttype'
    taxonomy = $(this).data 'taxonomy'
    tr_delay (->
      that.TypeRocketLink type, taxonomy
      return
    ), 250

  $('.typerocket-container').on 'click', '.tr-link-search-result', ->
    id = $(this).data 'id'
    title = $(this).text()
    $(this).parent().prev().html 'Selection: <b>'+title+'</b>'
    $(this).parent().prev().prev().val id
    $(this).parent().prev().prev().prev().focus().val ''
    $(this).parent().html ''