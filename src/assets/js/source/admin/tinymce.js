/**
 * Adds the tinymce button.
 *
 * @since 1.0.0
 *
 * @global tinymce
 * @global ajaxurl
 * @global Render_Modal
 * @global Render_Data
 * @global Render_MCECallbacks
 *
 * @package Render
 * @subpackage Modal
 */
var Render_tinymce;
(function ($) {
    var min_load_time = false,
        last_message = 0,
        submitted_editors = [],
        render_data = Render_Data.all_shortcodes,
        l18n = Render_Data.l18n,
        $modal_shortcodes = $('#render-modal-wrap').find('.render-modal-shortcodes');

    Render_tinymce = {

        /**
         * The currently active editor.
         *
         * @since {{VERSION}}
         */
        active_editor: null,

        /**
         * Initializes the object.
         *
         * @since 1.0.0
         */
        init: function () {

            this.addToTinymce();
            this.binds();
            this.createLoaders();
        },

        /**
         * Sets up the handlers.
         *
         * @since 1.0.0
         */
        binds: function () {

            $(document).on('render-modal-close', function () {
                Render_tinymce.close();
            });

            $(document).on('render-modal-update', function () {
                Render_tinymce.update();
            });

            $(document).on('render-modal-remove', function () {
                Render_tinymce.removeShortcode();
            });

            $(document).on('render-tinymce-post-render', function () {
                Render_tinymce.postRender();
            });

            $('#post').submit(function (event) {
                Render_tinymce.submit(event, $(this));
            });
        },

        /**
         * Sets up handlers within the TinyMCE body.
         *
         * @since 1.0.0
         *
         * @param $body The TinyMCE body element.
         */
        editorBinds: function ($body) {

            $body.on('mouseover', '.render-tinymce-shortcode-wrapper:not(.hide-actions)', function (e) {

                e.stopPropagation();

                // Remove all other active tooltips
                $body.find('.render-tinymce-shortcode-wrapper-actions.active').removeClass('active');

                // Add to new tooltip
                $(this).find('> .render-tinymce-shortcode-wrapper-actions').addClass('active');
            });

            $body.on('mouseleave', '.render-tinymce-shortcode-wrapper', function (e) {

                e.stopPropagation();

                // Remove all other active tooltips
                $body.find('.render-tinymce-shortcode-wrapper-actions.active:not(.hide-actions)').removeClass('active');
            })
        },

        /**
         * Integrates Render into the TinyMCE.
         *
         * @since 1.0.0
         */
        addToTinymce: function () {

            tinymce.PluginManager.add('render', function (editor) {

                var $body;

                editor.addButton('render_open', {

                    // Establishes an icon class for the button with the prefix "mce-i-"
                    icon: 'render-mce-icon',
                    cmd: 'Render_Open',
                    tooltip: l18n.add_shortcode
                });

                // Fires when clicking the shortcode <> button in the tinymce toolbar
                editor.addCommand('Render_Open', function () {

                    var selection, $selection;

                    if (Render_Data.do_render) {

                        selection = editor.selection.getContent({format: 'html'});
                        $selection = '<div>' + selection + '</div>';

                        // Don't bother if we just have whitespace selected.
                        if ($($selection).text().trim().length) {

                            // Load text with dummy divs, then slice off the divs to use for selection
                            Render_Modal.selection = Render_tinymce.loadText($selection)
                                .slice(5, Render_Modal.selection.length - 6);
                        }
                    } else {
                        Render_Modal.selection = editor.selection.getContent();
                    }

                    Render_tinymce.active_editor = editor;
                    Render_tinymce.open(null);
                });

                // WP default shortcut
                editor.addShortcut('alt+shift+s', '', 'render-open');

                if (!Render_Data.do_render) {
                    return;
                }

                editor.addButton('render_refresh', {

                    // Establishes an icon class for the button with the prefix "mce-i-"
                    icon: 'render-mce-refresh-icon',
                    cmd: 'Render_Refresh',
                    tooltip: 'Refresh Editor'
                });

                // Refresh the editor
                editor.addCommand('Render_Refresh', function () {
                    Render_tinymce.active_editor = editor;
                    Render_tinymce.loadVisual();
                });

                // Click the editor
                editor.onClick.add(function (editor, event) {

                    Render_tinymce.active_editor = editor;

                    // Remove delete overlay for all shortcodes
                    var $shortcode, content, container_html, shortcode;
                    $body = $(editor.getBody());

                    $body.find('.render-tinymce-shortcode-wrapper.delete').removeClass('delete');

                    // Edit a shortcode
                    if ($(event.target).hasClass('render-tinymce-shortcode-wrapper-edit')) {

                        $shortcode = $(event.target).closest('.render-tinymce-shortcode-wrapper');
                        content = $shortcode.find('.render-tinymce-shortcode-content').html();
                        container_html = $('<div />').append($shortcode.clone()).html();
                        shortcode = Render_tinymce.convertRenderedToLiteral(container_html);

                        if (content && content.length) {
                            Render_Modal.selection = content;
                        }

                        $shortcode.addClass('render-tinymce-editing');

                        Render_tinymce.open(shortcode);
                    }

                    // Remove a shortcode
                    if ($(event.target).hasClass('render-tinymce-shortcode-wrapper-remove')) {

                        $(event.target).closest('.render-tinymce-shortcode-wrapper').addClass('render-tinymce-editing');
                        Render_tinymce.removeShortcode();
                    }
                });

                /**
                 * Returns information about the character that's about to be deleted.
                 *
                 * Needs to be fired within the keyDown handler on the editor, and needs to be inside a check to make sure
                 * the backspace key is being pressed. (not currently in use)
                 *
                 * @since 1.0.0
                 *
                 * @returns object Character to be deleted and its char code.
                 */
                function get_char_to_be_deleted() {

                    //insert special marker char
                    var dummy_node = '<span id="__dummycaret">\u2060</span>',
                        current_node = editor.selection.getNode();

                    editor.selection.setContent(dummy_node, {format: 'raw', no_events: 1});

                    var node_content = $(current_node).text();

                    var cursor_position = node_content.search('\u2060');

                    // this is the character
                    var char_before_cursor = cursor_position != 0 ? node_content.slice(cursor_position - 1, cursor_position) : 'NA';

                    $(editor.getBody()).find('#__dummycaret').remove();

                    return {
                        char: char_before_cursor,
                        char_code: node_content.charCodeAt(cursor_position - 1)
                    }
                }

                // KeyDown (includes backspace)
                editor.onKeyDown.add(function (editor, event) {

                    Render_tinymce.active_editor = editor;

                    var node = editor.selection.getNode(),
                        node_content = $(node).html();

                    if (node && !$(node).hasClass('render-tinymce-shortcode-content')) {
                        return;
                    }

                    var curElm = editor.selection.getRng().startContainer,
                        range = editor.selection.getBookmark(curElm.textContent).rng;

                    if (typeof range === 'undefined') {
                        return;
                    }

                    var caretPos = range.startOffset;

                    // Convert char codes to literal for counting purposes
                    var literal_text = $('<div />').html(node_content).text();

                    // Beginning of shortcode
                    if (caretPos === 0) {

                        // Don't allow backspace or left arrow to do anything
                        if (event.which === 8 || event.which === 37) {
                            event.preventDefault();
                            return false;
                        }
                    }

                    // End of shortcode
                    if (caretPos === literal_text.length) {

                        // Don't right arrow to do anything
                        if (event.which === 39) {
                            event.preventDefault();
                            return false;
                        }
                    }
                });

                // Keypress (printable keys) in the editor
                editor.onKeyPress.add(function (editor, event) {

                    Render_tinymce.active_editor = editor;

                    var node = editor.selection.getNode(),
                        node_content = $(node).html();

                    if (node && !$(node).hasClass('render-tinymce-shortcode-content')) {
                        return;
                    }

                    var curElm = editor.selection.getRng().startContainer,
                        range = editor.selection.getBookmark(curElm.textContent).rng;

                    if (typeof range === 'undefined') {
                        return;
                    }

                    var caretPos = range.startOffset,
                        char_to_delete = caretPos != 0 ? node_content.slice(caretPos - 1, caretPos) : '';

                    // Convert char codes to literal for counting purposes
                    var literal_text = $('<div />').html(node_content).text();

                    // Insert char after string to prevent editing outside the shortcode
                    if (caretPos === literal_text.length) {

                        var newChar = String.fromCharCode(event.charCode);

                        if (newChar === ' ') {
                            newChar = '&nbsp;';
                        }

                        $(node).html(literal_text + newChar);
                        editor.selection.select(node, true);
                        editor.selection.collapse(false);

                        event.preventDefault();
                        return false;
                    }
                });

                // When clicking an image with a shortcode, don't allow resizing
                editor.on('nodechange', function (event) {

                    Render_tinymce.active_editor = editor;

                    // Must be image
                    if (event.element.nodeName != 'IMG') {
                        return;
                    }

                    var $img = $(event.element),
                        $shortcode = $img.closest('.render-tinymce-shortcode-wrapper'),
                        $body = $(editor.getBody()),
                        shortcode_data, $resize_elements;

                    // Must be in a shortcode
                    if (!$shortcode.length) {
                        return;
                    }

                    shortcode_data = Render_Data.all_shortcodes[$shortcode.data('code')].render;

                    // Must not have image editing allowed
                    if (typeof shortcode_data != 'undefined' && typeof shortcode_data.allowImageEditing != 'undefined') {
                        return;
                    }

                    // Hide the handles
                    setTimeout(function () {
                        $resize_elements = $body.find('.mce-resizehandle');
                        $resize_elements.hide();
                    }, 1);
                });

                editor.on('init', function () {
                    Render_tinymce.active_editor = editor;
                    Render_tinymce.editorBinds($(editor.getBody()));
                });

                // Init and switch to Visual
                editor.on('init show', function () {
                    Render_tinymce.active_editor = editor;
                    Render_tinymce.loadVisual();
                });

                // Switch to Text or Submit post
                editor.on('PostProcess', function (e) {
                    e.content = Render_tinymce.loadText(e.content);
                });

                // Prevent adding undo levels on rendering shortcodes
                // FIXME #136
                //editor.on('BeforeAddUndo', function (event) {
                //
                //    // Get any unmodified shortcodes
                //    var wp_regex = Render_Data.shortcode_regex.match(/\((\w+\|?)+\)/),
                //        shortodeRegEx, codes;
                //
                //    if (wp_regex) {
                //        shortodeRegEx = new RegExp('\\[' + wp_regex[0], 'g');
                //
                //        if (event.level.content.length) {
                //            codes = event.level.content.match(shortodeRegEx);
                //        }
                //    }
                //
                //    // If we found any unmodified shortcodes, then this is the undo level that renders shortcodes, so
                //    // we DON'T want to add it to the undo levels
                //    if (codes) {
                //        event.preventDefault();
                //    }
                //});
            });
        },

        /**
         * Creates the loading overlay of the TinyMCE.
         *
         * @since 1.0.0
         */
        createLoaders: function () {

            $('.wp-editor-container').append('<div class="render-tinymce-loader hide"><div class="spinner"></div><div class="text">></div></div>');
        },

        /**
         * Renders literal shortcodes into visual shortcodes (Text -> Visual).
         *
         * @since 1.0.0
         */
        loadVisual: function () {

            var content = Render_tinymce.active_editor.getContent();
            content = Render_tinymce.loadText(content);
            Render_tinymce.convertLiteralToRendered(content);
        },

        /**
         * Fires after rendering the visual editor.
         *
         * @since 1.0.0
         */
        postRender: function () {

            var $body = $(Render_tinymce.active_editor.getBody());

            $body.find('.render-tinymce-shortcode-wrapper').each(function () {

                // Determine if the shortcode is the LAST thing in its parent (including text)
                var contents = $(this).parent().contents();
                if (contents[contents.length - 1] == $(this).get(0)) {

                    //Okay it is, so insert a dummy container afterwords
                    var tag = $(this).prop('tagName').toLowerCase(),
                        $dummy_node = $('<' + tag + ' class="render-tinymce-dummy-container">&#8203;</' + tag + '>');

                    $(this).after($dummy_node);
                }
            });
        },

        /**
         * Converts rendered shortcodes into literal shortcodes (Visual -> Text).
         *
         * @since 1.0.0
         */
        loadText: function (content) {

            content = content.replace(/&#8203;/g, '');
            content = Render_tinymce.convertRenderedToLiteral(content);

            return content;
        },

        /**
         * Converts a shortcode element into a shortcode string.
         *
         * @since 1.0.0
         *
         * @param shortcode The shortcode element.
         * @returns The shortcode.
         */
        visualToLiteral: function (shortcode) {

            var code = $(shortcode).data('code'),
                atts = $(shortcode).data('atts'),
                shortcode_content = $(shortcode).find('.render-tinymce-shortcode-content').html();

            var output = '[' + code;

            if (atts) {
                atts = JSON.parse(atts);
                $.each(atts, function (name, value) {
                    if (value.length) {
                        output += ' ' + name + '=\'' + value + '\'';
                    }
                });
            }

            output += ']';

            if (shortcode_content) {
                output += shortcode_content + '[/' + code + ']';
            }

            return output;
        },

        /**
         * Fires when opening the Modal.
         *
         * @since 1.0.0
         *
         * @param shortcode The shortcode that was activated.
         */
        open: function (shortcode) {

            if (typeof shortcode !== 'undefined' && shortcode !== null) {
                Render_Modal.modify(shortcode);
            } else {

                // Disable wrapping shortcodes when there's no selection
                if (!Render_Modal.selection) {
                    $modal_shortcodes.find('.render-modal-shortcode.wrapping:not(.nested-parent)').each(function () {

                        Render_Modal.disableShortcode(
                            $(this),
                            l18n.select_content_from_editor
                        );
                    });
                }

                Render_Modal.open();
            }

            // Hide any identical shortcodes if in a nesting shortcode
            var $cursor_node = $(Render_tinymce.active_editor.selection.getNode()),
                $nesting_shortcode = $cursor_node.length ? $cursor_node.closest('.nested-child') : false;

            // Disable nesting shortcodes from being nested inside another
            if ($nesting_shortcode.length) {

                var nesting_shortcode = $nesting_shortcode.parent().closest('.render-tinymce-shortcode-wrapper').data('code');

                $modal_shortcodes.find('.render-modal-shortcode[data-code="' + nesting_shortcode + '"]').each(function () {

                    Render_Modal.disableShortcode(
                        $(this),
                        l18n.cannot_place_shortcode_here
                    );
                });
            }
        },

        /**
         * Fires when closing the Modal.
         *
         * @since 1.0.0
         */
        close: function () {
            this.active_editor.focus();
        },

        /**
         * Fires when submitting the Modal.
         *
         * @since 1.0.0
         */
        update: function () {

            // If we're editing a shortcode, select the node with TinyMCE
            var $shortcode = $(this.active_editor.dom.select('.render-tinymce-editing'));
            if ($shortcode.length) {
                this.active_editor.selection.select($shortcode.get(0));
            }

            // Replace or insert the content
            if (this.active_editor.selection.getContent().length) {
                this.active_editor.selection.setContent(Render_Modal.output.all);
            } else {
                this.active_editor.insertContent(Render_Modal.output.all);
            }

            // Render the shortcodes
            if (Render_Data.do_render) {
                this.loadVisual();
            }
        },

        /**
         * Removes a shortcode from the TinyMCE body.
         *
         * @since 1.0.0
         */
        removeShortcode: function () {

            var $container = $('<div />').append($(Render_tinymce.active_editor.getBody()).html()),
                $shortcode = $container.find('.render-tinymce-editing'),
                $content = $shortcode.find('.render-tinymce-shortcode-content'),
                data = render_data[$shortcode.data('code')]['render'],
                nested = typeof data != 'undefined' && typeof data['nested'] != 'undefined';

            // Strip the shortcode if there is content and this isn't a nesting shortcode
            if ($content.length && !nested) {
                $shortcode.replaceWith($content.contents());
            } else {
                $shortcode.remove();
            }

            this.active_editor.setContent($container.html());

            Render_Modal.close();
        },

        /**
         * Shows or hides the loading overlay and cycles the messages.
         *
         * @since 1.0.0
         *
         * @param loading Whether to show or hide the overlay.
         */
        loading: function (loading) {

            var $container = $(Render_tinymce.active_editor.getContainer()).closest('.wp-editor-wrap'),
                $loader = $container.find('.render-tinymce-loader');

            if (loading) {

                // Makes sure the the loader stays for a minimum time
                min_load_time = true;
                setTimeout(function () {
                    min_load_time = false;
                }, 1000);

                // Get a random loading message
                var loading_messages = Render_Data.loading_messages,
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
                $container.find('.switch-html').prop('disabled', true);
                $container.find('.switch-tmce').prop('disabled', true);
                $container.find('.wp-media-buttons').addClass('disabled');
            } else {

                $loader.addClass('hide');
                $container.find('.switch-html').prop('disabled', false);
                $container.find('.switch-tmce').prop('disabled', false);
                $container.find('.wp-media-buttons').removeClass('disabled');
            }
        },

        /**
         * Gets the editor (from within the confines of this object).
         *
         * @since 1.0.0
         *
         * @returns The active editor.
         */
        getEditor: function () {
            return this.active_editor;
        },

        /**
         * Converts literal shortcode within the content into rendered HTML.
         *
         * @since 1.0.0
         *
         * @param content The content to convert.
         */
        convertLiteralToRendered: function (content) {

            Render_tinymce.loading(true);

            var data;

            if (typeof Render_Data.render_data !== 'undefined') {
                data = Render_Data.render_data;
            }

            data.action = 'render_render_shortcodes';
            data.content = content;
            data.shortcode_data = Render_Data.rendered_shortcodes;

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: data,
                editor: this.active_editor,
                success: function (response) {

                    Render_tinymce.active_editor = this.editor;

                    Render_tinymce.active_editor.setContent(response);

                    // no-js support
                    $(Render_tinymce.active_editor.getBody()).find('.no-js').css('display', 'none');

                    Render_tinymce.loading(false);

                    $(document).trigger('render-tinymce-post-render');
                }
            });
        },

        /**
         * Converts Rendered shortcode HTML in the content into literal shortcodes.
         *
         * @since 1.0.0
         *
         * @param content The content to convert.
         * @returns The converted content.
         */
        convertRenderedToLiteral: function (content) {

            var $container = $('<div />').append(content);

            // Remove dummy containers
            $container.find('.render-tinymce-dummy-container').each(function () {
                $(this).replaceWith(this.childNodes);
            });

            var $shortcodes = $container.find('.render-tinymce-shortcode-wrapper').sortByDepth();

            $shortcodes.each(function () {

                var atts = $(this).data('atts'),
                    code = $(this).data('code'),
                    shortcode_content = $(this).find('.render-tinymce-shortcode-content').first().html(),
                    output = '[' + code;

                if (atts) {

                    // Parse if they aren't already ($.data auto parses it)
                    if (typeof atts !== 'object') {
                        atts = JSON.parse(atts);
                    }

                    var _atts = '';
                    $.each(atts, function (name, value) {
                        _atts += ' ' + name + '=\'' + value + '\'';
                    });
                    output += _atts;
                }

                output += ']';

                if (shortcode_content) {
                    output += shortcode_content + '[/' + code + ']';
                }

                $(this).replaceWith(output);
            });

            return $container.html();
        }
    };

    $(function () {
        Render_tinymce.init();
    });

    window['RenderRefreshTinyMCE'] = function () {
        Render_tinymce.loadVisual();
    }
})(jQuery);