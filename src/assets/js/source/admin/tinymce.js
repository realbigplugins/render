/**
 * Adds the TinyMCE button and all TinyMCE functionality.
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
(function (window, wp, wpviews, $) {

    //noinspection JSUnresolvedVariable
    var data = Render_Data,
        min_load_time = false,
        last_message = 0,
        render_shortcode_data = data['all_shortcodes'],
        l18n = data['l18n'],
        sc_editor_error_timeout = null,
        $modal_shortcodes = $('#render-modal-wrap').find('.render-modal-shortcodes'),
        key_map = [];

    Render_tinymce = {

        /**
         * The currently active editor.
         *
         * @since {{VERSION}}
         */
        active_editor: null,

        /**
         * The jQuery HTML object for the shortcode content editor.
         *
         * @since {{VERSION}}
         */
        $shortcode_content_editor: null,

        /**
         * The TinyMCE editor object of the currently being used editor.
         *
         * @since {{VERSION}}
         */
        editing_shortcode_content_editor: null,

        /**
         * Initializes the object.
         *
         * @since 1.0.0
         */
        init: function () {

            this.$shortcode_content_editor = $('#render-tinymce-sc-content-editor');

            this.createWPView();

            this.tinymceInit();
            this.binds();
            //this.keyboardShortcuts();
            this.createLoaders();
        },

        createWPView: function () {

            var shortcodes = data['all_shortcodes'] || false,
                rendertinymce = this;

            if (shortcodes) {
                $.each(shortcodes, function (code, shortcode) {

                    // View already registered
                    if (wpviews.get(code)) {
                        return true; // continue $.each()
                    }

                    wpviews.register(code, {

                        initialize: function () {
                            this.renderShortcode();
                        },

                        edit : function( shortcode, update ) {

                            var self = this;

                            // Backwards compatability for WP pre-4.2
                            if ( 'object' === typeof( shortcode ) ) {
                                shortcode = decodeURIComponent( $(shortcode).attr('data-wpview-text') );
                            }

                            this.editing = true;
                            rendertinymce.editing = true;

                            Render_Modal.modify(shortcode);

                            // Bind update function
                            $(document).on('render-modal-update', function (e, output) {

                                if (self.editing) {
                                    delete self.editing;
                                    delete rendertinymce.editing;
                                    update(output.all);
                                }
                            });

                        },

                        renderShortcode: function () {

                            var self = this;

                            if (!this.getting) {

                                this.getting = true;

                                wp.ajax.post('render_shortcode', {
                                    post_ID: $('#post_ID').val(),
                                    shortcode: self.buildShortcode()
                                    //shortcode: this.shortcodeModel.formatShortcode(),
                                    //nonce: shortcodeUIData.nonces.preview,
                                }).done(function (response) {

                                    if ('' === response) {
                                        self.content = 'empty!';
                                    } else {
                                        self.content = response;
                                    }

                                }).fail(function () {

                                    self.content = 'error!';

                                }).always(function () {

                                    // So it only gets one time (waits for the AJAX to come back)
                                    delete self.getting;

                                    // Render the shortcode
                                    self.render(null, true);

                                });
                            }
                        },

                        buildShortcode: function () {

                            var shortcode = this.shortcode,
                                attrs = shortcode['attrs']['named'],
                                content = shortcode['content'],
                                tag = shortcode['tag'],
                                type = shortcode['type'],
                                output = '[' + tag;

                            // Add attributes if they exist
                            if (attrs) {
                                $.each(attrs, function (attr, value) {

                                    // Skip empty attributes.
                                    if ( ! value ||  value.length < 1 ) {
                                        return;
                                    }

                                    output += ' ' + attr + '="' + value + '"';
                                });
                            }

                            // Close opening tag
                            output += ']';

                            // Add content, if exists
                            if (content) {
                                output += content;
                            }

                            // Add closing tag, if exists
                            if (type == 'closed') {
                                output += '[/' + tag + ']';
                            }

                            return output;
                        }
                    });
                })
            }
        },

        /**
         * Sets up the handlers.
         *
         * @since 1.0.0
         */
        binds: function () {

            $(document).on('render-modal-close', function () {
                //Render_tinymce.close();
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

            this.$shortcode_content_editor.find('.submit').click(function () {
                Render_tinymce.updateShortcodeContent();
                return false;
            });

            this.$shortcode_content_editor.find('.cancel').click(function () {
                Render_tinymce.closeShortcodeContentEditor();
                return false;
            });

            Render_Modal.getElement('backdrop').click(function (event) {

                if (Render_tinymce.editing_shortcode_content_editor === null) {
                    return;
                }

                Render_tinymce.closeShortcodeContentEditor();
                event.preventDefault();
            });
        },

        keyboardShortcuts: function () {

            $(document).keyup(function (e) {

                // Don't bother if not open
                if (!this.editing_shortcode_content_editor) {
                    return;
                }

                switch (e.which) {

                    // Escape
                    case 27:
                        e.preventDefault();
                        Render_tinymce.closeShortcodeContentEditor();
                        break;
                }
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
        tinymceInit: function () {

            tinymce.PluginManager.add('render', function (editor) {

                editor.addButton('render_open', {

                    // Establishes an icon class for the button with the prefix "mce-i-"
                    icon: 'render-mce-icon',
                    cmd: 'Render_Open',
                    tooltip: l18n.add_shortcode
                });

                // Fires when clicking the shortcode <> button in the tinymce toolbar
                editor.addCommand('Render_Open', function () {

                    var selection, $selection;

                    if (data['do_render']) {

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

                if (!data['do_render']) {
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

            });
        },


        /**
         * Fires when clicking on a shortcode or the edit content button in the shortcode actions toolbar.
         *
         * @since {{VERSION}}
         */
        editShortcodeContent: function () {

            // Set the shortcode editing editor
            this.editing_shortcode_content_editor = this.active_editor;

            var $shortcode = $(this.editing_shortcode_content_editor.getBody()).find('.render-tinymce-editing-content'),
                $content_container = $('<div />'),
                content = $shortcode.find('.render-tinymce-shortcode-content').html();

            // Remove any placeholders before editing the content
            $content_container.append(content);
            $content_container.find('.render-tinymce-shortcode-placeholder').remove();
            content = $content_container.html();

            this.openShortcodeContentEditor($shortcode, content);
        },

        /**
         * Updates the currently being edited shortcode from the shortcode content editor.
         *
         * @since {{VERSION}}
         */
        updateShortcodeContent: function () {

            var sc_editor = tinymce.get('render-tinymce-shortcode-content'),
                content = sc_editor.getContent(),
                $shortcode = $(this.editing_shortcode_content_editor.getBody()).find('.render-tinymce-editing-content'),
                code = $shortcode.data('code');

            // Pass content through some filtering
            // Strip tags
            if (render_shortcode_data[code]['render'] && render_shortcode_data[code]['render']['displayInline']) {
                content = content.replace(new RegExp(data['block_regex'], 'gi'), '');
            }

            // Set the content of the shortcode being edited
            $shortcode.find('.render-tinymce-shortcode-content').html(content);

            this.closeShortcodeContentEditor();

            // Render the shortcodes
            if (data['do_render']) {
                this.loadVisual();
            } else {
                $shortcode.removeClass('render-tinymce-editing-content');
            }
        },

        /**
         * Closes the shortcode content editor modal.
         *
         * @since {{VERSION}}
         */
        closeShortcodeContentEditor: function () {

            var $shortcode = $(this.editing_shortcode_content_editor.getBody()).find('.render-tinymce-editing-content');

            // Make sure we tell TinyMCE we're not editing a shortcode anymore
            $shortcode.removeClass('render-tinymce-editing-content');

            // Hide the modal (no fancy animations here, just quick)
            this.$shortcode_content_editor.hide().removeClass('active');

            // Remove loading overlay from editor and focus it
            this.loading(false, this.editing_shortcode_content_editor);
            this.editing_shortcode_content_editor.focus();

            // Hide backdrop
            Render_Modal.getElement('backdrop').hide();
            Render_Modal.keepBackdrop = false;

            // Reset the active editor
            this.active_editor = this.editing_shortcode_content_editor;
            this.editing_shortcode_content_editor = null;

        },

        /**
         * Opens the shortcode content editor modal.
         *
         * When opening the modal, it animates it from where the shortcode is in the TinyMCE. This helps make apparent
         * that you are editing a specific shortcode.
         *
         * @since {{VERSION}}
         *
         * @param $shortcode The shortcode being edited.
         * @param content The content to place in the editor.
         */
        openShortcodeContentEditor: function ($shortcode, content) {

            // Remove instance because moving the iframe breaks it
            tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'render-tinymce-shortcode-content');

            // Show the backdrop
            Render_Modal.getElement('backdrop').show();
            Render_Modal.keepBackdrop = true;

            // "Disable" the editor
            this.loading(true, this.editing_shortcode_content_editor);

            // Insert shortcode name into title
            this.$shortcode_content_editor.find('.render-tinymce-sc-content-editor-title-sc-name').html(
                $shortcode.data('name')
            );

            this.$shortcode_content_editor.data({
                name: $shortcode.data('name'),
                code: $shortcode.data('code')
            });

            // Show the sc content editor (with a cool effect!)
            var width = $(window).width() < 500 ? $(window).width() * 0.9 : 500,
                height = $(window).height() < 600 ? $(window).height() * 0.9 : 600,
                marginLeft = width / 2 * -1,
                marginTop = height / 2 * -1,
                $container = $(this.editing_shortcode_content_editor.getContainer()),
                sc_offset_left = $shortcode.offset().left + ($shortcode.width() / 2) + $container.offset().left,
                sc_offset_top = $shortcode.offset().top + ($shortcode.height() / 2) + $container.offset().top,
                animation_time = 300,
                window_scroll = $(window).scrollTop();

            // Compensate for window scroll
            if (window_scroll) {
                sc_offset_top = sc_offset_top - window_scroll;
            }

            // Final size and starting position
            this.$shortcode_content_editor.css({
                width: width,
                height: height,
                marginLeft: marginLeft,
                marginTop: marginTop,
                left: sc_offset_left,
                top: sc_offset_top
            });

            // Animate position from shortcode
            this.$shortcode_content_editor.show().delay(100).animate({
                left: '50%',
                top: '50%'
            }, animation_time);

            // Grow the modal
            this.$shortcode_content_editor.find('.render-tinymce-sc-content-editor-container').hide().show('scale', {
                origin: ['middle', 'center'],
                percent: 100,
                scale: 'box',
                complete: function () {

                    // Delay fixes no-transition bug
                    setTimeout(function () {

                        var $sc_editor = Render_tinymce.$shortcode_content_editor;

                        // Adding active removes the cover
                        $sc_editor.addClass('active');

                        // Re-initialize the editor now that the iframe is done moving
                        tinymce.EditorManager.execCommand('mceAddEditor', true, 'render-tinymce-shortcode-content');

                        var editor = tinymce.get('render-tinymce-shortcode-content');

                        // Set the content to whatever the content was of the shortcode being edited
                        editor.setContent(content);

                        // Editor height
                        var $main_editor = $('#wp-render-tinymce-shortcode-content-wrap');
                        $main_editor.find('.mce-edit-area').css(
                            'height',
                            $sc_editor.height() -
                            ($main_editor.height() - $main_editor.find('.mce-edit-area').height()) -
                            $sc_editor.find('.render-tinymce-sc-content-editor-actions').outerHeight(true) -
                            $sc_editor.find('.render-tinymce-sc-content-editor-title').outerHeight(true)
                        );

                        // Focus the editor
                        Render_tinymce.active_editor.focus();
                    }, 1);
                }
            }, animation_time);
        },

        /**
         * Displays an error in the shortcode content editor.
         *
         * @since {{VERSION}}
         *
         * @param {string} message   The error message to display.
         * @param {int}    [timeout] How long to show the message (default of 2sec).
         */
        showSCEditorError: function (message, timeout) {

            var $error = this.$shortcode_content_editor.find('.render-tinymce-sc-content-editor-error');

            timeout = timeout || 3000;

            $error.html(message).addClass('show');

            // Reset (if already set) the timeout
            if (sc_editor_error_timeout !== null) {
                clearTimeout(sc_editor_error_timeout);
                $error.effect('shake', {
                    distance: 10,
                    times: 2
                }, 200);
            }

            sc_editor_error_timeout = setTimeout(function () {
                $error.removeClass('show');
                sc_editor_error_timeout = null;
            }, timeout);
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
            // Nobody loves me...
        },

        /**
         * Converts rendered shortcodes into literal shortcodes (Visual -> Text).
         *
         * @since 1.0.0
         */
        loadText: function (content) {

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
         * @param [shortcode] The shortcode that was activated.
         * @param [$shortcode] The shortcode DOM object.
         */
        open: function (shortcode, $shortcode) {

            if (typeof shortcode !== 'undefined' && shortcode !== null) {
                Render_Modal.modify(shortcode);
            }

            // Disable wrapping shortcodes when there's no selection
            if (!Render_Modal.selection) {

                $modal_shortcodes.find('.render-modal-shortcode.wrapping:not(.nested-parent)').each(function () {

                    Render_Modal.disableShortcode(
                        $(this),
                        l18n['select_content_from_editor']
                    );
                });
            }

            // Disable adding a shortcode in another shortcode
            if (this.editing_shortcode_content_editor) {

                var code = this.$shortcode_content_editor.data('code');

                $modal_shortcodes.find('.render-modal-shortcode[data-code="' + code + '"]').each(function () {

                    Render_Modal.disableShortcode(
                        $(this),
                        l18n['cannot_nest_identical']
                    );
                });
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
                        l18n['cannot_place_shortcode_here']
                    );
                });
            }

            // And finally, open the Modal
            Render_Modal.open();
        },

        /**
         * Fires when closing the Modal.
         *
         * @since 1.0.0
         */
        close: function () {

            // Remove the currently being edited shortcode class (if any)
            $(this.active_editor.dom.select('.render-tinymce-editing')).removeClass('render-tinymce-editing');

            // Re-focus the editor
            this.active_editor.focus();
        },

        /**
         * Fires when submitting the Modal.
         *
         * @since 1.0.0
         */
        update: function () {

            if (this.editing) {
                return;
            }

            this.active_editor.insertContent(Render_Modal.output.all);
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
                data = render_shortcode_data[$shortcode.data('code')]['render'],
                nested = typeof data != 'undefined' && typeof data['nested'] != 'undefined',
                $new_content = $content.length ? $content.contents() : '',
                re_load = false;

            // If this is a nesting shortcode, combine all of the nested children content, and use that for the new content
            if (nested) {

                var $contents;

                $new_content = '';

                $content.children('.render-tinymce-shortcode-wrapper.nested-child').each(function () {

                    $(this).find('.render-tinymce-shortcode-content').first().each(function () {

                        // Because we're using loadText(), we'll need to re-load visual
                        re_load = true;

                        $contents = Render_tinymce.loadText($('<div />').append($(this).contents()).html());

                        if ($new_content === '') {
                            $new_content = $('<p />').append($contents);
                        } else {
                            $new_content = $new_content.add($('<p />').append($contents));
                        }
                    });
                });
            }

            $shortcode.replaceWith($new_content);

            this.active_editor.setContent($container.html());

            Render_Modal.close();

            if (re_load) {
                this.loadVisual();
            }
        },

        /**
         * Shows or hides the loading overlay and cycles the messages.
         *
         * @since 1.0.0
         *
         * @param loading Whether to show or hide the overlay.
         * @param [editor] The editor to set to loading.
         */
        loading: function (loading, editor) {

            editor = editor || Render_tinymce.active_editor;

            var $container = $(editor.getContainer()).closest('.wp-editor-wrap'),
                $loader = $container.find('.render-tinymce-loader');

            if (loading) {

                // Get a random loading message
                var loading_messages = data['loading_messages'],
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

            var post_data;

            if (typeof data['render_data'] !== 'undefined') {
                post_data = data['render_data'];
            }

            post_data.action = 'render_render_shortcodes';
            post_data.content = content;
            post_data.shortcode_data = data['rendered_shortcodes'];
            post_data.editor_id = this.active_editor.id;

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: post_data,
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

            // Remove placeholders
            $container.find('.render-tinymce-shortcode-placeholder').remove();

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

                if (render_shortcode_data[code]['wrapping']) {
                    output += (shortcode_content || '') + '[/' + code + ']';
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
    };
})(window, window.wp, window.wp.mce.views, window.jQuery);