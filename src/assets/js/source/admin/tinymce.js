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
// TODO When in a shortcode, don't show "p>>span>>a>>span>>span" at the bottom, show "p>>shortcode"
(function ($) {
    var editor, $texteditor, $editor, $loader, no_selected,
        min_load_time = false,
        last_message = 0,
        submitted = false,
        $lastNode = '';

    USL_tinymce = {

        init: function () {

            // TODO Shortcode drag and drop

            this.addToTinymce();
            this.binds();

            $editor = $('#wp-content-editor-container');
            $texteditor = $editor.find('.wp-editor-area');

            this.createLoader();
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

        addToTinymce: function () {

            tinymce.PluginManager.add('usl', function (_editor) {

                // Set the active editor
                editor = _editor;

                // WP default shortcut
                editor.addShortcut( 'alt+shift+s', '', 'usl-open' );

                editor.addButton('usl', {

                    // Establishes an icon class for the button with the prefix "mce-i-"
                    icon: 'usl-mce-icon',
                    cmd: 'usl-open',

                    // Make the < > button active when cursor is inside a shortcode
                    onPostRender: function () {
                        var usl_button = this;

                        editor.on('nodechange', function (event) {

                            var $node = $(event.element).hasClass('usl-tinymce-shortcode-wrapper') ?
                                $(event.element) :
                                $(event.element).closest('.usl-tinymce-shortcode-wrapper'),
                                is_usl = $node.length ? true : false;

                            if ($lastNode.length) {
                                $lastNode.removeClass('active');
                            }

                            if (is_usl) {
                                $lastNode = $node;
                                $lastNode.addClass('active');
                            }

                            usl_button.active(is_usl);
                        });
                    }
                });

                // When deleting the character &8203;, this should also delete the shortcode node preceding it (because
                // the character &8203; is not visible to the user, so having to delete that and THEN also deleting the
                // shortcode would be confusing.
                editor.onKeyDown.add(function (ed, event) {

                    if (event.keyCode == 8) {

                        var curElm = ed.selection.getRng().startContainer,
                            range = ed.selection.getBookmark(curElm.textContent).rng;

                        if (typeof range !== 'undefined') {
                            var caretPos = ed.selection.getBookmark(curElm.textContent).rng.startOffset,
                                charcode = curElm.textContent.charCodeAt(0);

                            if (charcode === 8203 && caretPos === 1) {
                                var $curElm = $(curElm),
                                    $prev = $curElm.prev();

                                $prev.remove();
                            }
                        }
                    }
                });

                // Fires when clicking the shortcode < > button in the tinymce toolbar
                editor.addCommand('usl-open', function () {

                    var node = editor.selection.getNode();

                    USL_Modal.selection = editor.selection.getContent();
                    no_selected = false;

                    if ($(node).closest('.usl-tinymce-shortcode-wrapper').length || $(node).hasClass('usl-tinymce-shortcode-wrapper')) {

                        var $node = $(node).hasClass('usl-tinymce-shortcode-wrapper') ?
                                $(node) :
                                $(node).closest('.usl-tinymce-shortcode-wrapper'),
                            content = $node.find('.usl-tinymce-shortcode-content').html(),
                            container_html = $('<div />').append($node.clone()).html(),
                            shortcode = USL_tinymce.visualToLiteral(container_html);

                        // If there's no selected text, assume the entire shortcode is being modified
                        if (!USL_Modal.selection.length) {
                            USL_Modal.selection = content;
                            no_selected = true;
                        }

                        USL_Modal.showRemoveButton();
                        USL_Modal.modify(shortcode);
                    } else {
                        USL_Modal.hideRemoveButton();
                        USL_Modal.open();
                    }
                });

                editor.on('init show undo redo', USL_tinymce.loadVisual);
                $(document).on('usl-modal-update', USL_tinymce.loadVisual);

                editor.on('hide', function () {
                    var content = editor.getContent({format: 'numeric'});
                    $texteditor.val(window.switchEditors.pre_wpautop(USL_tinymce.loadText(content)));
                });
            });
        },

        createLoader: function () {

            $editor.append('<div id="usl-tinymce-loader" class="hide"><div class="spinner"></div><div class="text">></div></div>');
            $loader = $('#usl-tinymce-loader');
        },

        /**
         * Renders literal shortcodes into visual shortcodes (Text -> Visual).
         */
        loadVisual: function () {

            if (USL_Data.do_render) {
                var content = editor.getContent();
                USL_MCECallbacks.convertLiteralToRendered(content, editor);
            }
        },

        /**
         * Converts rendered shortcodes into literal shortcodes (Visual -> Text).
         */
        loadText: function (content) {

            content = USL_MCECallbacks.convertRenderedToLiteral(content);
            content = content.replace(/&#8203;/g, '');

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

            // Nesting support
            var old_code = USL_Modal.current_shortcode.code,
                new_code = USL_Modal.output.code,
                old_render_obj = USL_Data.rendered_shortcodes[old_code],
                new_render_obj = USL_Data.rendered_shortcodes[new_code],
                nesting = false;

            if (old_code && old_code !== new_code) {

                // Only do it if the parent code supports it, the code to be nested doesn't allow it, and there is
                // some selected content
                if (old_render_obj.allowNesting && !new_render_obj.allowNesting && USL_Modal.selection.length) {
                    nesting = true;
                }
            }

            if (!nesting) {
                this.removeShortcode();
            }

            editor.insertContent(USL_Modal.output.all);
        },

        removeShortcode: function () {

            var node = editor.selection.getNode(),
                $node = $(node).hasClass('usl-tinymce-shortcode-wrapper') ?
                    $(node) :
                    $(node).closest('.usl-tinymce-shortcode-wrapper');

            $node.remove();
            USL_Modal.close();
        },

        loading: function (loading) {

            if (loading) {

                // Makes sure the the loader stays for a minimum time
                min_load_time = true;
                setTimeout(function () {
                    min_load_time = false;
                }, 1500);

                // Get a random loading message
                var loading_messages = USL_Data.loading_messages,
                    random_message = Math.floor(Math.random() * (loading_messages.length));

                // Make sure it's not the same message as last time (that's boring!)
                if (random_message == last_message) {
                    while (random_message == last_message) {
                        random_message = Math.floor(Math.random() * (loading_messages.length))
                    }
                }

                last_message = random_message;

                $loader.find('.text').html(loading_messages[random_message]);
                $loader.removeClass('hide');
                $('#content-html').prop('disabled', true);
                $('#content-tmce').prop('disabled', true);
                $('#wp-content-media-buttons').addClass('disabled');
            } else {
                waitMinimumLoadingTime();
            }

            function waitMinimumLoadingTime () {

                // Don't remove the loader until the minimum load time has passed
                if (min_load_time) {
                    setTimeout(waitMinimumLoadingTime, 50);
                    return;
                }

                $loader.addClass('hide');
                $('#content-html').prop('disabled', false);
                $('#content-tmce').prop('disabled', false);
                $('#wp-content-media-buttons').removeClass('disabled');
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