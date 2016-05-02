(function() {
  jQuery.typerocketHttp = {
    get: function(url, data) {
      this.send('GET', url, data);
    },
    post: function(url, data) {
      this.send('POST', url, data);
    },
    put: function(url, data) {
      this.send('PUT', url, data);
    },
    "delete": function(url, data) {
      this.send('DELETE', url, data);
    },
    send: function(method, url, data) {
      this.tools.ajax({
        method: method,
        data: data,
        url: this.tools.addTrailingSlash(url)
      });
    },
    tools: {
      stripTrailingSlash: function(str) {
        if (str.substr(-1) === '/') {
          return str.substr(0, str.length - 1);
        }
        return str;
      },
      addTrailingSlash: function(str) {
        if (str.substr(-1) !== '/') {
          str = str + '/';
        }
        return str;
      },
      ajax: function(obj) {
        var settings, tools;
        tools = this;
        settings = {
          method: 'GET',
          data: {},
          dataType: 'json',
          success: function(data) {
            if (data.redirect) {
              window.location = data.redirect;
              return;
            }
            tools.checkData(data);
          },
          error: function(hx, error, message) {
            alert('Your request had an error. ' + hx.status + ' - ' + message);
          }
        };
        jQuery.extend(settings, obj);
        jQuery.ajax(settings);
      },
      checkData: function(data) {
        var ri, type;
        ri = 0;
        while (TypeRocket.httpCallbacks.length > ri) {
          if (typeof TypeRocket.httpCallbacks[ri] === 'function') {
            TypeRocket.httpCallbacks[ri](data);
          }
          ri++;
        }
        type = '';
        if (data.valid === true) {
          type = 'success';
        } else {
          type = 'error';
        }
        if (data.flash === true) {
          jQuery('body').prepend(jQuery('<div class="typerocket-rest-alert node-' + type + ' ">' + data.message + '</div>').delay(1500).fadeOut(100, function() {
            jQuery(this).remove();
          }));
        }
      }
    }
  };

  jQuery(document).ready(function($) {
    $('form.typerocket-rest-form').on('submit', function(e) {
      e.preventDefault();
      TypeRocket.lastSubmittedForm = $(this);
      $.typerocketHttp.send('POST', $(this).data('api'), $(this).serialize());
    });
  });

}).call(this);


/**
 * Booyah!
 *
 * The easy mode template parser.
 *
 * User: Kevin Dees
 * Date: 9/6/13
 * Time: 10:37 AM
 */

(function() {
  this.Booyah = (function() {
    Booyah.prototype.templateTagKeys = [];

    Booyah.prototype.templateTagValues = [];

    Booyah.prototype.templateArray = [];

    Booyah.prototype.templateString = '';

    function Booyah() {
      this.templateTagKeys = [];
      this.templateTagValues = [];
      this.templateArray = [];
      this.templateString = '';
    }

    Booyah.prototype.ready = function() {
      this.templateString = this.templateArray.join('');
      this.replaceTags();
      return this.templateString;
    };

    Booyah.prototype.addTag = function(key, value) {
      this.templateTagKeys.push(key);
      this.templateTagValues.push(value);
      return this;
    };

    Booyah.prototype.addTemplate = function(string) {
      this.templateArray.push(string);
      return this;
    };

    Booyah.prototype.replaceTags = function() {
      var i, replaceTag, tagCount, withThisValue;
      tagCount = this.templateTagKeys.length;
      i = 0;
      while (tagCount > i) {
        replaceTag = this.templateTagKeys[i];
        withThisValue = this.templateTagValues[i];
        this.templateString = this.templateString.replace(new RegExp(replaceTag), withThisValue);
        i++;
      }
    };

    return Booyah;

  })();

}).call(this);

(function() {
  jQuery(document).ready(function($) {
    var $trContainer, add_color_picker, add_date_picker, add_editor, add_sorting, editorHeight, repeaterClone, tr_max;
    editorHeight = function() {
      $('.wp-editor-wrap').each(function() {
        var editor_iframe;
        editor_iframe = $(this).find('iframe');
        if (editor_iframe.height() < 30) {
          editor_iframe.css({
            'height': 'auto'
          });
        }
      });
    };
    editorHeight();
    add_sorting = function(obj) {
      var $items_list, $repeater_fields, $sortables;
      if ($.isFunction($.fn.sortable)) {
        $sortables = $(obj).find('.tr-gallery-list');
        $items_list = $(obj).find('.tr-items-list');
        $repeater_fields = $(obj).find('.tr-repeater-fields');
        if ($sortables.length > 0) {
          $sortables.sortable();
        }
        if ($repeater_fields.length > 0) {
          $repeater_fields.sortable({
            connectWith: '.tr-repeater-group',
            handle: '.repeater-controls'
          });
        }
        if ($items_list.length > 0) {
          $items_list.sortable({
            connectWith: '.item',
            handle: '.move'
          });
        }
      }
    };
    add_date_picker = function(obj) {
      if ($.isFunction($.fn.datepicker)) {
        $(obj).find('.date-picker[name]').each(function() {
          $(this).datepicker({
            beforeShow: function(input, inst) {
              $('#ui-datepicker-div').addClass('typerocket-datepicker');
            }
          });
        });
      }
    };
    add_color_picker = function(obj) {
      if ($.isFunction($.fn.wpColorPicker)) {
        $(obj).find('.color-picker[name]').each(function() {
          var pal, settings;
          pal = $(this).attr('id') + '_color_palette';
          settings = {
            palettes: window[pal]
          };
          $(this).wpColorPicker(settings);
        });
      }
    };
    add_editor = function(obj) {
      if ($.isFunction($.fn.redactor)) {
        $(obj).find('.typerocket-editor[name]').each(function() {
          $(this).redactor();
        });
      }
    };
    $trContainer = $('.typerocket-container');
    add_sorting($trContainer);
    add_date_picker($trContainer);
    add_color_picker($trContainer);
    add_editor($trContainer);
    TypeRocket.repeaterCallbacks.push(add_date_picker);
    TypeRocket.repeaterCallbacks.push(add_color_picker);
    TypeRocket.repeaterCallbacks.push(add_editor);
    $trContainer.on('keyup', 'input[maxlength], textarea[maxlength]', function() {
      var $that;
      $that = $(this);
      $that.next().find('span').text(tr_max.len(this));
    });
    $('.tr-tabs li').each(function() {
      $(this).click(function(e) {
        var section;
        $(this).addClass('active').siblings().removeClass('active');
        section = $(this).find('a').attr('href');
        $(section).addClass('active').siblings().removeClass('active');
        editorHeight();
        e.preventDefault();
      });
    });
    $('.contextual-help-tabs a').click(function() {
      editorHeight();
    });
    repeaterClone = {
      init: function() {
        var obj;
        obj = this;
        $(document).on('click', '.tr-repeater .controls .add', function() {
          var $fields_div, $group_template, data_name, data_name_filtered, dev_notes, hash, replacement_id, ri;
          $group_template = $($(this).parent().parent().next().clone()).removeClass('tr-repeater-group-template').addClass('tr-repeater-group');
          hash = (new Date).getTime();
          replacement_id = $group_template.data('id');
          dev_notes = $group_template.find('.dev .field span');
          data_name = $group_template.find('[data-name]');
          data_name_filtered = $group_template.find('.tr-repeater-group-template [data-name]');
          $(data_name).each(function() {
            var name;
            name = obj.nameParse($(this).data('name'), hash, replacement_id);
            $(this).attr('name', name);
            $(this).attr('data-name', null);
          });
          $(dev_notes).each(function() {
            var name;
            name = obj.nameParse($(this).html(), hash, replacement_id);
            $(this).html(name);
          });
          $(data_name_filtered).each(function() {
            $(this).attr('data-name', $(this).attr('name'));
            $(this).attr('name', null);
          });
          add_sorting($group_template);
          ri = 0;
          while (TypeRocket.repeaterCallbacks.length > ri) {
            if (typeof TypeRocket.repeaterCallbacks[ri] === 'function') {
              TypeRocket.repeaterCallbacks[ri]($group_template);
            }
            ri++;
          }
          $fields_div = $(this).parent().parent().next().next();
          $group_template.prependTo($fields_div).hide().delay(10).slideDown(300).scrollTop('100%');
        });
        $(document).on('click', '.tr-repeater .repeater-controls .remove', function(e) {
          $(this).parent().parent().slideUp(300, function() {
            $(this).remove();
          });
          e.preventDefault();
        });
        $(document).on('click', '.tr-repeater .repeater-controls .collapse', function(e) {
          var $group;
          $group = $(this).parent().parent();
          if ($group.hasClass('tr-repeater-group-collapsed') || $group.height() === 90) {
            $group.removeClass('tr-repeater-group-collapsed');
            $group.addClass('tr-repeater-group-expanded');
            $group.attr('style', '');
          } else {
            $group.removeClass('tr-repeater-group-expanded');
            $group.addClass('tr-repeater-group-collapsed');
          }
          e.preventDefault();
        });
        $(document).on('click', '.tr-repeater .controls .tr_action_collapse', function(e) {
          var $collapse, $groups_group;
          $groups_group = $(this).parent().parent().next().next();
          if ($(this).val() === 'Contract') {
            $(this).val('Expand');
            $groups_group.find('> .tr-repeater-group').animate({
              height: '90px'
            }, 200);
          } else {
            $(this).val('Contract');
            $groups_group.find('> .tr-repeater-group').attr('style', '');
          }
          $collapse = $(this).parent().parent().next().next();
          if ($collapse.hasClass('tr-repeater-collapse')) {
            $collapse.toggleClass('tr-repeater-collapse');
            $collapse.find('> .tr-repeater-group').removeClass('tr-repeater-group-collapsed').attr('style', '');
          } else {
            $collapse.toggleClass('tr-repeater-collapse');
            $collapse.find('> .tr-repeater-group').removeClass('tr-repeater-group-expanded');
          }
          e.preventDefault();
        });
        $(document).on('click', '.tr-repeater .controls .clear', function(e) {
          if (confirm('Remove all items?')) {
            $(this).parent().parent().next().next().html('');
          }
          e.preventDefault();
        });
        $(document).on('click', '.tr-repeater .controls .flip', function(e) {
          var items;
          if (confirm('Flip order of all items?')) {
            items = $(this).parent().parent().next().next();
            items.children().each(function(i, item) {
              items.prepend(item);
            });
          }
          e.preventDefault();
        });
      },
      nameParse: function(string, hash, id) {
        var liveTemplate, temp;
        liveTemplate = string;
        temp = new Booyah;
        liveTemplate = temp.addTemplate(liveTemplate).addTag('{{ ' + id + ' }}', hash).ready();
        return liveTemplate;
      }
    };
    repeaterClone.init();
    tr_max = {
      len: function(that) {
        var $that;
        $that = $(that);
        return parseInt($that.attr('maxlength')) - ($that.val().length);
      }
    };
  });

}).call(this);

(function() {
  jQuery(document).ready(function($) {
    var clear_items;
    clear_items = function(button, field) {
      if (confirm('Remove all items?')) {
        $(field).val('');
        $(button).parent().next().html('');
      }
      return false;
    };
    $(document).on('click', '.items-list-button', function() {
      var $ul, name;
      $ul = $(this).parent().next();
      name = $ul.attr('name');
      if (name) {
        $ul.data('name', name);
      }
      name = $ul.data('name');
      $ul.prepend($('<li class="item"><div class="move tr-icon-menu"></div><a href="#remove" class="tr-icon-remove2 remove" title="Remove Item"></a><input type="text" name="' + name + '[]" /></li>').hide().delay(10).slideDown(150).scrollTop('100%'));
    });
    $(document).on('click', '.items-list-clear', function() {
      var field;
      field = $(this).parent().prev();
      clear_items($(this), field[0]);
    });
    $(document).on('click', '.tr-items-list .remove', function() {
      $(this).parent().slideUp(150, function() {
        $(this).remove();
      });
    });
  });

}).call(this);

(function() {
  jQuery(document).ready(function($) {
    var clear_gallery, clear_media, set_file_uploader, set_gallery_uploader, set_image_uploader;
    set_image_uploader = function(button, field) {
      var btnTitle, temp_frame, title, typeInput;
      title = 'Select an Image';
      btnTitle = 'Use Image';
      typeInput = 'image';
      temp_frame = wp.media({
        title: title,
        button: {
          text: btnTitle
        },
        library: {
          type: typeInput
        },
        multiple: false
      });
      temp_frame.on('select', function() {
        var attachment, url;
        attachment = temp_frame.state().get('selection').first().toJSON();
        url = '';
        if (attachment.sizes.thumbnail) {
          url = attachment.sizes.thumbnail.url;
        } else {
          url = attachment.sizes.full.url;
        }
        $(field).val(attachment.id);
        $(button).parent().next().html('<img src="' + url + '"/>');
      });
      wp.media.frames.image_frame = temp_frame;
      wp.media.frames.image_frame.open();
      return false;
    };
    set_file_uploader = function(button, field) {
      var btnTitle, temp_frame, title, typeInput;
      title = 'Select a File';
      btnTitle = 'Use File';
      typeInput = '';
      temp_frame = wp.media({
        title: title,
        button: {
          text: btnTitle
        },
        library: {
          type: typeInput
        },
        multiple: false
      });
      temp_frame.on('select', function() {
        var attachment, link;
        attachment = temp_frame.state().get('selection').first().toJSON();
        link = '<a target="_blank" href="' + attachment.url + '">' + attachment.url + '</a>';
        $(field).val(attachment.id);
        $(button).parent().next().html(link);
      });
      wp.media.frames.file_frame = temp_frame;
      wp.media.frames.file_frame.open();
      return false;
    };
    clear_media = function(button, field) {
      $(field).val('');
      $(button).parent().next().html('');
      return false;
    };
    set_gallery_uploader = function(button, list) {
      var btnTitle, temp_frame, title;
      title = 'Select Images';
      btnTitle = 'Use Images';
      temp_frame = wp.media({
        title: title,
        button: {
          text: btnTitle
        },
        library: {
          type: 'image'
        },
        multiple: 'toggle'
      });
      temp_frame.on('select', function() {
        var attachment, field, i, item, l, use_url;
        attachment = temp_frame.state().get('selection').toJSON();
        l = attachment.length;
        i = 0;
        while (i < l) {
          field = $(button).parent().prev().clone();
          use_url = '';
          if (attachment[i].sizes.thumbnail) {
            use_url = attachment[i].sizes.thumbnail.url;
          } else {
            use_url = attachment[i].sizes.full.url;
          }
          item = $('<li class="image-picker-placeholder"><a href="#remove" class="tr-icon-remove2" title="Remove Image"></a><img src="' + use_url + '"/></li>');
          $(item).append(field.val(attachment[i].id).attr('name', field.attr('name') + '[]'));
          $(list).append(item);
          $(list).find('a').on('click', function(e) {
            e.preventDefault();
            $(this).parent().remove();
          });
          i++;
        }
      });
      wp.media.frames.gallery_frame = temp_frame;
      wp.media.frames.gallery_frame.open();
      return false;
    };
    clear_gallery = function(button, field) {
      if (confirm('Remove all images?')) {
        $(field).html('');
      }
      return false;
    };
    $(document).on('click', '.image-picker-button', function() {
      var field;
      field = $(this).parent().prev();
      set_image_uploader($(this), field[0]);
    });
    $(document).on('click', '.file-picker-button', function() {
      var field;
      field = $(this).parent().prev();
      set_file_uploader($(this), field[0]);
    });
    $(document).on('click', '.image-picker-clear, .file-picker-clear', function() {
      var field;
      field = $(this).parent().prev();
      clear_media($(this), field[0]);
    });
    $(document).on('click', '.gallery-picker-button', function() {
      var list;
      list = $(this).parent().next();
      set_gallery_uploader($(this), list[0]);
    });
    $(document).on('click', '.gallery-picker-clear', function() {
      var list;
      list = $(this).parent().next();
      clear_gallery($(this), list[0]);
    });
    $('.tr-gallery-list a').on('click', function(e) {
      e.preventDefault();
      $(this).parent().remove();
    });
  });

}).call(this);

(function() {
  jQuery(document).ready(function($) {
    $('.typerocket-container').on('click', '.matrix-button', function(e) {
      var $fields, $select, $that, button_txt, callbacks, form_group, group, mxid, type, url;
      $that = $(this);
      if (!$that.is(':disabled')) {
        mxid = $that.data('id');
        group = $that.data('folder');
        $fields = $('#' + mxid);
        $select = $('select[data-mxid="' + mxid + '"]');
        button_txt = $that.val();
        type = $select.val();
        callbacks = TypeRocket.repeaterCallbacks;
        $that.attr('disabled', 'disabled').val('Adding...');
        url = '/typerocket_matrix_api/v1/' + group + '/' + type;
        form_group = $select.data('group');
        $.ajax({
          url: url,
          method: 'POST',
          dataType: 'html',
          data: {
            form_group: form_group
          },
          success: function(data) {
            var $items_list, $repeater_fields, $sortables, ri;
            data = $(data);
            ri = 0;
            while (callbacks.length > ri) {
              if (typeof callbacks[ri] === 'function') {
                callbacks[ri](data);
              }
              ri++;
            }
            data.prependTo($fields).hide().delay(10).slideDown(300).scrollTop('100%');
            if ($.isFunction($.fn.sortable)) {
              $sortables = $fields.find('.tr-gallery-list');
              $items_list = $fields.find('.tr-items-list');
              $repeater_fields = $fields.find('.tr-repeater-fields');
              if ($sortables.length > 0) {
                $sortables.sortable();
              }
              if ($repeater_fields.length > 0) {
                $repeater_fields.sortable({
                  connectWith: '.tr-repeater-group',
                  handle: '.repeater-controls'
                });
              }
              if ($items_list.length > 0) {
                $items_list.sortable({
                  connectWith: '.item',
                  handle: '.move'
                });
              }
            }
            $that.val(button_txt).removeAttr('disabled', 'disabled');
          },
          error: function(jqXHR) {
            $that.val('Try again - Error ' + jqXHR.status).removeAttr('disabled', 'disabled');
          }
        });
      }
    });
  });

}).call(this);

(function() {
  jQuery(document).ready(function($) {
    var initComponent;
    if ($('.tr-components').length > 0) {
      initComponent = function(data, fields) {
        var $items_list, $repeater_fields, $sortables, callbacks, ri;
        callbacks = TypeRocket.repeaterCallbacks;
        ri = 0;
        while (callbacks.length > ri) {
          if (typeof callbacks[ri] === 'function') {
            callbacks[ri](data);
          }
          ri++;
        }
        if ($.isFunction($.fn.sortable)) {
          $sortables = fields.find('.tr-gallery-list');
          $items_list = fields.find('.tr-items-list');
          $repeater_fields = fields.find('.tr-repeater-fields');
          if ($sortables.length > 0) {
            $sortables.sortable();
          }
          if ($repeater_fields.length > 0) {
            $repeater_fields.sortable({
              connectWith: '.tr-repeater-group',
              handle: '.repeater-controls'
            });
          }
          if ($items_list.length > 0) {
            $items_list.sortable({
              connectWith: '.item',
              handle: '.move'
            });
          }
        }
      };
      $('.typerocket-container').on('click', '.tr-builder-add-button', function(e) {
        var overlay, select;
        e.preventDefault();
        select = $(this).next();
        overlay = $('<div>').addClass('tr-builder-select-overlay').on('click', function() {
          $(this).remove();
          return $('.tr-builder-select').fadeOut();
        });
        $('body').append(overlay);
        return select.fadeIn();
      });
      $('.typerocket-container').on('click', '.tr-builder-component-control', function(e) {
        var component, components, frame, id, index;
        e.preventDefault();
        $(this).parent().children().removeClass('active');
        id = $(this).addClass('active').parent().data('id');
        index = $(this).index();
        frame = $('#frame-' + id);
        components = frame.children();
        components.removeClass('active');
        component = components[index];
        return $(component).addClass('active');
      });
      $('.typerocket-container').on('click', '.tr-remove-builder-component', function(e) {
        var component, components, control, frame, id, index;
        e.preventDefault();
        if (confirm('Remove component?')) {
          control = $(this).parent();
          control.parent().children().removeClass('active');
          id = control.parent().data('id');
          index = $(this).parent().index();
          frame = $('#frame-' + id);
          components = frame.children();
          component = components[index];
          $(component).remove();
          return control.remove();
        }
      });
      $('.tr-components').sortable({
        start: function(e, ui) {
          return ui.item.startPos = ui.item.index();
        },
        update: function(e, ui) {
          var builder, components, frame, id, index, old, select;
          select = ui.item.parent();
          id = select.data('id');
          frame = $('#frame-' + id);
          components = frame.children().detach();
          index = ui.item.index();
          old = ui.item.startPos;
          builder = components.splice(old, 1);
          components.splice(index, 0, builder[0]);
          return frame.append(components);
        }
      });
      $('.typerocket-container').on('click', '.builder-select-option', function(e) {
        var $components, $fields, $select, $that, form_group, group, img, mxid, type, url;
        $that = $(this);
        $that.parent().fadeOut();
        $('.tr-builder-select-overlay').remove();
        if (!$that.hasClass('disabled')) {
          mxid = $that.data('id');
          group = $that.data('folder');
          img = $that.data('thumbnail');
          $fields = $('#frame-' + mxid);
          $components = $('#components-' + mxid);
          $select = $('ul[data-mxid="' + mxid + '"]');
          type = $that.data('value');
          $that.addClass('disabled');
          url = '/typerocket_builder_api/v1/' + group + '/' + type;
          form_group = $select.data('group');
          $.ajax({
            url: url,
            method: 'POST',
            dataType: 'html',
            data: {
              form_group: form_group
            },
            success: function(data) {
              var html;
              data = $(data);
              $fields.children().removeClass('active');
              $components.children().removeClass('active');
              data.prependTo($fields).addClass('active');
              if (img) {
                img = '<img src="' + img + '" />';
              }
              html = '<li class="active tr-builder-component-control">' + img + '<span class="tr-builder-component-title">' + $that.text() + '</span><span class="remove tr-remove-builder-component"></span>';
              $components.prepend(html);
              initComponent(data, $fields);
              return $that.removeClass('disabled');
            },
            error: function(jqXHR) {
              $that.val('Try again - Error ' + jqXHR.status).removeAttr('disabled', 'disabled');
            }
          });
        }
      });
    }
  });

}).call(this);

(function() {
  var tr_delay;

  jQuery.fn.TypeRocketLink = function(type) {
    var search, that;
    if (type == null) {
      type = 'any';
    }
    that = this;
    search = encodeURI(this.val());
    jQuery.getJSON('/wp-json/typerocket/v1/search?post_type=' + type + '&s=' + search, function(data) {
      var i, len, post, results;
      if (data) {
        that.next().next().next().html('');
        that.next().next().next().append('<li class="tr-link-search-result-title">Results');
        results = [];
        for (i = 0, len = data.length; i < len; i++) {
          post = data[i];
          results.push(that.next().next().next().append('<li class="tr-link-search-result" data-id="' + post.ID + '" >' + post.post_title));
        }
        return results;
      }
    });
    return this;
  };

  tr_delay = (function() {
    var timer;
    timer = 0;
    return function(callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();

  jQuery(document).ready(function($) {
    $('.typerocket-container').on('keyup', '.tr-link-search-input', function() {
      var that, type;
      that = $(this);
      type = $(this).data('type');
      return tr_delay((function() {
        that.TypeRocketLink(type);
      }), 250);
    });
    return $('.typerocket-container').on('click', '.tr-link-search-result', function() {
      var id, title;
      id = $(this).data('id');
      title = $(this).text();
      $(this).parent().prev().html('Selection: <b>' + title + '</b>');
      $(this).parent().prev().prev().val(id);
      $(this).parent().prev().prev().prev().focus().val('');
      return $(this).parent().html('');
    });
  });

}).call(this);

(function() {
  jQuery.fn.selectText = function() {
    var doc, element, range, selection;
    doc = document;
    element = this[0];
    range = void 0;
    selection = void 0;
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
    $('.typerocket-container').on('click', '.field', function() {
      $(this).selectText();
    });
  });

}).call(this);
