jQuery.fn.TypeRocketLink = (type = 'any') ->

  that = this
  search = encodeURI this.val()

  jQuery.getJSON '/wp-json/typerocket/v1/search?post_type='+type+'&s='+search, (data) ->
    console.log data

    if data
      that.next().next().html ''
      for post in data
        that.next().next().next().append '<li class="tr-link-search-result" data-id="'+post.ID+'" >'+post.post_title
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
    tr_delay (->
      that.TypeRocketLink()
      return
    ), 300

  $('.typerocket-container').on 'click', '.tr-link-search-result', ->
    id = $(this).data 'id'
    title = $(this).text()
    $(this).parent().prev().text title
    $(this).parent().prev().prev().val id