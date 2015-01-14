jQuery(document).ready(function($) {

  $(document).on('click', '.items-list-button', function() {
    var $ul = $(this).parent().next();
    var name = $ul.attr('name');

    if(name) {
      $ul.data('name', name)
    }

    name = $ul.data('name');

    $ul.prepend($('<li class="item"><div class="move tr-icon-menu"></div><a href="#remove" class="tr-icon-remove2 remove" title="Remove Item"></a><input type="text" name="'+name+'[]" /></li>').hide().delay(10).slideDown(150).scrollTop('100%'));
  });

  $(document).on('click', '.items-list-clear', function() {
    var field = $(this).parent().prev();
    clear_items($(this), field[0]);
  });

  $(document).on('click', '.tr-items-list .remove', function() {
    $(this).parent().slideUp(150, function() {$(this).remove()});
  });

  function clear_items(button, field) {

    if(confirm('Remove all items?')) {
      $(field).val('');
      $(button).parent().next().html('');
    }

    return false;
  }

});
