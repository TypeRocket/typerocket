jQuery.typerocketHttp =
  get: (url, data) ->
    @send 'GET', url, data
    return
  post: (url, data) ->
    @send 'POST', url, data
    return
  put: (url, data) ->
    @send 'PUT', url, data
    return
  delete: (url, data) ->
    @send 'DELETE', url, data
    return
  send: (method, url, data, trailing = true ) ->
    if trailing
      url = @tools.addTrailingSlash(url)

    @tools.ajax
      method: method
      data: data
      url: url
    return
  tools:
    stripTrailingSlash: (str) ->
      if str.substr(-1) == '/'
        return str.substr(0, str.length - 1)
      str
    addTrailingSlash: (str) ->
      if ! str.indexOf('.php')
        return str.replace(/\/?(\?|#|$)/, '/$1')
      str
    ajax: (obj) ->
      tools = this
      settings =
        method: 'GET'
        data: {}
        dataType: 'json'
        success: (data) ->
          if data.redirect
            window.location = data.redirect
            return
          tools.checkData data
          return
        error: (hx, error, message) ->
          alert 'Your request had an error. ' + hx.status + ' - ' + message
          return
      jQuery.extend settings, obj
      jQuery.ajax settings
      return
    checkData: (data) ->
      # callback group
      ri = 0
      while TypeRocket.httpCallbacks.length > ri
        if typeof TypeRocket.httpCallbacks[ri] == 'function'
          TypeRocket.httpCallbacks[ri] data
        ri++
      type = data.message_type

      if data.flash == true
        jQuery('body').prepend jQuery('<div class="typerocket-ajax-alert tr-alert-' + type + ' ">' + data.message + '</div>').fadeIn(200).delay(2000).fadeOut( 200, ->
          jQuery(this).remove()
          return
        )
      return
jQuery(document).ready ($) ->
  $('form.typerocket-ajax-form').on 'submit', (e) ->
    e.preventDefault()
    TypeRocket.lastSubmittedForm = $(this)
    $.typerocketHttp.send 'POST', $(this).attr('action'), $(this).serialize()
    return

  $('.tr-delete-row-rest-button').on 'click', (e) ->
    e.preventDefault()

    if confirm("Confirm Delete.")
      target = $(this).data('target');
      $(target).remove()

      data =
        _tr_ajax_request: '1'
        _method: 'DELETE'
      $.typerocketHttp.send 'POST', $(this).attr('href'), data , false