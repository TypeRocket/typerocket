jQuery(document).ready(function($) {

    $('.tr-matrix').on('click', '.matrix-button', function(e) {
        var $that = $(this);

        if(!$that.hasClass('matrix-disabled')) {
            var mxid = $that.data('id'), folder = $that.data('folder');
            var $fields = $( '.matrix-fields-' + mxid ), $select = $( '.matrix-select-' + mxid );
            var button_txt = $that.val();

            $that.val('Getting Group').addClass('matrix-disabled');

            var url = tr_matrix_url + '/' + folder + '/' + $select.val();

            console.log(url);

            $.ajax({
                url:  url,
                data: {id: $that.data('folder'), form_group: tr_matrix_form_group, type: $select.find('option[value="' +$select.val()+ '"]').data('file') },
                success: function(data) {
                    data = $('<div class="matrix-field-group tr-repeater-group">' + data + '</div></div>');

                    data.prependTo($fields).hide().delay(10).slideDown(300).scrollTop('100%');
                },
                dataType: 'html',
                complete: function() {
                    $that.val(button_txt).removeClass('matrix-disabled');
                }
            });
        }

    });
});
