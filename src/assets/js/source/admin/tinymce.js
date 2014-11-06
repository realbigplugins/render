/**
 * Adds the tinymce button.
 *
 * @since USL 1.0.0
 *
 * @global tinymce
 * @global USL_Modal
 * @global USL_Data
 * @global USL_MCECallbacks
 *
 * @package USL
 * @subpackage Modal
 */

var USL_tinymce;
(function ($) {
    var editor, selection;

    USL_tinymce = {
        init: function () {
            this.add_to_tinymce();
            this.binds();
        },

        binds: function () {

            $(document).on('usl-modal-close', function () {
                USL_tinymce.close();
            });

            $(document).on('usl-modal-update', function () {
                USL_tinymce.update();
            });
        },

        add_to_tinymce: function () {
            tinymce.PluginManager.add('usl_button', function (editor) {
                editor.addButton('usl_button', {

                    // Establishes an icon class with the prefix "mce-i-"
                    icon: 'usl-mce-icon',
                    cmd: 'usl-open'
                });

                editor.addCommand('usl-open', function () {
                    USL_tinymce.open();
                    USL_Modal.open(selection);
                });

                editor.on('BeforeSetContent', function (e) {

                    if (e.content) {
                        $.each(USL_MCECallbacks.visual.callbacks, function (callback) {
                            e.content = USL_MCECallbacks.visual._parseContent(USL_MCECallbacks.visual.callbacks[callback], e.content);
                            console.log(e.content);
                        });
                    }
                });

                editor.on('PostProcess', function (e) {
                    //if (e.get) {
                    //
                    //    $.each(USL_MCECallbacks.visual, function (callback) {
                    //        e.content = USL_MCECallbacks.visual[callback](e.content);
                    //    });
                    //}
                });
            });
        },

        open: function () {

            // Get the tinymce editor object
            if (typeof tinymce !== 'undefined') {
                var _editor = tinymce.get(wpActiveEditor);

                if (_editor && !_editor.isHidden()) {
                    editor = _editor;
                    selection = editor.selection.getContent();
                } else {
                    editor = null;
                }
            }
        },

        close: function () {
            editor.focus();
        },

        update: function () {
            editor.insertContent(USL_Modal.output);
        }
    };

    $(function () {
        USL_tinymce.init();
    })
})(jQuery);