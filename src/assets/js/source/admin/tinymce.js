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
(function ($) {

    //noinspection JSUnresolvedVariable
    var data = Render_Data,
        min_load_time = false,
        last_message = 0,
        render_shortcode_data = data['all_shortcodes'],
        l18n = data['l18n'],
        sc_editor_error_timeout = null,
        $modal_shortcodes = $('#ender-modal-wrap').find('.render-modal-shortcodes');

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

            this.addToTinymce();
            this.binds();
            this.keyboardShortcuts();
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
                if (this.editing_shortcode_content_editor === null) {
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

                // Click the editor to edit shortcodes (but not in the sc content editor!)
                editor.onClick.add(function (editor, event) {

                    Render_tinymce.active_editor = editor;

                    // Remove delete overlay for all shortcodes
                    var $shortcode = $(event.target).closest('.render-tinymce-shortcode-wrapper'),
                        content, container_html, shortcode;

                    if ($(event.target).hasClass('render-tinymce-shortcode-wrapper-edit')) {

                        // Edit a shortcode
                        content = $shortcode.find('.render-tinymce-shortcode-content').html();
                        container_html = $('<div />').append($shortcode.clone()).html();
                        shortcode = Render_tinymce.convertRenderedToLiteral(container_html);

                        if (content && content.length) {
                            Render_Modal.selection = content;
                        }

                        $shortcode.addClass('render-tinymce-editing');

                        Render_tinymce.open(shortcode);

                    } else if ($(event.target).hasClass('render-tinymce-shortcode-wrapper-remove')) {

                        // Remove a shortcode
                        $(event.target).closest('.render-tinymce-shortcode-wrapper').addClass('render-tinymce-editing');
                        Render_tinymce.removeShortcode();

                    } else if (
                        !$shortcode.find('.nested-child').length &&
                        $shortcode.length &&
                        $shortcode.find('.render-tinymce-shortcode-content').length
                    ) {

                        // Notify user you can't edit shortcode content when in sc content editor
                        if (editor.id == 'render-tinymce-shortcode-content') {

                            var message = data['l18n']['cannot_edit_sc_content'],
                                timeout = null;

                            // Eventually show more detailed message
                            if (sc_editor_error_timeout !== null) {
                                message = data['l18n']['cannot_edit_sc_content_detail'];
                                timeout = 6000;
                            }

                            // Replace {shortcode1} snd {shortcode2} with actual names
                            message = message.replace(/\{shortcode1}/g, Render_tinymce.$shortcode_content_editor.data('name'));
                            message = message.replace(/\{shortcode2}/g, $shortcode.data('name'));

                            Render_tinymce.showSCEditorError(message, timeout);
                            return;
                        }

                        // Edit a shortcode's content
                        $shortcode.addClass('render-tinymce-editing-content');
                        Render_tinymce.editShortcodeContent();
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

                    shortcode_data = render_shortcode_data[$shortcode.data('code')]['render'];

                    // Must not have image editing allowed
                    if (typeof shortcode_data != 'undefined' && typeof shortcode_data['allowImageEditing'] != 'undefined') {
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

                    // Delay allows for setting content before loading
                    setTimeout(function () {
                        Render_tinymce.active_editor = editor;
                        Render_tinymce.loadVisual();
                    }, 1);
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
                //    var wp_regex = data.shortcode_regex.match(/\((\w+\|?)+\)/),
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

            // Insert shortcode name into title
            this.$shortcode_content_editor.find('.render-tinymce-sc-content-editor-title-sc-name').html(
                $shortcode.data('name')
            );

            this.$shortcode_content_editor.data('name', $shortcode.data('name'));

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
            if (data['do_render']) {
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
                data = render_shortcode_data[$shortcode.data('code')]['render'],
                nested = typeof data != 'undefined' && typeof data['nested'] != 'undefined',
                $new_content = $content.length ? $content.contents() : '';

            // If this is a nesting shortcode, combine all of the nested children content, and use that for the new content
            if (nested) {

                var $contents;

                $new_content = '';

                $content.children('.render-tinymce-shortcode-wrapper.nested-child').each(function () {

                    $(this).find('.render-tinymce-shortcode-content').first().each(function () {

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

            this.loadVisual();
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
})(jQuery);