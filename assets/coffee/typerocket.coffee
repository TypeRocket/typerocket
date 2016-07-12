jQuery(document).ready ($) ->
# fork from theme options framework

  editorHeight = ->
  # Editor Height (needs improvement)
    $('.wp-editor-wrap').each ->
      editor_iframe = $(this).find('iframe')
      if editor_iframe.height() < 30
        editor_iframe.css 'height': 'auto'
      return
    return

  editorHeight()
  # sorting, colors, dates

  add_sorting = (obj) ->
    if $.isFunction($.fn.sortable)
      $sortables = $(obj).find('.tr-gallery-list')
      $items_list = $(obj).find('.tr-items-list')
      $repeater_fields = $(obj).find('.tr-repeater-fields')
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
    return

  add_date_picker = (obj) ->
    if $.isFunction($.fn.datepicker)
      $(obj).find('.date-picker[name]').each ->
        $(this).datepicker beforeShow: (input, inst) ->
          $('#ui-datepicker-div').addClass 'typerocket-datepicker'
          return
        return
    return

  add_color_picker = (obj) ->
    if $.isFunction($.fn.wpColorPicker)
      $(obj).find('.color-picker[name]').each ->
        pal = $(this).attr('id') + '_color_palette'
        console.log pal
        console.log window[pal]
        settings = palettes: window[pal]
        $(this).wpColorPicker settings
        return
    return

  add_editor = (obj) ->
    if $.isFunction($.fn.redactor)
      $(obj).find('.typerocket-editor[name]').each ->
        $(this).redactor()
        return
    return

  $trContainer = $('.typerocket-container')
  add_sorting $trContainer
  add_date_picker $trContainer
  add_color_picker $trContainer
  add_editor $trContainer
  TypeRocket.repeaterCallbacks.push add_date_picker
  TypeRocket.repeaterCallbacks.push add_color_picker
  TypeRocket.repeaterCallbacks.push add_editor
  $trContainer.on 'keyup', 'input[maxlength], textarea[maxlength]', ->
    $that = $(this)
    $that.next().find('span').text tr_max.len(this)
    return
  # Tabs
  $('.tr-tabs li').each ->
    $(this).click (e) ->
      $(this).addClass('active').siblings().removeClass 'active'
      section = $(this).find('a').attr('href')
      $(section).addClass('active').siblings().removeClass 'active'
      editorHeight()
      e.preventDefault()
      return
    return
  $('.contextual-help-tabs a').click ->
    editorHeight()
    return
  # repeaters
  repeaterClone =
    init: ->
      obj = this
      # Add
      # $group_template | div.tr-repeater-group-template | contains all fields for repeating
      # hash | int | used for keying arrayed data to group repeated field groups
      $(document).on 'click', '.tr-repeater .controls .add', ->
        $group_template = $($(this).parent().parent().next().clone()).removeClass('tr-repeater-group-template').addClass('tr-repeater-group')
        hash = (new Date).getTime()
        replacement_id = $group_template.data('id')
        dev_notes = $group_template.find('.dev .field span')
        data_name = $group_template.find('[data-name]')
        data_name_filtered = $group_template.find('.tr-repeater-group-template [data-name]')
        $(data_name).each ->
          name = obj.nameParse($(this).data('name'), hash, replacement_id)
          $(this).attr 'name', name
          $(this).attr 'data-name', null
          return
        $(dev_notes).each ->
          name = obj.nameParse($(this).html(), hash, replacement_id)
          $(this).html name
          return
        $(data_name_filtered).each ->
          $(this).attr 'data-name', $(this).attr('name')
          $(this).attr 'name', null
          return
        # add sorting
        add_sorting $group_template
        # callback group
        ri = 0
        while TypeRocket.repeaterCallbacks.length > ri
          if typeof TypeRocket.repeaterCallbacks[ri] == 'function'
# Call it, since we have confirmed it is callable​
            TypeRocket.repeaterCallbacks[ri] $group_template
          ri++
        $fields_div = $(this).parent().parent().next().next()
        $group_template.prependTo($fields_div).hide().delay(10).slideDown(300).scrollTop '100%'
        return
      # remove
      $(document).on 'click', '.tr-repeater .repeater-controls .remove', (e) ->
        $(this).parent().parent().slideUp 300, ->
          $(this).remove()
          return
        e.preventDefault()
        return
      # collapse
      $(document).on 'click', '.tr-repeater .repeater-controls .collapse', (e) ->
        $group = $(this).parent().parent()
        if $group.hasClass('tr-repeater-group-collapsed') or $group.height() == 90
          $group.removeClass 'tr-repeater-group-collapsed'
          $group.addClass 'tr-repeater-group-expanded'
          $group.attr 'style', ''
        else
          $group.removeClass 'tr-repeater-group-expanded'
          $group.addClass 'tr-repeater-group-collapsed'
        e.preventDefault()
        return
      # tr_action_collapse
      $(document).on 'click', '.tr-repeater .controls .tr_action_collapse', (e) ->
        $groups_group = $(this).parent().parent().next().next()
        if $(this).val() == 'Contract'
          $(this).val 'Expand'
          $groups_group.find('> .tr-repeater-group').animate { height: '90px' }, 200
        else
          $(this).val 'Contract'
          $groups_group.find('> .tr-repeater-group').attr 'style', ''
        $collapse = $(this).parent().parent().next().next()
        if $collapse.hasClass('tr-repeater-collapse')
          $collapse.toggleClass 'tr-repeater-collapse'
          $collapse.find('> .tr-repeater-group').removeClass('tr-repeater-group-collapsed').attr 'style', ''
        else
          $collapse.toggleClass 'tr-repeater-collapse'
          $collapse.find('> .tr-repeater-group').removeClass 'tr-repeater-group-expanded'
        e.preventDefault()
        return
      # clear
      $(document).on 'click', '.tr-repeater .controls .clear', (e) ->
        if confirm('Remove all items?')
          $(this).parent().parent().next().next().html ''
        e.preventDefault()
        return
      # flip
      $(document).on 'click', '.tr-repeater .controls .flip', (e) ->
        if confirm('Flip order of all items?')
          items = $(this).parent().parent().next().next()
          items.children().each (i, item) ->
            items.prepend item
            return
        e.preventDefault()
        return
      return
    nameParse: (string, hash, id) ->
      liveTemplate = string
      temp = new Booyah
      liveTemplate = temp.addTemplate(liveTemplate).addTag('{{ ' + id + ' }}', hash).ready()
      liveTemplate
  repeaterClone.init()
  # max length input and textarea
  tr_max = len: (that) ->
    $that = $(that)
    parseInt($that.attr('maxlength')) - ($that.val().length)
  return