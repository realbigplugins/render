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
            tinymce.PluginManager.add('usl', function (editor) {
                editor.addButton('usl', {

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
                        $.each(USL_MCECallbacks.callbacks, function (callback) {
                            e.content = USL_MCECallbacks._parseVisualContent(USL_MCECallbacks.callbacks[callback], e.content);
                        });
                    }
                });

                editor.on('PostProcess', function (e) {
                    if (e.get) {

                        $.each(USL_MCECallbacks.callbacks, function (callback) {
                            e.content = USL_MCECallbacks._parseTextContent(USL_MCECallbacks.callbacks[callback], e.content);
                        });
                    }
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