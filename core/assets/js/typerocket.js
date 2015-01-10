jQuery(document).ready(function($) {

  // fork from theme options framework
  function editorHeight() {
    // Editor Height (needs improvement)
    $('.wp-editor-wrap').each(function() {
      var editor_iframe = $(this).find('iframe');
      if ( editor_iframe.height() < 30 ) {
        editor_iframe.css({'height':'auto'});
      }
    });
  }
  editorHeight();

  // sorting, colors, dates
  var add_sorting = function(obj) {
      if( $.isFunction($.fn.sortable) ) {
        var $sortables = $(obj).find('.tr-gallery-list'),
          $items_list = $(obj).find('.tr-items-list'),
          $repeater_fields = $(obj).find('.tr-repeater-fields');
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
    },
    add_date_picker = function(obj) {
      if( $.isFunction($.fn.datepicker) ) {
        $(obj).each(function(){
          $(this).datepicker();
        });
      }
    },
    add_color_picker = function(obj) {
      if( $.isFunction($.fn.wpColorPicker) ) {
        $(obj).each(function(){
          var pal = $(this).attr('id') + '_color_palette',
            settings = { palettes: window[pal] };
          $(this).wpColorPicker(settings);
        });
      }
    };

  add_sorting(document);
  add_date_picker($('.date-picker[name]'));
  add_color_picker($('.color-picker[name]'));

  // Tabs
  $('.tr-tabs li').each(function(){

    $(this).click(function(e){
      $(this).addClass('active').siblings().removeClass('active');
      var section = $(this).find('a').attr('href');
      $(section).addClass('active').siblings().removeClass('active');
      editorHeight();

      e.preventDefault();
    });
  });

  $('.contextual-help-tabs a').click(function(){
    editorHeight()
  });


  // repeaters
  var repeaterClone = {

    init: function() {
      var obj = this;

      // Add
      // $group_template | div.tr-repeater-group-template | contains all fields for repeating
      // hash | int | used for keying arrayed data to group repeated field groups
      $(document).on('click', '.tr-repeater .controls .add', function(e){
        var $group_template = $($(this).parent().parent().next().clone()).removeClass('tr-repeater-group-template').addClass('tr-repeater-group'),
          hash = new Date().getTime(),
          replacement_id = $group_template.data('id'),
          dev_notes = $group_template.find('.dev .field span'),
          data_name = $group_template.find('[data-name]'),
          data_name_filtered = $group_template.find('.tr-repeater-group-template [data-name]');

        $(data_name).each(function(i){
          var name = obj.nameParse($(this).data('name'), hash, replacement_id);
          $(this).attr('name', name);
          $(this).attr('data-name', null);
          $(this).attr('value', null);
        });

        $(dev_notes).each(function(i) {
          var name = obj.nameParse($(this).html(), hash, replacement_id);
          $(this).html(name);
        });

        $(data_name_filtered).each(function(i){
          $(this).attr('data-name', $(this).attr('name'));
          $(this).attr('name', null);
          $(this).attr('value', null);
        });

        // add sorting
        add_sorting($group_template);
        add_date_picker($group_template.find('.date-picker[name]'));

        var $fields_div = $(this).parent().parent().next().next();

        $group_template.prependTo($fields_div).hide().delay(10).slideDown(300).scrollTop('100%');

        add_color_picker($group_template.find('.color-picker[name]'));

      });

      // remove
      $(document).on('click', '.tr-repeater .repeater-controls .remove', function(e){
        $(this).parent().parent().slideUp(300, function(){$(this).remove()});
        e.preventDefault;
      });

      // tr_action_collapse
      $(document).on('click', '.tr-repeater .controls .tr_action_collapse', function(e){
        $(this).parent().parent().next().next().toggleClass('tr-repeater-collapse');
        e.preventDefault;
      });

      // clear
      $(document).on('click', '.tr-repeater .controls .clear', function(e){
        if(confirm('Remove all items?')) {
          $(this).parent().parent().next().next().html('');
        }
        e.preventDefault;
      });

      // flip
      $(document).on('click', '.tr-repeater .controls .flip', function(e){
        if(confirm('Flip order of all items?')) {
          var items = $(this).parent().parent().next().next();
          items.children().each(function(i,item){items.prepend(item)})
        }
        e.preventDefault;
      });

    },

    nameParse: function(string, hash, id) {
      var liveTemplate = string;
      var temp = booyah;
      liveTemplate = temp
        .addTemplate(liveTemplate)
        .addTag('{{ '+id+' }}', hash)
        .ready();

      return liveTemplate;
    }

  }

  repeaterClone.init();

});


