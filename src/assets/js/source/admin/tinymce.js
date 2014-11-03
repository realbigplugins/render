/**
 * Adds the tinymce button.
 *
 * @since USL 1.0.0
 *
 * @global tinymce
 * @global USL_Modal
 * @global USL_Data
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
            });
        },

        open: function () {

            // Get the tinymodal editor object
            if (typeof tinymodal !== 'undefined') {
                var _editor = tinymodal.get(wpActiveEditor);

                if (_editor && !_editor.isHidden()) {
                    editor = _editor;
                } else {
                    editor = null;
                }
            }

            selection = editor.selection.getContent();
        },

        close: function () {
            editor.focus();
        },

        update: function () {

        }
    };

    $(function () {
        USL_tinymce.init();
    })
})(jQuery);