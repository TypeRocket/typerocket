jQuery.fn.TypeRocketLink = (type = 'page') ->

  search = encodeURI this.val()

  jQuery.getJSON '/wp-json/typerocket/v1/search?post_type='+type+'&s='+search, (data) ->
    console.log data


  @

tr_delay = do ->
  timer = 0
  (callback, ms) ->
    clearTimeout timer
    timer = setTimeout(callback, ms)
    return

jQuery(document).ready ($) ->
  $('.typerocket-container').on 'keyup', '.tr-link', ->
    that = $(this)
    tr_delay (->
      that.TypeRocketLink()
      return
    ), 300