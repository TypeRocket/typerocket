jQuery(document).ready(function($) {

    $('.matrix-group').on('click', '.matrix-button', function(e) {
        var $that = $(this);
        var mxid = $that.data('id');
        var $fields = $( '.matrix-fields-' + mxid ), $select = $( '.matrix-select-' + mxid );

        $.ajax({
            url: tr_matrix_url + '/' + mxid + '/' + $select.val() ,
            data: {id: $that.data('id'), type: $select.find('option[value="' +$select.val()+ '"]').text() },
            success: function(data) {
                $fields.prepend( '<div class="matrix-field-group">' + data + '</div>');
            },
            dataType: 'html'
        });
    });
});
