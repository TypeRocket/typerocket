// TinyMCE doesn't handle being moved in the DOM.  Destroy the
// editor instances at the start of a sort and recreate 
// them afterwards.
// https://core.trac.wordpress.org/ticket/19173
var _triggerAllEditors = function(event, creatingEditor) {
    var postbox, textarea;

    postbox = jQuery(event.target);
    textarea = postbox.find('textarea.wp-editor-area');

    textarea.each(function(index, element) {
        var editor;
        editor = tinyMCE.EditorManager.get(element.id);
        if (creatingEditor) {
            if (!editor) {
                tinyMCE.execCommand('mceAddControl', true, element.id);
            }
        }
        else {
            if (editor) {
                editor.save();
                tinyMCE.execCommand('mceRemoveControl', true, element.id);
            }
        }
    });
};
jQuery('#poststuff, .tr-repeater-group').on('sortstart', function(event) {
    _triggerAllEditors(event, false);
}).on('sortstop', function(event) {
    _triggerAllEditors(event, true);
    alert('move');
});