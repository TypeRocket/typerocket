jQuery(document).ready(function($) {

    $('.typerocket-container').on('click', '.matrix-button', function(e) {
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


                    if( $.isFunction($.fn.sortable) ) {
                        var $sortables = $fields.find('.tr-gallery-list'),
                            $items_list = $fields.find('.tr-items-list'),
                            $repeater_fields = $fields.find('.tr-repeater-fields');
                        if($sortables.length > 0) {
                            $sortables.sortable();
                        }

                        if($repeater_fields.length > 0) {
                            $repeater_fields.sortable({
                                connectWith: ".tr-repeater-group",
                                handle: ".repeater-controls"
                            });
                        }

                        if($items_list.length > 0) {
                            $items_list.sortable({
                                connectWith: ".item",
                                handle: ".move"
                            });
                        }

                    }

                    if( $.isFunction($.fn.datepicker) ) {
                        $fields.find('.date-picker[name]').each(function(){
                            $(this).datepicker();
                        });
                    }

                    if( $.isFunction($.fn.wpColorPicker) ) {
                        $fields.find('.color-picker[name]').each(function(){
                            var pal = $(this).attr('id') + '_color_palette',
                                settings = { palettes: window[pal] };
                            $(this).wpColorPicker(settings);
                        });
                    }

                },
                dataType: 'html',
                complete: function() {
                    $that.val(button_txt).removeClass('matrix-disabled');
                }
            });
        }

    });
});