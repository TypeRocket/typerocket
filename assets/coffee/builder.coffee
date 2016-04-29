jQuery(document).ready ($) ->
  $('.typerocket-container').on 'click', '.tr-builder-add-button', (e) ->
    e.preventDefault()
    select = $(this).next()

    overlay = $('<div>').addClass('tr-builder-select-overlay').on 'click', ->
      $(this).remove()
      $('.tr-builder-select').fadeOut()

    $('body').append overlay
    select.fadeIn()

  $('.typerocket-container').on 'click', '.tr-builder-component-control', (e) ->
    e.preventDefault()
    $(this).parent().children().removeClass 'active'
    id = $(this).addClass('active').parent().data 'id'
    index = $(this).index()
    frame = $('#frame-'+id)
    components = frame.children()
    components.removeClass 'active'
    component = components[index]
    $(component).addClass 'active'

  $('.typerocket-container').on 'click', '.tr-remove-builder-component', (e) ->
    e.preventDefault()
    if confirm('Remove component?')
      control = $(this).parent()
      control.parent().children().removeClass 'active'
      id = control.parent().data 'id'
      index = $(this).parent().index()
      frame = $('#frame-'+id)
      components = frame.children()
      component = components[index]
      $(component).remove()
      control.remove()

  $('.tr-components').sortable
    start: (e, ui) ->
      ui.item.startPos = ui.item.index()

    update: (e, ui) ->
      id = ui.item.parent().data 'id'
      frame = $('#frame-'+id)
      components = frame.children()
      index = ui.item.index()
      old = ui.item.startPos
      builder = components.splice old, 1
      components.splice index, 0, builder[0]
      frame.html components


  $('.typerocket-container').on 'click', '.builder-select-option', (e) ->
    $that = $(this)
    $that.parent().fadeOut()
    $('.tr-builder-select-overlay').remove()
    if !$that.hasClass('disabled')
      mxid = $that.data('id')
      group = $that.data('folder')
      $fields = $('#frame-' + mxid)
      $components = $('#components-' + mxid)
      $select = $('ul[data-mxid="' + mxid + '"]')
      type = $that.data('value')
      callbacks = TypeRocket.repeaterCallbacks
      $that.addClass 'disabled'
      url = '/typerocket_builder_api/v1/' + group + '/' + type
      form_group = $select.data('group')
      $.ajax
        url: url
        method: 'POST'
        dataType: 'html'
        data: form_group: form_group
        success: (data) ->
          data = $(data)
          ri = 0
          while callbacks.length > ri
            if typeof callbacks[ri] == 'function'
              callbacks[ri] data
            ri++
          $fields.children().removeClass 'active'
          $components.children().removeClass 'active'
          data.prependTo($fields).addClass 'active'
          $components.prepend '<li class="active tr-builder-component-control">'+$that.text()+'<span class="remove tr-remove-builder-component"></span>'
          if $.isFunction($.fn.sortable)
            $sortables = $fields.find('.tr-gallery-list')
            $items_list = $fields.find('.tr-items-list')
            $repeater_fields = $fields.find('.tr-repeater-fields')
            if $sortables.length > 0
              $sortables.sortable()
            if $repeater_fields.length > 0
              $repeater_fields.sortable
                connectWith: '.tr-repeater-group'
                handle: '.repeater-controls'
            if $items_list.length > 0
              $items_list.sortable
                connectWith: '.item'
                handle: '.move'
          $that.removeClass 'disabled'
          return
        error: (jqXHR) ->
          $that.val('Try again - Error ' + jqXHR.status).removeAttr 'disabled', 'disabled'
          return
    return
  return