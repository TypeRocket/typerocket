jQuery(document).ready(function ($) {

    $('.typerocket-container').on('click', '.matrix-button', function (e) {
        var $that = $(this);
        console.log('clicked matrix');

        if (!$that.is(":disabled")) {
            var mxid = $that.data('id'), group = $that.data('folder');
            var $fields = $('#' + mxid), $select = $('select[data-mxid="' + mxid + '"]');
            var button_txt = $that.val();
            var type = $select.val();
            var callbacks = TypeRocket.repeaterCallbacks;

            $that.attr("disabled", "disabled").val('Adding...');

            var url = '/typerocket_matrix_api/v1/' + group + '/' + type, form_group = $select.data('group');

            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'html',
                data: {form_group: form_group},
                success: function (data) {
                    data = $(data);

                    for (var ri = 0; callbacks.length > ri; ri++) {
                        if (typeof callbacks[ri] === "function") {
                            callbacks[ri](data);
                        }
                    }

                    data.prependTo($fields).hide().delay(10).slideDown(300).scrollTop('100%');


                    if ($.isFunction($.fn.sortable)) {
                        var $sortables = $fields.find('.tr-gallery-list'),
                            $items_list = $fields.find('.tr-items-list'),
                            $repeater_fields = $fields.find('.tr-repeater-fields');
                        if ($sortables.length > 0) {
                            $sortables.sortable();
                        }

                        if ($repeater_fields.length > 0) {
                            $repeater_fields.sortable({
                                connectWith: ".tr-repeater-group",
                                handle: ".repeater-controls"
                            });
                        }

                        if ($items_list.length > 0) {
                            $items_list.sortable({
                                connectWith: ".item",
                                handle: ".move"
                            });
                        }

                    }

                    $that.val(button_txt).removeAttr("disabled", "disabled");

                },
                error: function (jqXHR) {
                    $that.val('Try again - Error ' + jqXHR.status).removeAttr("disabled", "disabled");
                }
            });
        }

    });
});
