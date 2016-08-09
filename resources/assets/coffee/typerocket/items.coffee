jQuery(document).ready ($) ->

  clear_items = (button, field) ->
    if confirm('Remove all items?')
      $(field).val ''
      $(button).parent().next().html ''
    false

  $(document).on 'click', '.items-list-button', ->
    $ul = $(this).parent().next()
    name = $ul.attr('name')
    if name
      $ul.data 'name', name
    name = $ul.data('name')
    $ul.prepend $('<li class="item"><div class="move tr-icon-menu"></div><a href="#remove" class="tr-icon-remove2 remove" title="Remove Item"></a><input type="text" name="' + name + '[]" /></li>').hide().delay(10).slideDown(150).scrollTop('100%')
    return
  $(document).on 'click', '.items-list-clear', ->
    field = $(this).parent().prev()
    clear_items $(this), field[0]
    return
  $(document).on 'click', '.tr-items-list .remove', ->
    $(this).parent().slideUp 150, ->
      $(this).remove()
      return
    return
  return