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
    var editor, $texteditor, $editor, $loader, submitted = false;

    USL_tinymce = {

        init: function () {

            this.add_to_tinymce();
            this.binds();

            $editor = $('#wp-content-editor-container');
            $texteditor = $editor.find('.wp-editor-area');

            $editor.append('<div id="usl-tinymce-loader" class="hide"><div class="spinner"></div></div>');
            $loader = $('#usl-tinymce-loader');
        },

        binds: function () {

            $(document).on('usl-modal-close', function () {
                USL_tinymce.close();
            });

            $(document).on('usl-modal-update', function () {
                USL_tinymce.update();
            });

            $(document).on('usl-modal-remove', function () {
                USL_tinymce.removeShortcode();
            });

            $('#post').submit(function (event) {
                USL_tinymce.submit(event, $(this));
            });
        },

        // REMOVE If not still in use
        editorBinds: function () {

            editor.on('click', function (event) {

                // Show shortcode toolbar
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

                // Fires when clicking the shortcode (< >) Button in the tinymce toolbar
                editor.addCommand('usl-open', function () {

                    var selection = editor.selection.getContent(),
                        node = editor.selection.getNode(),
                        $node = $(node);

                    if ($node.hasClass('usl-tinymce-shortcode-wrapper')) {

                        var content = $node.find('.usl-tinymce-shortcode-content').html(),
                            container_html = $('<div />').append($node.clone()).html(),
                            shortcode = USL_tinymce.visualToLiteral(container_html);

                        USL_Modal.showRemoveButton();
                        USL_Modal.modify(shortcode, content);
                    } else {
                        USL_Modal.hideRemoveButton();
                        USL_Modal.open(selection);
                    }
                });

                // This is used to activate our shortcode (< >) button whenever the user selects a shortcode within the
                // visual editor
                function setState(button, node) {
                    button.active(node.className.indexOf('usl-tinymce-shortcode-wrapper') !== -1);
                }

                // This helps with editing content around rendered shortcodes
                editor.on('keyup', function (e) {

                    var ndThis = editor.selection.getNode(),
                        $this = $(ndThis);

                    if ($this.attr('id') === 'mce_noneditablecaret' && $this.attr('data-mce-bogus')) {

                        // FIXME First character after shortcode causes "caret" to fall back into the shortcode
                        $this.replaceWith(ndThis.childNodes);
                    }
                });

                editor.on('init show undo redo', USL_tinymce.loadVisual);
                $(document).on('usl-modal-update', USL_tinymce.loadVisual);

                editor.on('hide', function () {
                    var content = editor.getContent();
                    $texteditor.val(window.switchEditors.pre_wpautop(USL_tinymce.loadText(content)));
                });
            });
        },


        // Renders literal shortcodes into visual shortcodes (Text -> Visual)
        loadVisual: function () {

            var content = editor.getContent();

            USL_MCECallbacks.visualLoadCounter.count = 0;
            USL_MCECallbacks.visualLoadCounter.total = 0;

            if (content.length && USL_Data.rendered_shortcodes) {
                for (var i = 0; i < USL_Data.rendered_shortcodes.length; i++) {
                    USL_MCECallbacks.convertLiteralToRendered(USL_Data.rendered_shortcodes[i], content, editor);
                }
            }
        },

        // Converts rendered shortcodes into literal shortcodes (Visual -> Text)
        loadText: function (content) {

            if (content.length && USL_Data.rendered_shortcodes) {
                for (var i = 0; i < USL_Data.rendered_shortcodes.length; i++) {
                    content = USL_MCECallbacks.convertRenderedToLiteral(USL_Data.rendered_shortcodes[i], content, $texteditor);
                }
            }

            return content;
        },

        visualToLiteral: function (shortcode) {

            var atts = USL_MCECallbacks.getVisualAtts(shortcode),
                shortcode_content = USL_MCECallbacks.getVisualContent(shortcode),
                code = USL_MCECallbacks.getVisualCode(shortcode),
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

        removeShortcode: function () {
            editor.selection.setContent('');
            USL_Modal.close();
        },

        loading: function (loading) {

            if (loading) {
                $loader.removeClass('hide');
            } else {
                $loader.addClass('hide');
            }
        },

        submit: function (event, $e) {

            if (!submitted) {

                submitted = true;

                event.preventDefault();
                var content = editor.getContent();

                editor.on('PostProcess', function (e) {
                    e.content = USL_tinymce.loadText(content);
                });

                $e.submit();
            }
        }
    };

    $(function () {
        USL_tinymce.init();
    })
})(jQuery);