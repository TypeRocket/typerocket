jQuery.fn.selectText = ->
  doc = document
  element = @[0]
  range = undefined
  selection = undefined
  if doc.body.createTextRange
    range = document.body.createTextRange()
    range.moveToElementText element
    range.select()
  else if window.getSelection
    selection = window.getSelection()
    range = document.createRange()
    range.selectNodeContents element
    selection.removeAllRanges()
    selection.addRange range
  return

jQuery(document).ready ($) ->
  $('.typerocket-container').on 'click', '.field', ->
    $(this).selectText()
    return
  return