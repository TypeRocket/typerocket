jQuery(document).ready ($) ->
  $('.typerocket-container').on 'click', '.builder-select-option', (e) ->
    $that = $(this)
    if !$that.hasClass('disabled')
      mxid = $that.data('id')
      group = $that.data('folder')
      $fields = $('#' + mxid)
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
          data.prependTo($fields)
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