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
    var editor;

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

        // REMOVE If not still in use
        editorBinds: function () {

            editor.on('click', function (event) {

                // Remove shortcode button
                //if (event.target.className.indexOf('usl-tinymce-shortcode-interaction-remove') !== -1) {
                //    editor.execCommand('uslRemoveShortcode', false, event.target);
                //}
            });
        },

        add_to_tinymce: function () {

            tinymce.PluginManager.add('usl', function (_editor) {

                // Set the active editor
                editor = _editor;

                // REMOVE If not still in use
                USL_tinymce.editorBinds();

                // This is used to activate our shortcode (< >) button whenever the user selects a shortcode within the
                // visual editor
                function setState( button, node ) {
                    button.active(node.className.indexOf('usl-tinymce-shortcode-wrapper') !== -1);
                }

                editor.addButton('usl', {

                    // Establishes an icon class for the button with the prefix "mce-i-"
                    icon: 'usl-mce-icon',

                    cmd: 'usl-open',

                    onPostRender: function () {
                        var usl_button = this;

                        editor.on('nodechange', function (event) {
                            setState(usl_button, event.element);
                        });
                    }
                });

                // This helps with editing content around rendered shortcodes
                editor.on('keyup', function (e) {

                    var ndThis = editor.selection.getNode(),
                        $this = $(ndThis);

                    if ($this.attr('id') === 'mce_noneditablecaret' && $this.attr('data-mce-bogus')) {

                        // FIXME First character after shortcode causes "caret" to fall back into the shortcode
                        $this.replaceWith(ndThis.childNodes);
                    }
                });

                // Fires when clicking the shortcode (< >) Button in the tinymce toolbar
                editor.addCommand('usl-open', function () {

                    var selection = editor.selection.getContent(),
                        node = editor.selection.getNode(),
                        $node = $(node);

                    if ($node.hasClass('usl-tinymce-shortcode-wrapper')) {

                        var content = $node.find('.usl-tinymce-shortcode-content').html(),
                            container_html = $('<div />').append($node.clone()).html(),
                            shortcode = USL_tinymce.visualToLiteral(container_html);

                        USL_Modal.modify(shortcode, content);
                    } else {
                        USL_Modal.open(selection);
                    }
                });

                // Renders literal shortcodes into visual shortcodes (Text -> Visual)
                editor.on('BeforeSetContent', function (e) {

                    if (e.content) {
                        $.each(USL_MCECallbacks.callbacks, function (callback) {
                            e.content = USL_MCECallbacks._convertLiteralToRendered(USL_MCECallbacks.callbacks[callback], e.content);
                        });
                    }
                });

                // Converts rendered shortcodes into literal shortcodes (Visual -> Text)
                editor.on('PostProcess', function (e) {
                    if (e.get) {

                        $.each(USL_MCECallbacks.callbacks, function (callback) {
                            e.content = USL_MCECallbacks._convertRenderedToLiteral(USL_MCECallbacks.callbacks[callback], e.content);
                        });
                    }
                });
            });
        },

        visualToLiteral: function (shortcode) {

            var atts = USL_MCECallbacks._getVisualAtts(shortcode),
                shortcode_content = USL_MCECallbacks._getVisualContent(shortcode),
                code = USL_MCECallbacks._getVisualCode(shortcode),
                output = '[' + code;

            if (atts) {
                $.each(atts, function (name, value) {
                    if (value.length) {
                        output += ' ' + name + '="' + value + '"';
                    }
                });
            }

            output += ']';

            if (shortcode_content.length) {
                output += shortcode_content + '[/' + code + ']';
            }

            return output;
        },

        close: function () {
            editor.focus();
        },

        update: function () {
            editor.insertContent(USL_Modal.output);
        },

        removeShortcode: function ($e) {
            $e.closest('.usl-tinymce-shortcode-wrapper').remove();
        }
    };

    $(function () {
        USL_tinymce.init();
    })
})(jQuery);