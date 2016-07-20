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

    console.log url

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
          # Call it, since we have confirmed it is callable​
          TypeRocket.httpCallbacks[ri] data
        ri++
      type = ''
      if data.valid == true
        type = 'success'
      else
        type = 'error'
      if data.flash == true
        jQuery('body').prepend jQuery('<div class="typerocket-rest-alert node-' + type + ' ">' + data.message + '</div>').delay(1500).fadeOut(100, ->
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
      $.typerocketHttp.send 'DELETE', $(this).attr('href'), [], false