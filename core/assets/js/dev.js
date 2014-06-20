jQuery.fn.selectText = function(){
  var doc = document
    , element = this[0]
    , range, selection
    ;
  if (doc.body.createTextRange) {
    range = document.body.createTextRange();
    range.moveToElementText(element);
    range.select();
  } else if (window.getSelection) {
    selection = window.getSelection();
    range = document.createRange();
    range.selectNodeContents(element);
    selection.removeAllRanges();
    selection.addRange(range);
  }
};

jQuery(document).ready(function($) {

  $(".debug").each(function() {
   // $(this).opentip("Copy the reveled PHP code to access the data stored by this field and use it in your theme template files. (Note: Post type data must be accessed from within 'The Loop')", { delay: 1, background: '#ffffff', borderColor: '#ccc', target: $(this).next(), tipJoint: 'top', borderRadius: 3 });
  });

  $('.typerocket-container').on('click', '.field', function(){
    $(this).selectText();
  })

});