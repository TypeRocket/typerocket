jQuery(document).ready(function($) {

    $('.tr-matrix').on('click', '.matrix-button', function(e) {
        var $that = $(this);

        if(!$that.hasClass('matrix-disabled')) {
            var mxid = $that.data('id');
            var $fields = $( '.matrix-fields-' + mxid ), $select = $( '.matrix-select-' + mxid );
            var button_txt = $that.val();

            $that.val('Getting Group').addClass('matrix-disabled');

            $.ajax({
                url: tr_matrix_url + '/' + mxid + '/' + $select.val() ,
                data: {id: $that.data('id'), form_group: tr_matrix_form_group, type: $select.find('option[value="' +$select.val()+ '"]').text() },
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
