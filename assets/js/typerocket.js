jQuery(document).ready(function ($) {

    // fork from theme options framework
    function editorHeight() {
        // Editor Height (needs improvement)
        $('.wp-editor-wrap').each(function () {
            var editor_iframe = $(this).find('iframe');
            if (editor_iframe.height() < 30) {
                editor_iframe.css({'height': 'auto'});
            }
        });
    }

    editorHeight();

    // sorting, colors, dates
    var add_sorting = function (obj) {
            if ($.isFunction($.fn.sortable)) {
                var $sortables = $(obj).find('.tr-gallery-list'),
                    $items_list = $(obj).find('.tr-items-list'),
                    $repeater_fields = $(obj).find('.tr-repeater-fields');
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
        },
        add_date_picker = function (obj) {
            if ($.isFunction($.fn.datepicker)) {
                $(obj).find('.date-picker[name]').each(function () {
                    $(this).datepicker({
                        beforeShow: function(input, inst) {
                            $('#ui-datepicker-div').addClass('typerocket-datepicker');
                        }
                    });
                });
            }
        },
        add_color_picker = function (obj) {
            if ($.isFunction($.fn.wpColorPicker)) {
                $(obj).find('.color-picker[name]').each(function () {
                    var pal = $(this).attr('id') + '_color_palette',
                        settings = {palettes: window[pal]};
                    $(this).wpColorPicker(settings);
                });
            }
        },
        add_editor = function (obj) {
            if ($.isFunction($.fn.redactor)) {
                $(obj).find('.typerocket-editor[name]').each(function () {
                    $(this).redactor();
                });
            }
        };

    var $trContainer = $('.typerocket-container');
    add_sorting($trContainer);
    add_date_picker($trContainer);
    add_color_picker($trContainer);
    add_editor($trContainer);

    TypeRocket.repeaterCallbacks.push(add_date_picker);
    TypeRocket.repeaterCallbacks.push(add_color_picker);
    TypeRocket.repeaterCallbacks.push(add_editor);


    $trContainer.on('keyup', 'input[maxlength], textarea[maxlength]', function () {
        var $that = $(this);
        $that.next().find('span').text(tr_max.len(this));
    });

    // Tabs
    $('.tr-tabs li').each(function () {
        $(this).click(function (e) {
            $(this).addClass('active').siblings().removeClass('active');
            var section = $(this).find('a').attr('href');
            $(section).addClass('active').siblings().removeClass('active');
            editorHeight();

            e.preventDefault();
        });
    });

    $('.contextual-help-tabs a').click(function () {
        editorHeight()
    });


    // repeaters
    var repeaterClone = {

        init: function () {
            var obj = this;

            // Add
            // $group_template | div.tr-repeater-group-template | contains all fields for repeating
            // hash | int | used for keying arrayed data to group repeated field groups
            $(document).on('click', '.tr-repeater .controls .add', function () {
                var $group_template = $($(this).parent().parent().next().clone()).removeClass('tr-repeater-group-template').addClass('tr-repeater-group'),
                    hash = new Date().getTime(),
                    replacement_id = $group_template.data('id'),
                    dev_notes = $group_template.find('.dev .field span'),
                    data_name = $group_template.find('[data-name]'),
                    data_name_filtered = $group_template.find('.tr-repeater-group-template [data-name]');

                $(data_name).each(function () {
                    var name = obj.nameParse($(this).data('name'), hash, replacement_id);
                    $(this).attr('name', name);
                    $(this).attr('data-name', null);
                });

                $(dev_notes).each(function () {
                    var name = obj.nameParse($(this).html(), hash, replacement_id);
                    $(this).html(name);
                });

                $(data_name_filtered).each(function () {
                    $(this).attr('data-name', $(this).attr('name'));
                    $(this).attr('name', null);
                });

                // add sorting
                add_sorting($group_template);

                // callback group
                for (var ri = 0; TypeRocket.repeaterCallbacks.length > ri; ri++) {
                    if (typeof TypeRocket.repeaterCallbacks[ri] === "function") {
                        // Call it, since we have confirmed it is callable​
                        TypeRocket.repeaterCallbacks[ri]($group_template);
                    }
                }

                var $fields_div = $(this).parent().parent().next().next();

                $group_template.prependTo($fields_div).hide().delay(10).slideDown(300).scrollTop('100%');

            });

            // remove
            $(document).on('click', '.tr-repeater .repeater-controls .remove', function (e) {
                $(this).parent().parent().slideUp(300, function () {
                    $(this).remove()
                });
                e.preventDefault();
            });

            // collapse
            $(document).on('click', '.tr-repeater .repeater-controls .collapse', function (e) {
                var $group = $(this).parent().parent();

                if ($group.hasClass('tr-repeater-group-collapsed') || $group.height() == 90) {
                    $group.removeClass('tr-repeater-group-collapsed');
                    $group.addClass('tr-repeater-group-expanded');
                    $group.attr('style', '');
                } else {
                    $group.removeClass('tr-repeater-group-expanded');
                    $group.addClass('tr-repeater-group-collapsed');
                }

                e.preventDefault();
            });

            // tr_action_collapse
            $(document).on('click', '.tr-repeater .controls .tr_action_collapse', function (e) {

                var $groups_group = $(this).parent().parent().next().next();

                if ($(this).val() == "Contract") {
                    $(this).val("Expand");
                    $groups_group.find('> .tr-repeater-group').animate({height: '90px'}, 200);
                }
                else {
                    $(this).val("Contract");
                    $groups_group.find('> .tr-repeater-group').attr('style', '');
                }

                var $collapse = $(this).parent().parent().next().next();

                if ($collapse.hasClass('tr-repeater-collapse')) {
                    $collapse.toggleClass('tr-repeater-collapse');
                    $collapse.find('> .tr-repeater-group').removeClass('tr-repeater-group-collapsed').attr('style', '');
                } else {
                    $collapse.toggleClass('tr-repeater-collapse');
                    $collapse.find('> .tr-repeater-group').removeClass('tr-repeater-group-expanded');
                }

                e.preventDefault();
            });

            // clear
            $(document).on('click', '.tr-repeater .controls .clear', function (e) {
                if (confirm('Remove all items?')) {
                    $(this).parent().parent().next().next().html('');
                }
                e.preventDefault();
            });

            // flip
            $(document).on('click', '.tr-repeater .controls .flip', function (e) {
                if (confirm('Flip order of all items?')) {
                    var items = $(this).parent().parent().next().next();
                    items.children().each(function (i, item) {
                        items.prepend(item)
                    })
                }
                e.preventDefault();
            });

        },

        nameParse: function (string, hash, id) {
            var liveTemplate = string;
            var temp = booyah;
            liveTemplate = temp
                .addTemplate(liveTemplate)
                .addTag('{{ ' + id + ' }}', hash)
                .ready();

            return liveTemplate;
        }

    };

    repeaterClone.init();

    // max length input and textarea
    var tr_max = {
        len: function (that) {
            var $that = $(that);

            return parseInt($that.attr('maxlength')) - $that.val().length;

        }
    };

});
