/**
 * Functionality for the Render modal.
 *
 * @since 1.0.0
 *
 * @global Render_Data
 *
 * @package Render
 * @subpackage Modal
 */
var Render_Modal;
(function ($) {

    //noinspection JSUnresolvedVariable
    var elements = {},
        shortcodes = {},
        slide_transition = 150,
        categories_sliding = false,
        render_modal_open = false,
        error_color = '#ec6750',
        l18n = Render_Data.l18n,
        render_data = Render_Data.all_shortcodes,
        _search_timeout, search_loading;

    Render_Modal = {

        current_shortcode: '',
        active_shortcode: '',
        output: '',
        selection: '',
        modifying: false,

        /**
         * Initializes the object.
         *
         * @since 1.0.0
         */
        init: function () {

            this.establishElements();
            this.binds();
            this.keyboardShortcuts();
            this.preventWindowScroll();
            this.search();
        },

        /**
         * Fires on window resize.
         *
         * @since 1.0.0
         */
        resize: function () {
            this.listHeight();
        },

        /**
         * Sets up our various Modal elements.
         *
         * @since 1.0.0
         */
        establishElements: function () {

            elements.wrap = $('#render-modal-wrap');
            elements.submit = $('#render-modal-submit');
            elements.backdrop = $('#render-modal-backdrop');
            elements.cancel = elements.wrap.find('.render-modal-cancel');
            elements.close = elements.wrap.find('.render-modal-close');
            elements.remove = $('#render-modal-remove');
            elements.title = elements.wrap.find('.render-modal-title');
            elements.search = elements.wrap.find('.render-modal-search');
            elements.categories = elements.wrap.find('.render-modal-categories');
            elements.footer = elements.wrap.find('.render-modal-footer');
            elements.list = elements.wrap.find('.render-modal-shortcodes');
            elements.search_input = elements.wrap.find('input[name="render-modal-search"]');
            elements.active_shortcode = false;
            elements.last_active_shortcode = false;
        },

        /**
         * Sets up handlers.
         *
         * @since 1.0.0
         */
        binds: function () {

            // Active a shortcode
            elements.list.find('.accordion-section-title, .render-modal-sc-title').click(function () {
                Render_Modal.activateShortcode($(this));
            });

            // Shortcode toolbar toggle
            elements.list.find('.render-modal-shortcode-toolbar-toggle').click(function () {
                Render_Modal.shortcodeToolbarTogggle($(this));
            });

            // Restore shortcode
            elements.list.find('.render-modal-shortcode-toolbar-button-restore').click(function () {

                if (!$(this).hasClass('disabled')) {
                    Render_Modal.restoreShortcode();
                }
            });

            // Submit the form
            elements.submit.off('click').click(function (event) {
                event.preventDefault();
                Render_Modal.update();
            });

            // Remove button
            elements.remove.click(function () {
                $(document).trigger('render-modal-remove');
            });

            // Close the form
            elements.cancel.click(function (event) {
                event.preventDefault();
                Render_Modal.close();
            });
            elements.close.click(function (event) {
                event.preventDefault();
                Render_Modal.close();
            });
            elements.backdrop.click(function (event) {
                event.stopPropagation();
                Render_Modal.close();
            });

            // Filter shortcodes by category
            elements.categories.find('li').click(function () {
                Render_Modal.filterByCategory($(this));
            });

            // Show advanced atts
            elements.list.find('.render-modal-show-advanced-atts').click(function () {
                Render_Modal.toggleAdvancedAtts($(this));
                return false;
            });

            // Move categories left and right
            elements.categories.find('.render-modal-categories-left').click(Render_Modal.moveCategoriesLeft);
            elements.categories.find('.render-modal-categories-right').click(Render_Modal.moveCategoriesRight);
        },

        /**
         * Provides keyboard navigation for the Modal.
         *
         * @since 1.0.0
         */
        keyboardShortcuts: function () {

            $(document).keyup(function (e) {

                if (!render_modal_open) {
                    return;
                }

                switch (e.which) {

                    // Enter
                    case 13:

                        e.preventDefault();
                        Render_Modal.update();
                        break;

                    // Escape
                    case 27:
                        e.preventDefault();
                        Render_Modal.close();
                        break;

                    // Tab
                    case 9:

                        if (elements.search.find('input[type="text"]').is(':focus')) {

                            e.preventDefault();

                            if (elements.active_shortcode) {
                                elements.active_shortcode.find('.render-modal-att-row').first().focus();
                            } else {

                                elements.list.find('li').each(function () {

                                    if ($(this).is(':visible')) {

                                        var $first = $(this);
                                        if ($next.length && $next.is(':visible')) {
                                            Render_Modal.activateShortcode($first);
                                        }
                                        return false;
                                    }
                                });
                            }
                        }
                        break;

                    // Down arrow
                    case 40:

                        e.preventDefault();

                        var $next;
                        if (!elements.active_shortcode) {

                            elements.list.find('li').each(function () {

                                if ($(this).is(':visible')) {

                                    $next = $(this);
                                    if ($next.length && $next.is(':visible')) {
                                        Render_Modal.activateShortcode($next);
                                    }
                                    return false;
                                }
                            });
                        } else {
                            $next = elements.active_shortcode.next();

                            if ($next.length && $next.is(':visible')) {

                                Render_Modal.closeShortcode();
                                Render_Modal.activateShortcode($next);
                            } else {
                                elements.active_shortcode.effect('shake', {
                                    distance: 10
                                }, 200);
                            }
                        }
                        break;

                    // Up arrow
                    case 38:

                        e.preventDefault();

                        var $prev;
                        if (!elements.active_shortcode) {

                            $(elements.list.find('li').get().reverse()).each(function () {
                                //elements.list.find('li').each(function () {

                                if ($(this).is(':visible')) {

                                    $prev = $(this);
                                    if ($prev.length && $prev.is(':visible')) {
                                        Render_Modal.activateShortcode($prev);
                                    }
                                    return false;
                                }
                            });
                        } else {
                            $prev = elements.active_shortcode.prev();

                            if ($prev.length && $prev.is(':visible')) {

                                Render_Modal.closeShortcode();
                                Render_Modal.activateShortcode($prev);
                            } else {
                                elements.active_shortcode.effect('shake', {
                                    distance: 10
                                }, 200);
                            }
                        }
                        break;
                    default:
                        return;
                }
            });
        },

        /**
         * Moves the categories left when clicking the right arrow.
         *
         * @since 1.0.0
         */
        moveCategoriesLeft: function () {

            var $list = elements.categories.find('ul'),
                individual_width = elements.categories.find('li').width(),
                current_offset = $list.css('left') != 'auto' ? parseInt($list.css('left')) : 0;

            if (current_offset < 0 && !categories_sliding) {
                categories_sliding = true;
                $list.animate({left: current_offset + individual_width}, {
                    duration: 300,
                    complete: function () {
                        categories_sliding = false;
                    }
                });
            }
        },

        /**
         * Moves the categories right when clicking the left arrow.
         *
         * @since 1.0.0
         */
        moveCategoriesRight: function () {

            var $list = elements.categories.find('ul'),
                individual_width = elements.categories.find('li').width(),
                total_width = elements.categories.find('li').length * individual_width,
                visible_width = 5 * individual_width,
                max_offset = (total_width - visible_width) * -1,
                current_offset = $list.css('left') != 'auto' ? parseInt($list.css('left')) : 0;

            if (current_offset > max_offset && !categories_sliding) {
                categories_sliding = true;

                $list.animate({left: current_offset + (individual_width * -1)}, {
                    duration: 300,
                    complete: function () {
                        categories_sliding = false;
                    }
                });
            }
        },

        /**
         * Initializes all attributes in a shortcode, when the shortcode is opened.
         *
         * @since 1.0.0
         */
        initAtts: function () {

            elements.active_shortcode.find('.render-modal-att-row').each(function () {

                // Skip if already initialized or if set to not initialize
                if (typeof $(this).data('attObj') != 'undefined' || $(this).data('no-init')) {
                    return true; // Continue $.each
                }

                var att_type = $(this).data('att-type'),
                    attObj = false;

                // Initialize each type of att
                switch (att_type) {

                    case 'hidden':

                        attObj = new Hidden($(this));
                        break;

                    case 'selectbox':

                        attObj = new Selectbox($(this));
                        break;

                    case 'colorpicker':

                        attObj = new Colorpicker($(this));
                        break;

                    case 'media':

                        attObj = new Media($(this));
                        break;

                    case 'slider':

                        attObj = new Slider($(this));
                        break;

                    case 'counter':

                        attObj = new Counter($(this));
                        break;

                    case 'repeater':

                        attObj = new Repeater($(this));
                        break;

                    case 'checkbox':

                        attObj = new Checkbox($(this));
                        break;

                    case 'toggle':

                        attObj = new Toggle($(this));
                        break;

                    case 'textarea':

                        attObj = new TextArea($(this));
                        break;

                    case 'textbox':

                        attObj = new TextBox($(this));
                        break;

                    default:

                        render_log_error('Invalid attribute type "' + att_type + '" for shortcode ' + $(this).data('code'));
                        break;
                }

                // Something bad happened!
                if (typeof attObj === false) {
                    render_log_error('No attObj for shortcode ' + $(this).data('code'));
                    return true; // continue $.each
                }

                $(this).data('attObj', attObj);

                // Custom callback
                if ($(this).data('init-callback') !== false) {
                    window[$(this).data('init-callback')]($(this), attObj);
                }
            });

            elements.active_shortcode.trigger('render-modal-shortcode-init');
        },

        /**
         * Searches through the shortcodes.
         *
         * @since 1.0.0
         */
        search: function () {

            var search_delay = 300,
                search_fade = 300;

            elements.search_input.on('keyup', function (e) {

                // Don't search for certain keys
                if (e.which == 9 || e.which == 13 || e.which == 40 || e.which == 38) {
                    return;
                }

                var search_query = $(this).val(),
                    matches = search_query.match(/[a-zA-Z0-9\s_]/g);

                // Don't search if the query isn't allowed characters
                if (search_query.length && (matches === null || matches.length !== search_query.length)) {
                    Render_Modal.invalidSearch(true);
                    return;
                } else {
                    Render_Modal.invalidSearch(false);
                }

                // Don't search if empty
                if (!search_query.length) {
                    Render_Modal.clearSearch(search_fade);
                    return;
                }

                if (!search_loading) {
                    elements.list.stop().animate({opacity: 0}, search_fade);
                }

                search_loading = true;

                clearTimeout(_search_timeout);
                _search_timeout = setTimeout(function () {

                    search_loading = false;
                    elements.list.stop().animate({opacity: 1}, search_fade);
                    elements.list.scrollTop(0);
                    Render_Modal.closeShortcode();

                    elements.list.find('.render-modal-shortcode').each(function () {
                        var title = $(this).find('.render-modal-shortcode-title').text(),
                            description = $(this).find('.render-modal-shortcode-description').text(),
                            code = $(this).data('code'),
                            source = $(this).data('source'),
                            tags = $(this).data('tags'),
                            search_string = title + description + code + source + tags;

                        if (search_string.toLowerCase().indexOf(search_query.toLowerCase()) < 0) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }, search_delay);
            });
        },

        /**
         * Clears the search and search errors.
         *
         * @since 1.0.0
         *
         * @param time How long the animation takes.
         */
        clearSearch: function (time) {

            time = typeof time === 'undefined' ? 0 : time;
            elements.search_input.val('');
            elements.list.find('.render-modal-shortcode').show();
            clearTimeout(_search_timeout);
            this.closeShortcode();
            this.invalidSearch(false);
            elements.list.stop().animate({opacity: 1}, time);
            search_loading = false;
        },

        /**
         * Shows when searching for invalid characters.
         *
         * @since 1.0.0
         *
         * @param invalid Whether to show or hide.
         */
        invalidSearch: function (invalid) {

            var $invalidsearch = elements.wrap.find('.render-modal-invalidsearch');

            if (invalid) {
                $invalidsearch.show();
            } else {
                $invalidsearch.hide();
            }
        },

        /**
         * Activates a shortcode.
         *
         * Sets which shortcode is the currently activated shortcode in the Modal.
         *
         * @since 1.0.0
         *
         * @param $e Which shortcode to activate (by jQuery element).
         */
        activateShortcode: function ($e) {

            var $container = $e.closest('.render-modal-shortcode');

            // Make sure all shortcodes are don't show disabled text
            elements.list.find('.render-modal-shortcode.render-modal-shortcode-disabled').each(function () {
                Render_Modal.toggleDisabledText($(this), false);
            });

            // Bail if the shortcode is disabled
            if ($container.hasClass('render-modal-shortcode-disabled')) {
                this.toggleDisabledText($container, true);
                return;
            }

            if ($container.hasClass('active')) {
                this.closeShortcode();
                elements.active_shortcode = false;
                elements.last_active_shortcode = false;
                this.active_shortcode = '';
                return;
            }

            this.closeShortcode();

            elements.active_shortcode = $container;
            this.active_shortcode = $container.data('code');

            // Change submit button
            if (this.modifying) {

                if (elements.active_shortcode.hasClass('current-shortcode')) {
                    this.submitButton('modify');
                } else {
                    this.submitButton('change');
                }
            } else {
                this.submitButton('add');
            }

            // Enable / Disable restore button
            if (this.modifying && this.active_shortcode === this.current_shortcode.code) {
                elements.active_shortcode.find('.render-modal-shortcode-toolbar-button-restore').removeClass('disabled');
            } else {
                elements.active_shortcode.find('.render-modal-shortcode-toolbar-button-restore').addClass('disabled');
            }

            this.openShortcode();
        },

        /**
         * Opens and closes the toolbar at the top of each shortcode item.
         *
         * @since 1.0.0
         *
         * @param $this The toggle element.
         * @param force Force it open or close.
         */
        shortcodeToolbarTogggle: function ($this, force) {

            force = typeof force !== 'undefined' ? force : false;

            var transition = 300,
                $tools = $this.siblings('.render-modal-shortcode-toolbar-tools');

            if ($this.hasClass('open') || force === 'close') {

                $this.removeClass('open dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');

                $tools.stop().animate({
                    height: 0
                }, transition);
            } else if (!$this.hasClass('open') || force === 'open') {

                $this.addClass('open dashicons-arrow-up-alt2').removeClass('dashicons-arrow-down-alt2');

                $tools.stop().animate({
                    height: '50px'
                }, transition);
            }
        },

        /**
         * Restores a shortcode that was selected from the TinyMCE to its values that were previously set.
         *
         * @since 1.0.0
         */
        restoreShortcode: function () {

            elements.active_shortcode.find('.render-modal-att-row').each(function () {
                $(this).data('attObj').revert();
            });

            this.populateShortcode(this.current_shortcode.atts);
        },


        /**
         * Shows and hides advanced shortcode attributes.
         *
         * @since 1.0.0
         *
         * @param $e The advanced attributes container.
         */
        toggleAdvancedAtts: function ($e) {

            if ($e.hasClass('hidden')) {
                this.showAdvancedAtts($e);
            } else {
                this.hideAdvancedAtts($e);
            }
        },

        /**
         * Shows the advanced attributes.
         *
         * @since 1.0.0
         *
         * @param $e The advanced attributes container.
         */
        showAdvancedAtts: function ($e) {

            $e.removeClass('hidden');
            $e.siblings('.render-modal-advanced-atts').show();
            $e.find('.show-text').hide();
            $e.find('.hide-text').show();
        },

        /**
         * Hides the advanced attributes.
         *
         * @since 1.0.0
         *
         * @param $e The advanced attributes container.
         */
        hideAdvancedAtts: function ($e) {

            $e.addClass('hidden');
            $e.siblings('.render-modal-advanced-atts').hide();
            $e.find('.hide-text').hide();
            $e.find('.show-text').show();
        },

        /**
         * Prevents window scrolling when inside something that scrolls.
         *
         * @since 1.0.0
         */
        preventWindowScroll: function () {

            elements.list.bind('mousewheel', function (e) {

                $(this).scrollTop($(this).scrollTop() - e.originalEvent.wheelDeltaY);
                return false;
            });
        },

        /**
         * Filters viewable shortcodes by a category.
         *
         * @since 1.0.0
         *
         * @param $e The category element.
         */
        filterByCategory: function ($e) {

            var category = $e.data('category'),
                shortcodes = elements.list.find('li');

            // Set all other categories to inactive, and this one to active
            elements.categories.find('li').removeClass('active');
            $e.addClass('active');

            // Clear previorendery activated and opened items and clear forms
            this.refresh();
            this.closeShortcode();
            elements.active_shortcode = false;

            if (category === 'all') {
                shortcodes.show();
            } else {
                shortcodes.each(function () {
                    if (category !== $(this).data('category')) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }

            this.refreshRows();
        },

        /**
         * Refreshes alternation of rows.
         *
         * @since 1.0.0
         */
        refreshRows: function () {

            var i = 0;
            elements.list.find('> li').each(function () {

                if ($(this).css('display') === 'none') {
                    return true;
                }

                if (i % 2) {
                    $(this).addClass('alt');
                } else {
                    $(this).removeClass('alt');
                }
                i++;
            })
        },

        /**
         * Sets the Modal shortcode list height.
         *
         * @since 1.0.0
         */
        listHeight: function () {

            var height = elements.wrap.innerHeight()
                - elements.title.outerHeight(true)
                - elements.search.outerHeight(true)
                - elements.categories.outerHeight(true)
                - elements.wrap.find('.dashicons-leftright').outerHeight(true)
                - elements.footer.outerHeight(true);

            elements.list.height(height);
        },

        /**
         * Shows or hides the "Remove Shortcode" button.
         *
         * @since 1.0.0
         *
         * @param which Hide or show.
         */
        removeButton: function (which) {

            which = which.toLowerCase();

            if (which == 'show') {
                elements.remove.show();
            } else {
                elements.remove.hide();
            }
        },

        /**
         * Toggles what the submit button is showing.
         *
         * @since 1.0.0
         *
         * @param which Which display to show.
         */
        submitButton: function (which) {

            var _which = which.toLowerCase();

            function transition($button) {

                var orig_width, width,
                    $buttons = elements.submit.find('[class^="render-modal-submit-text"]'),
                    offset = $button.height() * $button.index('[class^="render-modal-submit-text"]') * -1;

                orig_width = elements.submit.width();
                elements.submit.width('auto');
                width = $button.width();
                elements.submit.width(orig_width);

                elements.submit.animate({
                    width: width
                }, 400);


                if (offset != parseInt($button.css('top'))) {
                    $buttons.addClass('blur').animate({
                        top: offset
                    }, {
                        duration: 200,
                        complete: function () {
                            $(this).removeClass('blur');
                        }
                    });
                }
            }

            switch (_which) {
                case 'add':
                    elements.submit.removeClass('disabled');
                    transition(elements.submit.find('.render-modal-submit-text-add'));

                    break;
                case 'modify':
                    elements.submit.removeClass('disabled');
                    transition(elements.submit.find('.render-modal-submit-text-modify'));

                    break;
                case 'change':
                    elements.submit.removeClass('disabled');
                    transition(elements.submit.find('.render-modal-submit-text-change'));

                    break;
                case 'disable':
                    elements.submit.addClass('disabled');
                    break;
                default:
                    throw new Error('Render: submitButton() has no button type "' + which + '"');
            }
        },

        /**
         * Opens the Modal with a specific shortcode from TinyMCE to edit.
         *
         * @since 1.0.0
         *
         * @param shortcode The literal shortcode text.
         */
        modify: function (shortcode) {

            // Crop off any whitespace (generally preceding)
            shortcode = shortcode.trim();

            // Get our shortcode regex (localized)
            var shortcode_regex = Render_Data.shortcode_regex;

            // Make it compatible with JS (originally in PHP)
            shortcode_regex = shortcode_regex.replace(/\*\+/g, '*');

            // Turn it into executable regex and use it on our code
            var matches = new RegExp(shortcode_regex).exec(shortcode),
                code = matches[2],
                _atts = matches[3], atts = {},
                content = matches[5];

            // Get our att pairs
            var attRegEx = /(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/g,
                match;

            while (match = attRegEx.exec(_atts)) {

                var name = match[3],
                    value = match[4],
                    shortcode_att = render_data[code]['atts'][name];

                // Skip if not an attribute of the shortcode
                if (typeof shortcode_att == 'undefined') {
                    continue;
                }

                // Un-escape from being an attr value
                if (typeof value != 'undefined' && value.length) {
                    value = unescape_sc_attr(value);
                }

                atts[name] = value;
            }

            // Add on the content if there's a content attribute
            if (content) {
                atts.content = HTMLtoTextarea(content);
            }

            // Deal with nesting shortcodes
            var nested;
            if (typeof render_data[code]['render'] !== 'undefined') {
                nested = typeof render_data[code]['render']['nested'] !== 'undefined';
            } else {
                nested = false;
            }

            if (nested && content) {

                var shortcodeRegEx = new RegExp(shortcode_regex, 'g'),
                    child_code = render_data[code].render.nested.child;

                // Make sure it's set
                if (typeof atts.nested_children == 'undefined') {
                    atts.nested_children = {};
                } else {
                    atts.nested_children = JSON.parse(atts.nested_children);
                }

                // Cycle through nested children
                while (match = shortcodeRegEx.exec(content)) {

                    if (match[2] == child_code) {

                        if (typeof atts.nested_children['content'] != 'undefined') {
                            // Att already set, append new value
                            atts.nested_children['content'] += '::sep::' + match[5];
                        } else {
                            atts.nested_children['content'] = match[5];
                        }
                    }
                }

                // Add the nested children att back in (now with the content!)
                atts.nested_children = JSON.stringify(atts.nested_children);
            }

            this.modifying = true;

            this.setActiveShortcode(code);

            elements.active_shortcode.addClass('current-shortcode');

            // Disable all other shortcodes (you should NOT be able to switch from this one)
            if (nested) {
                elements.list.find('.render-modal-shortcode').each(function () {

                    if ($(this).hasClass('current-shortcode')) {
                        return true; // continue $.each
                    }

                    Render_Modal.disableShortcode($(this), l18n.cannot_change_from_shortcode);
                });
            }

            this.current_shortcode = {
                all: shortcode,
                code: code,
                atts: atts
            };

            this.open();

            this.activateShortcode(elements.active_shortcode);

            this.populateShortcode(atts);
        },

        /**
         * Sets the active shortcode in the Modal.
         *
         * @since 1.0.0
         *
         * @param shortcode Which code.
         */
        setActiveShortcode: function (shortcode) {

            // Find our current shortcode
            elements.active_shortcode = elements.list.find('li[data-code="' + shortcode + '"]');
        },

        /**
         * Disables the shortcode from being used.
         *
         * @since 1.1-beta-1
         *
         * @param {jQuery} $shortcode The shortcode element to disable.
         * @param {string} new_message The message to display when clicked.
         */
        disableShortcode: function ($shortcode, new_message) {

            var message = {
                originalText: $shortcode.find('.render-modal-shortcode-description').html().trim(),
                newText: new_message
            };

            $shortcode.addClass('render-modal-shortcode-disabled').data('renderDisabledShortcodeMessages', message);
        },

        /**
         * Enables the shortcode.
         *
         * @since 1.1-beta-1
         *
         * @param {jQuery} $shortcode The shortcode element to enable.
         */
        enableShortcode: function ($shortcode) {

            // No need if shortcode isn't disabled
            if (!$shortcode.hasClass('render-modal-shortcode-disabled')) {
                return;
            }

            this.toggleDisabledText($shortcode, false);
            $shortcode.removeClass('render-modal-shortcode-disabled');
        },

        /**
         * Shows the disabled message.
         *
         * @since 1.1-beta-1
         *
         * @param {jQuery} $shortcode The shortcode element to show the disabled message.
         * @param {bool} show Whether to show or hide the message.
         */
        toggleDisabledText: function ($shortcode, show) {

            var message = $shortcode.data('renderDisabledShortcodeMessages');

            if (show === true) {
                highlight($shortcode);
                $shortcode.addClass('render-modal-shortcode-error-message')
                    .find('.render-modal-shortcode-description').html(message.newText);
            } else {
                $shortcode.removeClass('render-modal-shortcode-error-message')
                    .find('.render-modal-shortcode-description').html(message.originalText);
            }
        },

        /**
         * Populates the shortcode item with attributes.
         *
         * @since 1.0.0
         *
         * @param atts The attributes to use.
         */
        populateShortcode: function (atts) {

            $.each(atts, function (name, value) {

                var attObj = elements.active_shortcode.find('.render-modal-att-row[data-att-name="' + name + '"]').data('attObj');

                // If there is an attObj, set the value
                if (typeof attObj != 'undefined') {
                    attObj._setValue(value);
                }

                // If this IS dynamically populated then also give it some data for after the AJAX population call is
                // completed
                if (attObj && attObj.$container.hasClass('render-modal-att-conditional-populate')) {
                    attObj.$input.data('renderPopulateValue', value);
                }
            });
        },

        /**
         * Closes a shortcode item.
         *
         * @since 1.0.0
         */
        closeShortcode: function () {

            if (elements.active_shortcode) {

                elements.active_shortcode.removeClass('active');

                elements.active_shortcode.find('.accordion-section-content').slideUp(slide_transition);

                Render_Modal.hideAdvancedAtts(elements.active_shortcode.find('.render-modal-show-advanced-atts'));

                if (!elements.active_shortcode.hasClass('current-shortcode') ||
                    (elements.active_shortcode.hasClass('current-shortcode') && !render_modal_open)
                ) {
                    Render_Modal.refresh();
                }

                this.shortcodeToolbarTogggle(elements.active_shortcode.find('.render-modal-shortcode-toolbar-toggle'), 'close');

                elements.last_active_shortcode = elements.active_shortcode;
                elements.active_shortcode = false;

                this.submitButton('disable');
            }
        },

        /**
         * Opens a shortcode item.
         *
         * @since 1.0.0
         */
        openShortcode: function () {

            if (elements.active_shortcode) {

                // Activate it
                elements.active_shortcode.addClass('active');

                // Open it if it's an accordion
                if (elements.active_shortcode.hasClass('accordion-section')) {
                    elements.active_shortcode.find('.accordion-section-content').slideDown(slide_transition);
                }

                // Init the atts (needs to be after the accordion opening to render Chosen properly)
                if (!elements.active_shortcode.data('attsInit')) {
                    this.initAtts();
                    elements.active_shortcode.data('attsInit', true);
                }

                // Trigger change on inputs for the sake of conditional fields on initial shortcode open
                elements.active_shortcode.find('.render-modal-att-row').each(function () {

                    var attObj = $(this).data('attObj');

                    // Oops
                    if (typeof attObj == 'undefined') {
                        return true; // continue $.each
                    }

                    attObj.$input.change();
                });

                // Scroll it into view
                var shortcode_offset = elements.active_shortcode.position(),
                    scrollTop = elements.list.scrollTop(),
                    offset = shortcode_offset.top + scrollTop;

                // If the last activated shortcode was an accordion AND that element was above this, we need to
                // compensate the scroll for it
                if (elements.last_active_shortcode &&
                    elements.active_shortcode.position().top > elements.last_active_shortcode.position().top &&
                    elements.last_active_shortcode.hasClass('accordion-section')
                ) {
                    offset = offset - elements.last_active_shortcode.find('.accordion-section-content').outerHeight();
                }

                elements.list.stop().animate({
                    scrollTop: offset
                });
            }
        },

        /**
         * Opens the Modal.
         *
         * @since 1.0.0
         */
        open: function () {

            render_modal_open = true;

            this.refreshRows();

            elements.wrap.show();
            elements.backdrop.show();

            elements.search.find('input[name="render-modal-search"]').focus();

            this.listHeight();

            // Buttons
            if (this.modifying) {
                this.submitButton('modify');
                this.removeButton('show');
            } else {
                this.submitButton('add');
                this.submitButton('disable');
            }

            $(document).trigger('render-modal-open');
        },

        /**
         * Closes the Modal.
         *
         * @since 1.0.0
         */
        close: function () {

            render_modal_open = false;

            this.output = false;

            elements.list.scrollTop(0);
            elements.wrap.hide();
            elements.backdrop.hide();

            this.closeShortcode();
            this.clearSearch();

            // Remove all errors
            elements.list.find('.render-modal-shortcode').each(function () {
                Render_Modal.enableShortcode($(this));
            });

            // Refresh categories at top
            elements.categories.find('.active').removeClass('active');
            elements.categories.find('li').first().addClass('active');
            elements.categories.find('> ul').css('left', 0);

            // Reset buttons
            elements.remove.hide();

            // Refresh and remove the current-shortcode class
            var $current_shortcode = elements.list.find('.current-shortcode');
            if ($current_shortcode.length) {
                this.refresh($current_shortcode);
                $current_shortcode.removeClass('current-shortcode');
            }

            this.modifying = false;
            this.current_shortcode = false;
            this.active_shortcode = '';
            this.selection = '';

            elements.last_active_shortcode = false;

            $(document).trigger('render-modal-close');
        },

        /**
         * Submits the Modal.
         *
         * @since 1.0.0
         */
        update: function () {

            if (!elements.active_shortcode || !this.validate() || elements.submit.hasClass('disabled')) {
                return;
            }

            this.sanitize();

            var code = elements.active_shortcode.data('code'),
                title = elements.active_shortcode.data('title'),
                props = render_data[code],
                atts = {},
                att_output = '',
                content = '',
                selection = this.selection,
                output, nested, i, global_code, global_val, globalAtts = {};

            // Nesting
            if (typeof render_data[code]['render'] !== 'undefined') {
                nested = typeof render_data[code]['render']['nested'] !== 'undefined';
            } else {
                nested = false;
            }

            // Get the atts
            elements.active_shortcode.find('.render-modal-att-row').each(function () {

                var attObj = $(this).data('attObj');

                // Skip if no attObj or if in a repeater
                if (!attObj || $(this).closest('.render-modal-repeater-field').length) {
                    return true; // Continue $.each
                }

                atts[attObj.name] = attObj._getValue();

                // Don't use if default value
                if (atts[attObj.name] == attObj.default_value) {
                    atts[attObj.name] = false;
                }
            });

            // Global nesting atts
            if (nested && typeof render_data[code]['render']['nested']['globalAtts'] != 'undefined') {

                for (i = 0; i < render_data[code]['render']['nested']['globalAtts'].length; i++) {

                    global_code = render_data[code]['render']['nested']['globalAtts'][i];
                    global_val = atts[global_code];

                    if (global_val) {
                        globalAtts[global_code] = global_val;
                    }
                }
            }

            // Get the content
            if (props['wrapping']) {

                if (nested) {

                    var nested_children = JSON.parse(atts.nested_children),
                        fields = parseRepeaterField(nested_children),
                        children = '',
                        count = 0,
                        child_code = render_data[code].render.nested.child; // From rendering data

                    // Get the count
                    $.each(nested_children, function (name, value) {
                        var new_count = value.split('::sep::').length;
                        count = new_count > count ? new_count : count;
                    });

                    if (typeof atts.nested_children_count == 'undefined') {
                        atts.nested_children_count = {};
                    }
                    atts.nested_children_count = count;

                    for (i = 0; i < count; i++) {

                        // Get the attributes
                        var attributes = '',
                            child_content = typeof fields[i].content != 'undefined' && fields[i].content != '' ? fields[i].content : '';

                        // Add any globalAtts
                        if (globalAtts !== {}) {
                            $.each(globalAtts, function (name, value) {
                                fields[i][name] = value;
                            });
                        }

                        $.each(fields[i], function (name, value) {

                            if (name == 'content') {
                                return true; // continue $.each
                            }

                            attributes += ' ' + name + '="' + value + '"';
                        });

                        // Construct the nested children
                        children += '[' + child_code + attributes + ']' + child_content + '[/' + child_code + ']';
                    }

                    content = children;

                    // Remove content from the nested children so it doesn't get used as an attribute
                    delete nested_children.content;
                    atts.nested_children = JSON.stringify(nested_children);

                    // Delete this attribute if it's empty
                    if (atts.nested_children == '{}') {
                        delete atts.nested_children;
                    }

                } else {

                    if (typeof atts.content !== 'undefined') {
                        content = atts.content;
                    } else {
                        content = selection;
                    }
                }

                content += '[/' + code + ']';
            }

            // Add on atts if they exist
            if (atts) {
                $.each(atts, function (name, value) {

                    // Make sure value is proper format
                    value = typeof value == 'undefined' || value === null ? '' : value;

                    // Skip if no value
                    if (!value) {
                        return true; // continue $.each
                    }

                    // Make sure the value is always text
                    value = value.toString();

                    // Escape for use as attr value
                    value = escape_sc_attr(value);

                    // Add the att to the shortcode output
                    if (value.length) {
                        att_output += ' ' + name + "='" + value + "'";
                    }
                });
            }

            // Construct the output
            output = '[' + code + att_output + ']' + content;

            this.output = {
                all: output,
                code: code,
                atts: atts,
                title: title,
                nested: nested
            };

            $(document).trigger('render-modal-update');

            this.close();
        },

        /**
         * Validates all attributes.
         *
         * @since 1.0.0
         *
         * @returns false If not validated.
         */
        validate: function () {

            var validated = true;

            elements.active_shortcode.find('.render-modal-att-row').each(function () {

                var attObj = $(this).data('attObj');

                // Skip if no attObj or if field is not visible
                if (!attObj || !attObj.$container.is(':visible')) {
                    return true; // Continue $.each
                }

                var required = attObj.$container.data('required'),
                    do_validate = attObj.$container.data('validate'),
                    att_value = attObj._getValue(),
                    att_valid = true;

                // Basic required and field being empty and field not matching default
                if (required && !att_value) {
                    att_valid = false;
                    validated = false;
                    attObj.setInvalid(l18n.this_field_required);
                    return true; // continue $.each iteration
                } else if (!att_value) {
                    return true; //continue $.each iteration
                }

                // If there's validation, let's do it
                if (do_validate) {

                    var validations = render_data[attObj.shortcode]['atts'][attObj.name]['validate'];

                    $.each(validations, function (type, value) {

                        var regExp,
                            url_pattern = '[(http(s)?):\\/\\/(www\\.)?a-zA-Z0-9@:%._\\+~#=]{2,256}\\.[a-z]{2,6}\\b' +
                                '([-a-zA-Z0-9@:%_\\+.~#?&//=]*)',
                            email_pattern = '\\b[\\w\\.-]+@[\\w\\.-]+\\.\\w{2,4}\\b',
                            match, i;


                        // Validate for many different types
                        switch (type) {

                            // Url validation
                            case 'URL':
                                regExp = new RegExp(url_pattern, 'ig');

                                if (!att_value.match(regExp)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid(l18n.enter_valid_url);
                                }
                                break;

                            // Email validation
                            case 'EMAIL':

                                regExp = new RegExp(email_pattern, 'ig');

                                if (!att_value.match(regExp)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid(l18n.enter_valid_email);
                                }
                                break;

                            // Specific characters only
                            case 'CONTAINS ONLY':

                                // Prepare regex string
                                value = value.split('');
                                for (i = 0; i < value.length; i++) {
                                    value[i] = esc_regex_string(value[i]) + (i !== value.length - 1 ? '|' : '');
                                }
                                value = value.join('');

                                regExp = new RegExp(value, 'g');
                                match = att_value.match(regExp);

                                if (match === null || att_value.length !== match.length) {

                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid(l18n.invalid_chars);
                                }

                                break;

                            // Maximum character count
                            case 'MAX CHAR':

                                if (att_value.length > parseInt(value)) {

                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid((att_value.length - parseInt(value)) + ' ' + l18n.too_many_chars);
                                }
                                break;

                            // Minimum character count
                            case 'MIN CHAR':

                                if (att_value.length < parseInt(value)) {

                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid((parseInt(value)) - att_value.length + ' ' + l18n.too_few_chars);
                                }
                                break;

                            // No numbers allowed
                            case 'CHAR ONLY':

                                if (att_value.match(/[0-9]/)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid(l18n.no_numbers);
                                }
                                break;

                            // Only numbers allowed
                            case 'INT ONLY':

                                var numbers = att_value.match(/[0-9]+/);

                                if (!numbers || (numbers[0] !== numbers.input)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid(l18n.only_numbers);
                                }
                                break;

                            // If no matches, throw error
                            default:
                                throw new Error('Render: Unsupported validation method "' + type + '" for the shortcode "' + attObj.shortcode + '" at field "' + attObj.fieldname + '"');
                        }
                    });
                }

                if (att_valid) {
                    attObj.setValid();
                }
            });

            return validated;
        },

        /**
         * Sanitizes all attributes.
         *
         * @since 1.0.0
         */
        sanitize: function () {

            elements.active_shortcode.find('.render-modal-att-row').each(function () {

                var attObj = $(this).data('attObj');

                // Skip if no attObj
                if (!attObj) {
                    return true; // Continue $.each
                }

                var do_sanitize = attObj.$container.data('sanitize'),
                    att_value = attObj._getValue();

                if (do_sanitize && att_value !== null && att_value.length) {

                    var sanitations = render_data[attObj.shortcode]['atts'][attObj.name]['sanitize'];

                    $.each(sanitations, function (type, value) {

                        switch (type) {

                            case 'URL':
                                if (!att_value.match(/https?:\/\//)) {
                                    att_value = 'http://' + att_value;
                                }
                                break;

                            case 'INT ONLY':

                                att_value = att_value.replace(/[^0-9]/g, '');
                                break;

                            // If no matches, throw an error
                            default:
                                throw new Error('Render -> Unsupported sanitation method "' + type + '" for the shortcode "' + attObj.shortcode + '" at field "' + attObj.fieldname + '"');

                        }

                        attObj._setValue(att_value);
                    });
                }
            });
        },

        /**
         * Refreshes the shortcode to original attribute values.
         *
         * @since 1.0.0
         *
         * @param shortcode Which shortcode to refresh.
         */
        refresh: function (shortcode) {

            shortcode = shortcode || elements.active_shortcode;

            if (shortcode.length) {

                shortcode.find('.render-modal-att-row').each(function () {

                    var attObj = $(this).data('attObj');

                    if (typeof attObj !== 'undefined') {
                        attObj._revert();
                    }
                });
            }
        },

        /**
         * Converts a string to JSON object.
         *
         * @param string
         * @returns {*}
         * @private
         */
        _stringToObject: function (string) {

            if (typeof string === 'undefined' || !string.length) {
                return false;
            }
            string = '"' + string.replace(/(:|,)/g, '"' + "$1" + '"') + '"';
            string = JSON.parse('{' + string + '}');
            return string;
        }
    };

    /**
     * Object for shortcode attributes.
     *
     * This is the base object for every attribute field type. Each attribute row is assigned its own version of the
     * AttAPI, and the AttAPI manages that attribute (things like getting the value, setting the value, reverting,
     * etc.
     *
     * @since 1.0.0
     *
     * @constructor
     */
    function AttAPI() {

        /**
         * The name (title) of the attribute.
         *
         * @since 1.0.0
         *
         * @type {string}
         */
        this.name = '';

        /**
         * The original value (on load) of the attribute field (used in reverting).
         *
         * @since 1.0.0
         *
         * @type {*}
         */
        this.original_value = null;

        /**
         * The default value of the attribute.
         *
         * @since 1.0.0
         *
         * @type {*}
         */
        this.default_value = null;

        /**
         * The name tag of the attribute field.
         *
         * @since 1.0.0
         *
         * @type {null}
         */
        this.fieldname = '';

        /**
         * The code itself ([example_code]).
         *
         * @since 1.0.0
         *
         * @type {string}
         */
        this.shortcode = '';

        /**
         * The attribute row container.
         *
         * @since 1.0.0
         *
         * @type {HTMLElement}
         */
        this.$container = null;

        /**
         * The attribute input field.
         *
         * @since 1.0.0
         *
         * @type {HTMLElement}
         */
        this.$input = null;

        /**
         * Attribute fiend conditionals.
         *
         * @since 1.1-beta-1
         *
         * @type {object|bool}
         */
        this.conditionals = false;

        /**
         * Whether or not this attribute is hidden (used for conditionals).
         *
         * @since 1.1-beta-1
         *
         * @type {boolean}
         */
        this.hidden = false;

        /**
         * Initializes the object.
         *
         * @since 1.0.0
         *
         * @param {{jQuery}} $e The attribute row container.
         */
        this.init = function ($e) {

            // Setup properties
            this.$container = $e;
            this.$input = this.$container.find('.render-modal-att-input');
            this.name = $e.data('att-name');
            this.fieldname = this.$container.find('.render-modal-att-name').text().trim();
            this.shortcode = this.$container.closest('.render-modal-shortcode').data('code');

            // Default value
            this.default_value = this.$input.data('default');
            this.default_value = typeof this.default_value != 'undefined' ? this.default_value.toString() : false;

            // Conditionals
            var data = render_data[this.shortcode]['atts'][this.name];
            this.conditionals = typeof data != 'undefined' ? data['conditional'] : false;
            this.conditionals = this.conditionals || false;

            this.storeOriginalValue();

            this.postInit($e);

            // Bind conditionals' initialization to the parent shortcode being finished initializing
            if (this.conditionals !== false) {
                $e.closest('.render-modal-shortcode').on('render-modal-shortcode-init', this._setupConditionals());
            }
        };

        /**
         * Stores the attribute's initial load value (for reverting later).
         *
         * @since 1.0.0
         */
        this.storeOriginalValue = function () {
            this.original_value = this.$input.val();
        };

        /**
         * Sets up the attribute's conditionals.
         *
         * @since 1.1-beta-1
         *
         * @private
         */
        this._setupConditionals = function () {

            // Create initial boundAtts array for tracking bound atts
            if (!('boundAtts' in this.conditionals)) {
                this.conditionals.boundAtts = [];
            }

            var _this = this;
            $.each(this.conditionals, function (type, conditional) {

                // Only these 2 types are currently supported
                if (type !== 'visibility' && type !== 'populate') {
                    return true; // continue $.each
                }

                $.each(conditional.atts, function (att_ID, att) {

                    // Get the attribute this conditional is dependent on
                    att.boundAtt = _this.$container.closest('.render-modal-shortcode-atts')
                        .find('.render-modal-att-row[data-att-name="' + att_ID + '"]').data('attObj');

                    // Something went wrong...
                    if (typeof att.boundAtt == 'undefined' || typeof att.boundAtt.$input == 'undefined') {
                        return true; // continue $.each
                    }

                    // Only attach handler once
                    if (_this.conditionals.boundAtts.indexOf(att_ID) === -1) {

                        // Add the att ID to the list to make sure it only gets bound once
                        _this.conditionals.boundAtts.push(att_ID);

                        // Bind the handler to the attribute changing
                        att.boundAtt.$input.change(function () {
                            _this.performConditionals();
                        });
                    }
                });
            });
        };

        /**
         * Performs conditional checks and actions.
         *
         * @since 1.1-beta-1
         */
        this.performConditionals = function () {

            var _this = this;
            $.each(this.conditionals, function (type, conditional) {

                // Only these 2 types are currently supported
                if (type !== 'visibility' && type !== 'populate') {
                    return true; // continue $.each
                }

                $.each(conditional.atts, function (att_ID, att) {

                    switch (type) {

                        case 'visibility':

                            if (compare_conditions(conditional.atts)) {

                                _this.hidden = false;
                                _this.$container.show('drop', {}, 300);

                            } else {

                                _this._revert();

                                // If this was visible, then run again now that the values have been changed because other
                                // dependent attributes could need to be hidden or shown now.
                                if (_this.hidden !== true) {
                                    _this.hidden = true;
                                    _this.$container.hide('drop', {}, 300);
                                    //_this.performConditionals();
                                }
                            }

                            break;

                        case 'populate':

                            var data = {
                                    action: 'render_conditional_att_populate',
                                    atts: {},
                                    callback: conditional.callback
                                },
                                cover_html = '<div class="render-att-populate-cover" style="display: none;">' +
                                    '<span class="spinner"></span>' +
                                    '</div>',
                                $cover;

                            $.each(conditional.atts, function (att_ID, att) {
                                data.atts[att_ID] = att.boundAtt._getValue();
                            });

                            // Setup last populate to keep track and not do redundant repeats
                            if (typeof att.lastPopulate == 'undefined') {
                                att.lastPopulate = '';
                            }

                            // Skip if same as last call
                            if (att.lastPopulate == JSON.stringify(data)) {
                                break;
                            }
                            att.lastPopulate = JSON.stringify(data);

                            // Place the cover
                            _this.$container.find('.render-modal-att-field').append(cover_html);
                            $cover = _this.$container.find('.render-att-populate-cover');
                            $cover.fadeIn(300);

                            if (att['populating'] === true) {

                                if (typeof att['populationQueue'] == 'undefined') {
                                    att['populationQueue'] = [];
                                }

                                att['populationQueue'].push(data);
                            }

                            call_ajax(data);

                            /**
                             * Calls the populating AJAX.
                             *
                             * @since {{VERSION}}
                             *
                             * @param data The att data to send off.
                             */
                            function call_ajax(data) {

                                att['populating'] = true;

                                $.ajax({
                                    type: 'POST',
                                    url: ajaxurl,
                                    data: data,
                                    success:function (response) {

                                        // Set our new options!
                                        if (response !== false && 'rebuildOptions' in _this) {
                                            _this.rebuildOptions(response);
                                            _this.$input.change();
                                        }

                                        // Set the value (if was set from populateShortcode())
                                        var value = _this.$input.data('renderPopulateValue');
                                        if (typeof value != 'undefined') {
                                            _this._setValue(value);
                                            _this.$input.data('renderPopulateValue', null);
                                        }

                                        $cover.fadeOut(300, function () {
                                            $(this).remove();
                                        });

                                        // If more in line, do them (this mimics synchronous calls)
                                        if (att['populationQueue']) {
                                            call_ajax(data[0]);
                                            delete data[0];
                                        } else {
                                            att['populating'] = false;
                                        }
                                    }
                                });
                            }

                            break;
                    }
                });
            });

            /**
             * Compares all existing conditions and shows or hides the attribute row.
             *
             * @since 1.1-beta-1
             *
             * @param {object} atts The conditional properties.
             */
            function compare_conditions(atts) {

                var show = false;

                $.each(atts, function (att_ID, att) {

                    var passing = false,
                        value = att.boundAtt.getValue(),
                        operator_table = {
                            '==': function (a, b) {
                                return a == b;
                            },
                            '===': function (a, b) {
                                return a === b;
                            },
                            '!=': function (a, b) {
                                return a != b;
                            },
                            '!==': function (a, b) {
                                return a !== b;
                            },
                            '>': function (a, b) {
                                return a > b;
                            },
                            '>=': function (a, b) {
                                return a >= b;
                            },
                            '<': function (a, b) {
                                return a < b;
                            },
                            '<=': function (a, b) {
                                return a <= b;
                            }
                        };

                    // Decide if this attribute is passing based on which type of conditional we're using
                    switch (att.type) {

                        case '==':
                        case '===':
                        case '!=':
                        case '!==':

                            if (operator_table[att.type](value, att.value)) {
                                passing = true;
                            }

                            break;

                        case '>':
                        case '>=':
                        case '<':
                        case '<=':

                            if (operator_table[att.type](parseFloat(value), parseFloat(att.value))) {
                                passing = true;
                            }

                            break;

                        case 'BETWEEN':

                            if (parseFloat(value) > parseFloat(att.value.split(',')[0]) &&
                                parseFloat(value) < parseFloat(att.value.split(',')[1])
                            ) {
                                passing = true;
                            }

                            break;

                        case 'NOT BETWEEN':

                            if (parseFloat(value) <= parseFloat(att.value.split(',')[0]) ||
                                parseFloat(value) >= parseFloat(att.value.split(',')[1])
                            ) {
                                passing = true;
                            }

                            break;

                        case 'CONTAINS':

                            if (value.indexOf(att.value) !== -1) {
                                passing = true;
                            }

                            break;

                        case 'EXCLUDES':

                            if (value.indexOf(att.value) === -1) {
                                passing = true;
                            }

                            break;

                        case 'IN':

                            if (att.value.split(',').indexOf(value) !== -1) {
                                passing = true;
                            }

                            break;

                        case 'NOT IN':

                            if (att.value.split(',').indexOf(value) === -1) {
                                passing = true;
                            }

                            break;

                        case 'NOT EMPTY':

                            if (value.toString().length) {
                                passing = true;
                            }

                            break;
                    }

                    // If we're passing, then we can show the attribute, UNLESS this condition is 'AND' and is not
                    // passing; then we hide no matter what.
                    if (passing) {
                        show = true;
                    } else if (att.relation == 'AND') {
                        show = false;
                        return false; // break $.each
                    }
                });

                return show;
            }
        };

        /**
         * Reverts the attribute to its original values.
         *
         * This will always run and cannot be changed or bypass. The attribute can have its own revert method though
         * that will run first in this method.
         *
         * @since 1.0.0
         * @private
         */
        this._revert = function () {

            this.revert();
            this.setValid();
            this.$input.prop('disabled', false);
            this.$input.trigger('render:att_revert');
        };

        /**
         * Reverts the attribute to its original values (can be overridden).
         *
         * @since 1.0.0
         */
        this.revert = function () {
            this._setValue(this.original_value);
        };

        /**
         * Fires the trigger and launches the getValue function.
         *
         * @since 1.0.0
         */
        this._getValue = function () {

            var value = this.getValue();
            this.$input.trigger('render:att_getValue', value);
            return value;
        };

        /**
         * Gets the attribute field current value (can be overridden).
         *
         * @since 1.0.0
         *
         * @returns {*} The input value.
         */
        this.getValue = function () {
            return this.$input.val();
        };

        /**
         * Fires the trigger and launches the setValue function.
         *
         * @since 1.0.0
         *
         *  @param {*} value The value to set to.
         */
        this._setValue = function (value) {
            this.setValue(value);
            this.$input.change();
            this.$input.trigger('render:att_setValue', value);
        };

        /**
         * Sets the attribute field to a specified value (can be overridden).
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {
            this.$input.val(value);
        };

        /**
         * Sets the attribute row to invalid (can be overridden).
         *
         * Displays a message and does not allow submitting the Modal.
         *
         * @since 1.0.0
         *
         * @param {string} msg The validation message to show the user.
         */
        this.setInvalid = function (msg) {

            this.$container.addClass('invalid');
            this.errorMsg(msg);
            highlight(this.$input);
            this.$input.trigger('render:att_setInvalid', msg);
        };

        /**
         * Sets the attribute from invalid to valid (can be overridden).
         *
         * @since 1.0.0
         */
        this.setValid = function () {
            this.$container.removeClass('invalid');
            this.$input.trigger('render:att_setValid');
        };

        /**
         * Rebuilds the available option(s).
         *
         * @since 1.1-beta-1
         *
         * @param {object} response The AJAX response.
         */
        this.rebuildOptions = function (response) {
            this.$input.val(response.value);
        };

        /**
         * Displays the validation message (can be overridden).
         *
         * @since 1.0.0
         *
         * @param {string} msg The message to show.
         */
        this.errorMsg = function (msg) {

            if (typeof this.$errormsg === 'undefined') {
                this.$errormsg = this.$container.find('.render-modal-att-errormsg');
            }

            this.$errormsg.html(msg);
        };

        /**
         * Customizable post-init function.
         *
         * @since 1.1-beta-1
         */
        this.postInit = function () {
        };
    }

    /**
     * Modulation of AttAPI for the Hidden attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Hidden = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Textbox attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var TextBox = function ($e) {

        /**
         * The input mask properties.
         *
         * @since 1.1-beta-1
         *
         * @type {boolean|object}
         */
        this.mask = false;

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the textbox
         *
         * @since 1.1-beta-1
         */
        this.postInit = function () {

            // Trigger immediate change
            this.$input.keyup(function () {
                $(this).change();
            });

            // Apply the mask if there is one
            if (typeof this.$input.data('mask') != 'undefined') {
                this.applyMask();
            }
        };

        /**
         * Gets the attribute field current value.
         *
         * This AttAPI applies to both textbox and text area, so we need to account for that.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            // Return without mask
            if (this.mask !== false) {

                var regExp = '', i = 0, mask;

                // Get our definitions to remove
                $.each($.mask.definitions, function (name) {
                    i++;
                    regExp += (i !== 1 ? '|' : '') + esc_regex_string(name);
                });
                regExp = new RegExp(regExp, 'g');

                // Remove the definitions from the mask to get all junk that needs to be removed from the att value
                mask = this.mask.mask.replace(regExp, '').split('').getUnique();

                regExp = '';
                for (i = 0; i < mask.length; i++) {
                    regExp += (i !== 0 ? '|' : '') + esc_regex_string(mask[i]);
                }
                regExp = new RegExp(regExp, 'g');

                // Return the att value minus all of the spacers and such in the placeholder
                return this.$input.val().replace(regExp, '');
            }

            return this.$input.val();
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Causes mask to take effect, if there is one.
         *
         * @since 1.1-beta-1
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            this.$input.val(value);

            // Causes mask to take effect
            if (this.mask !== false) {

                this.$input.focus();

                var $input = this.$input;
                setTimeout(function () {
                    $input.blur();
                }, 50);
            }
        };

        /**
         * Applies a mask to the input field.
         *
         * @since 1.1-beta-1
         */
        this.applyMask = function () {

            this.mask = render_data[this.shortcode]['atts'][this.name]['properties']['mask'];

            var mask, monospace, placeholder, options;

            mask = this.mask.mask || '';
            monospace = 'monospace' in this.mask;
            placeholder = this.mask.placeholder || false;

            // Optional templates
            if ('template' in this.mask) {

                switch (this.mask.template) {

                    case 'phone':

                        mask = '(999) 999-9999';
                        monospace = true;
                        break;

                    case 'date':

                        mask = '99/99/9999';
                        placeholder = 'mm/dd/yyyy';
                        monospace = true;
                        break;
                }

            }

            if (monospace) {
                this.$input.addClass('code');
            }

            options = placeholder === false ? {} : {
                placeholder: placeholder
            };

            this.mask = {
                mask: mask,
                placeholder: placeholder
            };

            this.$input.mask(mask, options);
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the TextArea attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var TextArea = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the textarea.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            // Disable Render Modal actions
            this.$input.keyup(function (e) {

                // Enter, up arrow, down arrow
                if (e.which == 13 || e.which == 38 || e.which == 40) {
                    e.preventDefault();
                    return false;
                }
            });

            /*
             Allow tab to indent instead of going to the next input

             Taken from http://stackoverflow.com/questions/6637341/use-tab-to-indent-in-textarea#answer-6637396
             Much thanks to user "kasdega"!
             */
            $container.delegate('textarea', 'keydown', function (e) {

                var keyCode = e.keyCode || e.which;

                // Tab key
                if (keyCode == 9) {
                    e.preventDefault();
                    var start = $(this).get(0).selectionStart;
                    var end = $(this).get(0).selectionEnd;

                    // set textarea value to: text before caret + tab + text after caret
                    $(this).val($(this).val().substring(0, start)
                    + "    "
                    + $(this).val().substring(end));

                    // put caret at right position again
                    $(this).get(0).selectionStart =
                        $(this).get(0).selectionEnd = start + 4;
                }
            });

            // Shift + enter should submit the form
            this.$input.keydown(function (event) {

                if (event.keyCode == 13 && event.shiftKey) {
                    event.preventDefault();
                    Render_Modal.update();
                }
            });

            // Trigger immediate change
            this.$input.keyup(function () {
                $(this).change();
            });
        };

        /**
         * Gets the attribute field current value.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {
            return this.$input.val();
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Checkbox attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Checkbox = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the checkbox.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            $container.find('.render-modal-att-checkbox').change(function () {

                if ($(this).prop('checked')) {
                    $container.find('.render-modal-att-checkbox-label').addClass('checked');
                } else {
                    $container.find('.render-modal-att-checkbox-label').removeClass('checked');
                }
            });

            $container.find('.render-modal-att-checkbox-label').click(function () {
                var $checkbox_input = $container.find('.render-modal-att-checkbox');
                $checkbox_input.prop('checked', !$checkbox_input.prop('checked')).trigger('change');
            });
        };

        /**
         * Gets the attribute field current value.
         *
         * Getting the value here is just seeing if it's checked.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            if (this.$input.prop('checked')) {
                return this.$input.val();
            } else {
                return '';
            }
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Checks or un-checks the checkbox.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            if (value) {
                this.$input.prop('checked', true);
            } else {
                this.$input.prop('checked', false);
            }
        };

        /**
         * Stores the attribute's initial load value (for reverting later).
         *
         * @since 1.0.0
         */
        this.storeOriginalValue = function () {

            if (this.$input.prop('checked')) {
                this.original_value = this.$input.val();
            } else {
                this.original_value = '';
            }
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Toggle attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Toggle = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Gets the attribute field current value.
         *
         * If checked, use the checkbox input, if not, use the hidden input.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            if (this.$input.prop('checked')) {
                return this.$input.val();
            } else {
                return this.$container.find('input[type="hidden"]').val();
            }
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Checks or un-checks the checkbox.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            if (value === this.$input.val()) {
                this.$input.prop('checked', true);
            } else {
                this.$input.prop('checked', false);
            }
        };

        /**
         * Stores the attribute's initial load value (for reverting later).
         *
         * @since 1.0.0
         */
        this.storeOriginalValue = function () {

            if (this.$input.prop('checked')) {
                this.original_value = this.$input.val();
            } else {
                this.original_value = this.$container.find('input[type="hidden"]').val();
            }
        };

        /**
         * Rebuilds the available options.
         *
         * @since 1.1-beta-1
         *
         * @param {object} response The AJAX response.
         */
        this.rebuildOptions = function (response) {

            var i = 0,
                attObj = this;

            $.each(response.options, function (value, label) {
                i++;

                if (i === 1) {
                    attObj.$container.find('input[type="checkbox"]').val(value);
                    attObj.$container.find('.render-modal-att-toggle-first').html(label);
                } else {
                    attObj.$container.find('input[type="hidden"]').val(value);
                    attObj.$container.find('.render-modal-att-toggle-second').html(label);
                }
            });
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Selectbox attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Selectbox = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Constructs the Chosen input.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            // Apply Chosen
            var $chosen = $container.find('.render-modal-att-input.chosen'),
                options = {
                    width: '100%',
                    search_contains: true,
                    allow_single_deselect: $chosen.data('deselect')
                },
                allow_icons = $chosen.data('allow-icons'),
                select_all = $chosen.data('select-all');

            // Not using Chosen
            if ($chosen.length === 0) {
                return;
            }

            // Allow icons
            if (allow_icons) {
                options.disable_search = true;
            }

            $chosen.chosen(options);

            // Fix scroll issue
            $container.find('.chosen-results').bind('mousewheel', function (e) {
                $(this).scrollTop($(this).scrollTop() - e.originalEvent.wheelDeltaY);
                return false;
            });

            // Make sure change triggers chosen to update
            this.$input.change(function () {
                $(this).trigger('chosen:updated');
            });

            // Scroll list as needed if chosen drop-down is cut off
            $chosen.on('chosen:showing_dropdown', function () {

                var $drop = $container.find('.chosen-drop'),
                    $list = elements.list,
                    drop_offset = $drop.offset().top + $drop.outerHeight(),
                    list_offset = $list.offset().top + $list.outerHeight(),
                    difference = drop_offset - list_offset;

                // Bottom of chosen drop is out of the list
                if (difference > 0) {
                    $list.animate({
                        scrollTop: $list.scrollTop() + difference
                    }, 150);
                }
            });

            // Extend functionality to allow select / de-select all (only on multi-selects)
            if (select_all && $chosen.attr('multiple')) {

                $chosen.change(function () {

                    var selected_options = $chosen.val(),
                        $chosen_container = $container.find('.chosen-container'),
                        $select_all = $chosen_container.find('.render-chosen-select-all'),
                        $deselect_all = $chosen_container.find('.render-chosen-deselect-all');

                    if (!$select_all.length && $chosen.find('option').length !== $chosen.find('option:selected').length) {

                        $select_all = $('<div class="render-chosen-select-all dashicons dashicons-plus"></div>');
                        $select_all.hide()
                            .mousedown(function (event) {

                                event.stopPropagation();

                                $chosen.find('option')
                                    .prop('selected', true)
                                    .trigger('chosen:updated')
                                    .change();
                            })
                            .appendTo($chosen_container)
                            .show('drop', {direction: 'right'}, 150);

                    } else if (!$deselect_all.length && selected_options) {

                        $deselect_all = $('<div class="render-chosen-deselect-all dashicons dashicons-no"></div>');
                        $deselect_all.hide()
                            .mousedown(function (event) {

                                event.stopPropagation();

                                $chosen.find('option')
                                    .prop('selected', false)
                                    .trigger('chosen:updated')
                                    .change();
                            })
                            .appendTo($chosen_container)
                            .show('drop', {direction: 'right'}, 150);

                    }

                    // Remove the select all when everything's selected
                    if ($chosen.find('option').length === $chosen.find('option:selected').length) {
                        $select_all.hide('drop', {direction: 'right'}, 150, function () {$(this).remove()});
                    }

                    // Remove the deselect when nothing's selected
                    if (!selected_options) {
                        $deselect_all.hide('drop', {direction: 'right'}, 150, function () {$(this).remove()});;
                    }
                });
            }

            // Extend functionality to allow icons
            if (allow_icons) {

                $chosen.on('chosen:showing_dropdown chosen:updated', function () {

                    $(this).find('option').each(function (index) {

                        var icon = $(this).data('icon');

                        if (!icon) {
                            return true; // Continue &.each
                        }

                        if (icon) {
                            $container.find('.chosen-results li').eq(index - 1).prepend(
                                '<span class="' + icon + '"></span>'
                            )
                        }
                    });
                });

                $chosen.on('change', function () {

                    var icon = 'dashicons ' + $chosen.val();

                    if (!$chosen.val()) {
                        $container.find('.chosen-single .dashicons').remove();
                    } else {
                        $container.find('.chosen-single span').prepend(
                            '<span class="' + icon + '"></span>'
                        );
                    }
                });

                // Trigger change when setting the value (for initial Modal opens)
                $chosen.on('render:att_setValue', function () {
                    $chosen.trigger('change');
                });
            }

            // Extend functionality to allow custom text input (if enabled on input)
            if ($chosen.hasClass('allow-custom-input')) {

                var $input_text = $container.find('.chosen-search input[type="text"]');

                // Hide the "no results..."
                $chosen.on('chosen:no_results', function () {
                    $container.find('.no-results').remove();
                });

                // Clear input field and data on change (this should be when the deselect)
                $chosen.change(function () {

                    if ($chosen.data('chosen-custom-input')) {
                        $input_text.val('');
                        $chosen.removeData('chosen-custom-input');
                    }
                });

                // Manually trigger the chosen "closing" because it doesn't always launch after we manually
                // build the single deselect
                $container.on('mousedown', '.search-choice-close', function () {
                    $chosen.trigger('chosen:hiding_dropdown');
                });

                // Use the custom value when hiding the dropdown
                $chosen.on('chosen:hiding_dropdown', function () {

                    var search_text = $input_text.val(),
                        Chosen = $chosen.data('chosen');

                    // If no searching, get outta here
                    if (!search_text) {
                        $chosen.removeData('chosen-custom-input');
                        return;
                    }

                    // Set the preview text to our custom input (and make it not look like the default text)
                    $container.find('.chosen-single').removeClass('chosen-default')
                        .find('> span').html(search_text);

                    // Tell the Modal to use this new custom data
                    $chosen.data('chosen-custom-input', search_text);

                    // Remove focus from input and clear any leftover input
                    $input_text.val('').blur();

                    // Make sure to trigger the input change
                    $chosen.trigger('render-att-change');

                    // Manually add choice deselect and event
                    Chosen.single_deselect_control_build();
                });

                // Populate search text if using custom input
                $chosen.on('chosen:showing_dropdown', function () {

                    var custom_text = $chosen.data('chosen-custom-input');

                    if (custom_text) {
                        $input_text.val(custom_text);
                    }
                });

                // Clear search text on clicking an option and set to that option
                $container.on('click', 'li.active-result', function () {

                    // Clear and de-select the custom input
                    $input_text.val('').blur();

                    // Unfortunately, by this time the custom text has been used, so we have to manually
                    // tell Chosen to use the option we clicked
                    var Chosen = $chosen.data('chosen'),
                        value = Chosen.results_data[$(this).data('option-array-index')].value;

                    $chosen.val(value)
                        .trigger('chosen:updated')
                        .removeData('chosen-custom-input');
                });

                // Pressing enter when typing a custom value shouldn't close the Modal, just use the text
                $input_text.keyup(function (e) {

                    if (e.which == 13) {

                        // Make sure we don't keep chosen focused
                        $input_text.blur();

                        // TODO Figure out why I can't just trigger "chosen:close"...

                        var Chosen = $chosen.data('chosen'),
                            custom_text = $input_text.val();

                        if (custom_text) {
                            Chosen.close_field();
                            $input_text.val(custom_text);
                            $chosen.trigger('chosen:hiding_dropdown');
                        }

                        return false;
                    }
                });
            }
        };

        /**
         * Gets the attribute field current value.
         *
         * Checks for custom input first.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            // Account for custom input
            var custom_text = this.$input.data('chosen-custom-input');

            if (custom_text) {
                return custom_text;
            } else {

                // For multiple, join the values
                if (this.$input.attr('multiple')) {
                    return this.$input.val().filter(function(n){ return n != ''}).join(',');
                }

                return this.$input.val();
            }
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Many variables to account for, including: reset field on empty value, custom input, multi-select.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            // Reset select (feed empty value)
            if (!value) {

                if (this.$input.attr('multiple')) {
                    this.$input.val([]);
                } else {
                    this.$input.val('');
                }
                return;
            }

            // Account for multi-select
            if (this.$input.attr('multiple')) {
                this.$input.val(value.split(','));
            } else {

                // Custom input (value doesn't exist in options)
                if (!this.$input.find('option[value="' + value + '"]').length) {
                    this.$container.find('.chosen-search input[type="text"]').val(value);
                    this.$input.trigger('chosen:hiding_dropdown');
                    return;
                }

                this.$input.val(value);
            }
        };

        /**
         * Reverts the attribute to its original values.
         *
         * Removes the custom value first, then sets to the original value.
         *
         * @since 1.0.0
         */
        this.revert = function () {
            this._setValue(this.original_value);
        };

        /**
         * Sets the attribute row to invalid.
         *
         * Makes sure not to highlight the selectbox, it should remain hidden.
         *
         * @since 1.0.0
         *
         * @param {string} msg The validation message to show the user.
         */
        this.setInvalid = function (msg) {

            this.$container.addClass('invalid');
            this.errorMsg(msg);
        };

        /**
         * Rebuilds the available options.
         *
         * @since 1.1-beta-1
         *
         * @param {object} response The AJAX response.
         */
        this.rebuildOptions = function (response) {

            var $no_options = this.$container.find('.render-modal-selectbox-no-options'),
                $chosen_container = this.$container.find('.render-chosen-container'),
                $description = this.$container.find('.render-modal-att-description');

            // Hide or show
            if (!response.options) {
                $no_options.show();
                $chosen_container.hide();

                // Set the no options text, if it's set
                if (response.no_options_text) {
                    $no_options.html(response.no_options_text);
                }
            } else {
                $no_options.hide();
                $chosen_container.show();
            }

            // Modify the description, if it's set
            if (response.description) {
                $description.html(response.description);
            }

            this.$input.html(response.options);
            this.$input.trigger('chosen:updated');
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the ColorPicker attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Colorpicker = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the colorpicker object.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            var _this = this;

            $container.find('.render-modal-att-colorpicker').first().each(function () {

                var data = $(this).data();

                // Trigger input change
                data.change = function () {
                    _this.$input.trigger('render-att-change');
                };

                $(this).wpColorPicker(data);
            });
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Triggers Iris colorpicker with new color.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            // Bail if not initialized
            if (typeof this.$input.data('wpWpColorPicker') === 'undefined') {

                render_log_error('setValue() called before Iris init on attribute ' + this.name + ' in shortcode ' + this.shortcode);
                return;
            }

            this.$input.iris('color', value);
            this.$input.change();
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Slider attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Slider = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Constructs the jQuery UI Slider object.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            $container.find('.render-modal-att-slider').each(function () {

                var $this = $(this),
                    $input = $this.siblings('.render-modal-att-slider-value'),
                    data = {}, i;

                // Skip if the slider's already been initilaized
                if (typeof $(this).data('uiSlider') !== 'undefined') {
                    return true; // Continue $.each
                }

                // Get the data
                var allowed_data = [
                    'animate',
                    'disabled',
                    'max',
                    'min',
                    'orientation',
                    'step',
                    'value',
                    'values',
                    'range',
                    'slide'
                ];

                for (i = 0; i < allowed_data.length; i++) {

                    var _data = $this.data('' + allowed_data[i]);
                    if (_data) {
                        data[allowed_data[i]] = _data;
                    }
                }

                // If the input had a number, and a default isn't set, use it
                if ($input.val() && !data.value) {
                    if (data.values) {
                        data.values = $input.val();
                    } else {
                        data.value = $input.val();
                    }
                }

                // Custom slide callback
                if (data.slide) {

                    var slide_callback = data.slide;

                    data.slide = function (event, ui) {
                        return window[slide_callback](event, ui, $input);
                    }
                } else {
                    if (data.values) {
                        data.slide = function (event, ui) {

                            // Prevent overlap
                            if (ui.values[0] >= ui.values[1] || ui.values[1] <= ui.values[0]) {
                                return false;
                            }

                            // Output the ranges to the text and the input
                            var $text = $input.siblings('.render-modal-att-slider-range-text');

                            $text.find('.render-modal-att-slider-range-text-value1').html(ui.values[0]);
                            $text.find('.render-modal-att-slider-range-text-value2').html(ui.values[1]);

                            $input.val(ui.values[0] + '-' + ui.values[1]);
                        };
                    } else {
                        data.slide = function (event, ui) {
                            $input.val(ui.value);
                        };
                    }
                }

                // Set the values to an array (if a range slider)
                if (data.values) {
                    data.values = data.values.split('-');
                }

                // Make sure this gets no duplicate handlers
                $input.off();

                // Only numbers (or negative)
                $input.keypress(function (e) {

                    if (!String.fromCharCode(e.which).match(/[0-9|-]/)) {
                        highlight($(this));
                        e.preventDefault();
                    }
                });

                // Change the slider and keep the numbers in the allowed range
                $input.change(function () {

                    var $slider = $(this).siblings('.render-modal-att-slider');

                    if ($slider.data('range')) {

                        // Range slider
                        var $text = $(this).siblings('.render-modal-att-slider-range-text'),
                            values = $(this).val().split('-');

                        $text.find('.render-modal-att-slider-range-text-value1').html(values[0]);
                        $text.find('.render-modal-att-slider-range-text-value2').html(values[1]);

                        $slider.slider('values', values);
                    } else {

                        // Normal slider
                        var min = parseInt($slider.data('min')),
                            max = parseInt($slider.data('max')),
                            val = parseInt($(this).val());

                        // Set the jQuery UI slider to match the new text value
                        $slider.slider('value', $(this).val());

                        // Keep in range
                        if (val < min) {
                            highlight($(this));
                            $(this).val(min);
                        } else if (val > max) {
                            highlight($(this));
                            $(this).val(max);
                        }

                        // Erase leading zeros
                        $(this).val(parseInt($(this).val(), 10));
                    }
                });

                // Initialize the slider
                $this.slider(data);
            });
        };

        this.revert = function () {

            // From original
            this._setValue(this.original_value);

            var $slider = this.$input.siblings('.render-modal-att-slider');

            // Allows range to transition only when reverting (delay must match the CSS3 transition)
            $slider.addClass('render-modal-att-slider-reverting');
            setTimeout(function () {
                $slider.removeClass('render-modal-att-slider-reverting');
            }, 500);
        };

        /**
         * Rebuilds the available options.
         *
         * For this one, it changes the available min and max.
         *
         * @since 1.1-beta-1
         *
         * @param {object} response The AJAX response.
         */
        this.rebuildOptions = function (response) {

            var min = response.options.min,
                max = response.options.max,
                value = this.getValue(),
                $slider = this.$input.siblings('.render-modal-att-slider');

            $slider.slider('option', {
                min: min,
                max: max
            });

            $slider.data('min', min);
            $slider.data('max', max);

            if (value < min) {
                this._setValue(min);
            }

            if (value > max) {
                this._setValue(max);
            }
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Media attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Media = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the WP Media integration.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            $container.find('.render-modal-att-media-upload').click(function () {

                // TODO Figure out various frame types and how to utilize this better
                var options = {
                        frame: 'post',
                        state: 'insert',
                        button: 'Use Media', // FIXME doesn't work
                        multiple: false
                    },
                    $this = $(this),
                    type = $this.data('type'),
                    json;

                if (type == 'gallery') {
                    options.multiple = true;
                }

                var file_frame = wp.media.frames.file_frame = wp.media(options);

                file_frame.open();

                file_frame.on('insert', function () {

                    json = file_frame.state().get('selection').first().toJSON();

                    if (0 > $.trim(json.url.length)) {
                        return;
                    }

                    switch (type) {
                        case 'image':
                            $this.siblings('.render-modal-att-media-preview-image').attr('src', json.url);
                            $this.siblings('.render-modal-att-input').val(json.id);
                            break;

                        case 'audio':
                            $this.siblings('.render-modal-att-media-preview-audio').html(json.url);
                            $this.siblings('.render-modal-att-input').val(json.url);
                            break;

                        case 'video':
                            $this.siblings('.render-modal-att-media-preview-video').html(json.url);
                            $this.siblings('.render-modal-att-input').val(json.url);
                            break;
                    }
                });
            });
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Renders the preview along with setting the value.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            var type = this.$input.siblings('.render-modal-att-media-upload').data('type');

            switch (type) {
                case 'image':
                    // TODO Image preview (probably via AJAX)
                    this.$input.val(value);
                    break;

                case 'audio':
                    this.$input.siblings('.render-modal-att-media-preview-audio').html(value);
                    this.$input.val(value);
                    break;

                case 'video':
                    this.$input.siblings('.render-modal-att-media-preview-video').html(value);
                    this.$input.val(value);
                    break;
            }
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Counter attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Counter = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up the counter object.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {

            var shift_down = false,
                $input = $container.find('.render-modal-att-counter'),
                $button_down = $input.siblings('.render-modal-counter-down'),
                $button_up = $input.siblings('.render-modal-counter-up'),
                min = parseInt($input.data('min')),
                max = parseInt($input.data('max')),
                step = parseInt($input.data('step')),
                shift_step = parseInt($input.data('shift-step'));

            // Set the "+" and "-" to disabled accordingly
            if (parseInt($input.val()) == min) {
                $button_down.addClass('disabled');
            } else {
                $button_down.removeClass('disabled');
            }

            if (parseInt($input.val()) == max) {
                $button_up.addClass('disabled');
            } else {
                $button_up.removeClass('disabled');
            }

            // If holding shift, let us know so we can use the shift_step later
            $(document).keydown(function (e) {
                if (e.which === 16) {
                    shift_down = true;
                }
            });

            $(document).keyup(function (e) {
                if (e.which === 16) {
                    shift_down = false;
                }
            });

            // Click on the "+"
            $container.find('.render-modal-counter-up').click(function () {
                $input.val(parseInt($input.val()) + (shift_down ? shift_step : step));
                $input.change();
            });

            // Click on the "-"
            $container.find('.render-modal-counter-down').click(function () {
                $input.val(parseInt($input.val()) - (shift_down ? shift_step : step));
                $input.change();
            });

            // Keep the number within its limits
            $input.change(function () {

                var $button_up = $(this).siblings('.render-modal-counter-up'),
                    $button_down = $(this).siblings('.render-modal-counter-down'),
                    min = parseInt($input.data('min')),
                    max = parseInt($input.data('max'));

                if (parseInt($(this).val()) >= max) {

                    if (parseInt($(this).val()) > max) {
                        highlight($(this));
                    }

                    $(this).val(max);
                    $button_up.addClass('disabled');
                    $button_down.removeClass('disabled');
                } else if (parseInt($(this).val()) <= min) {

                    if (parseInt($(this).val()) < min) {
                        highlight($(this));
                    }

                    $(this).val(min);
                    $button_down.addClass('disabled');
                    $button_up.removeClass('disabled');
                } else {

                    $button_up.removeClass('disabled');
                    $button_down.removeClass('disabled');
                }
            });

            // Units selectbox
            var $select = $container.find('select');

            if ($select.length) {

                $select.chosen({
                    width: '100px',
                    disable_search: true
                });

                // Make sure default value includes unit if the unit is set
                this.default_value = this.default_value + $select.val();
            }
        };

        /**
         * Gets the attribute field current value.
         *
         * Returns the value with the unit appended (if there is a unit type set).
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            var value = this.$input.val(),
                unit = this.$container.find('.render-modal-counter-unit select').val();

            if (unit) {
                value += unit;
            }

            return value;
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Deals with changing the value, the unit, and managing the buttons.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {

            // Divide value from units
            var values = value.split(/(\d+)/).filter(Boolean);
            value = values[0]; // The number

            // Make sure the "+" and "-" buttons have the right classes
            var min = this.$input.data('min'),
                max = this.$input.data('max');

            if (value == min) {
                this.$input.siblings('.render-modal-counter-down').addClass('disabled');
            } else {
                this.$input.siblings('.render-modal-counter-down').removeClass('disabled');
            }

            if (value == max) {
                this.$input.siblings('.render-modal-counter-up').addClass('disabled');
            } else {
                this.$input.siblings('.render-modal-counter-up').removeClass('disabled');
            }

            this.$input.val(value);
            this.$input.change();

            // If a unit was found
            if (values.length > 1) {
                this.$container.find('.render-modal-counter-unit-input').val(values[1]); // The unit
            }
        };

        /**
         * Rebuilds the available options.
         *
         * For this one, it changes the available min and max.
         *
         * @since 1.1-beta-1
         *
         * @param {object} response The AJAX response.
         */
        this.rebuildOptions = function (response) {

            var min = response.options.min,
                max = response.options.max,
                value = this.getValue();

            this.$input.data('min', min);
            this.$input.data('max', max);

            if (value < min) {
                this._setValue(min.toString());
            }

            if (value > max) {
                this._setValue(max.toString());
            }
        };

        this.init($e);
    };

    /**
     * Modulation of AttAPI for the Repeater attribute type.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     * @constructor
     */
    var Repeater = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        /**
         * Sets up repeater fields.
         *
         * @since 1.1-beta-1
         *
         * @param {{jQuery}} $container The current attribute row.
         */
        this.postInit = function ($container) {
            this.initRepeaterButtons($container);
        };
        /**
         * Initializes the repeater field buttons.
         *
         * Sets up handlers for the repeater field add and remove buttons.
         *
         * @since 1.0.0
         *
         * @param {HTMLElement} $e The attribute row container.
         */
        this.initRepeaterButtons = function ($e) {

            var $container = $e.find('.render-modal-att-field'),
                _this = this;

            // Add a new field after on pressing the "+"
            $container.find('.render-modal-repeater-add').off().click(function () {

                // Make sure we're not hitting a max first
                var max = $(this).closest('.render-modal-repeater-field').data('max'),
                    current = $(this).closest('.render-modal-att-field').find('.render-modal-repeater-field').length;

                if (max && current >= parseInt(max) + 1) { // + 1 for invisible dummy field
                    $(this).closest('.render-modal-att-field').effect('shake', {
                        distance: 10
                    }, 200);
                    return;
                }

                // Clone the dummy field in after the current field
                var $clone = $(this).closest('.render-modal-att-field').find('.render-modal-repeater-field.dummy-field').clone();

                // Modify the clone
                $clone.show();
                $clone.find('.render-modal-att-row').removeAttr('data-no-init');
                $clone.removeClass('dummy-field');

                $(this).closest('.render-modal-repeater-field').after($clone);

                // Re-build the attObj data for the newly cloned atts
                Render_Modal.initAtts();

                // Re-attach button handlers
                _this.initRepeaterButtons($e);
            });

            // Delete the field on pressing the "-"
            $container.find('.render-modal-repeater-remove').off().click(function () {

                var $field = $(this).closest('.render-modal-repeater-field');

                // If we're on the second (first visible) field and they're are no more (visible) fields besides this one
                if ($field.index() == 1 && $(this).closest('.render-modal-att-row').find('.render-modal-repeater-field').length <= 2) {

                    // Clear the inputs
                    highlight($field);
                    $field.find('.render-modal-att-row').each(function () {
                        $(this).data('attObj').revert();
                    });
                } else {

                    // Remove the field
                    highlight($field);
                    $field.effect('drop', {
                        duration: 300,
                        complete: function () {
                            $(this).remove();
                        }
                    });
                }
            });
        };

        /**
         * Reverts the attribute to its original values.
         *
         * Removes all extra repeat fields.
         *
         * @since 1.0.0
         */
        this.revert = function () {
            this.$container.find('.render-modal-repeater-field').each(function () {
                if ($(this).index() > 1) {
                    $(this).remove();
                }
            });
        };

        /**
         * Gets the attribute field current value.
         *
         * Gets the attribute field values for every repeater field.
         *
         * @since 1.0.0
         *
         * @returns {*} The attribute field value.
         */
        this.getValue = function () {

            var values = {};

            this.$container.find('.render-modal-att-row').each(function () {

                // Skip dummy field
                if ($(this).closest('.render-modal-repeater-field').hasClass('dummy-field')) {
                    return true; // Continue $.each
                }

                var attObj = $(this).data('attObj');

                if (typeof values[attObj.name] != 'undefined') {
                    // Att already set, append new value
                    values[attObj.name] += '::sep::' + attObj._getValue();
                } else {
                    values[attObj.name] = attObj._getValue();
                }
            });

            return JSON.stringify(values);
        };

        /**
         * Sets the attribute field to a specified value.
         *
         * Sets every field within the Repeater by adding as many fields as necessary.
         *
         * @since 1.0.0
         *
         * @param {*} object The value to set to (in JSON format).
         */
        this.setValue = function (object) {

            var self = this;

            if (object.length) {

                // Turn our string literal into an object
                object = JSON.parse(object);

                // Construct the fields object
                var fields = parseRepeaterField(object);

                // Add as many new fields as necessary
                for (var i = 1; i < fields.length; i++) {

                    // Fire clicking the "+" button manually in order to create all the fields
                    this.$container.find('.render-modal-repeater-field:eq(1)').find('.render-modal-repeater-add').click();
                }

                // Rebuild the new atts attObj data
                Render_Modal.initAtts();

                // Set the values
                for (i = 0; i < fields.length; i++) {

                    $.each(fields[i], function (name, value) {
                        self.$container.find('.render-modal-repeater-field:eq(' + ( i + 1 ) + ')').
                            find('.render-modal-att-row[data-att-name="' + name + '"]').data('attObj')._setValue(value);
                    });
                }
            }
        };

        this.init($e);
    };

    // Fires on document ready
    $(function () {
        Render_Modal.init();
    });

    // Fires whenever the window is re-sized
    $(window).resize(function () {
        Render_Modal.resize();
    });

    // ---------------- //
    // Helper functions //
    // ---------------- //

    /**
     * Run the value through various sanitation methods to prepare for being a shortcode attribute.
     *
     * @since 1.1-beta-1
     * @global sc_attr_escapes
     *
     * @param {string} value The value to escape.
     * @returns {string} The escaped string.
     */
    function escape_sc_attr(value) {

        var charCode, regExp;

        // Run through all of the escapes
        $.each(Render_Data.sc_attr_escapes, function (i, escape) {

            charCode = escape.charCodeAt(0).toString();

            regExp = new RegExp(esc_regex_string(escape), 'g');
            value = value.replace(regExp, '::' + charCode + '::');
        });

        return value;
    }

    /**
     * Un-escapes the shortcode attribute.
     *
     * @since 1.1-beta-1
     * @global sc_attr_escapes
     *
     * @param {string} value The value to un-escape.
     * @returns {string} The un-escaped string.
     */
    function unescape_sc_attr(value) {

        var charCode, regExp;

        // Run through all of the escapes
        $.each(Render_Data.sc_attr_escapes, function (i, escape) {

            charCode = escape.charCodeAt(0).toString();

            regExp = new RegExp('::' + charCode + '::', 'g');
            value = value.replace(regExp, escape);
        });

        return value;
    }

    /**
     * Escapes a string for use as a regular expression.
     *
     * @since 1.1-beta-1
     *
     * @param {string} string The string to be escaped.
     * @returns {string} The escaped string.
     */
    function esc_regex_string(string) {
        return string.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    /**
     * jQuery animation for highlighting input fields.
     *
     * @since 1.0.0
     *
     * @param {jQuery} $e The input field to highlight.
     */
    function highlight($e) {

        var background = error_color,
            transition = 300,
            orig_backgroundColor;

        // Remove the colors to get the computed original colors and store them
        $e.css('backgroundColor', '');

        orig_backgroundColor = $e.css('backgroundColor');

        // Set the highlight colors
        $e.css('backgroundColor', background);

        // Animate to orig colors
        $e.animate({
            backgroundColor: orig_backgroundColor
        }, {
            duration: transition,
            complete: function () {
                $(this).css('backgroundColor', '');
            }
        });
    }

    /**
     * Converts textarea elements to HTML elements.
     *
     * @since 1.0.0
     *
     * @param {string} value The value to convert.
     * @returns {string} The converted value.
     */
    function HTMLtoTextarea(value) {

        // Convert line breaks
        value = value.replace(/<br\s*\/?>/mg, "\n");

        // Convert spaces
        //var regExpSpaces = new RegExp(String.fromCharCode(160), "g");
        value = value.replace(/&nbsp;/g, " ");

        if (typeof window.switchEditors !== 'undefined') {
            value = window.switchEditors.pre_wpautop(value);
        }

        return value;
    }

    /**
     * Converts HTML elements to textarea elements.
     *
     * @since 1.0.0
     *
     * @param {string} value The value to convert.
     * @returns {string} The converted value.
     */
    function textareaToHTML(value) {

        // Convert line breaks
        value = value.replace(/(?:\r\n|\r|\n)/g, '<br/>');

        // Convert spaces
        // TODO more efficient way to do this
        value = value.replace(/\s/g, '&nbsp;');

        return value;
    }

    /**
     * Parses the attribute output of a repeater field.
     *
     * @since 1.1-beta-1
     *
     * @param object The repeater field "object"
     * @returns {Array} Sorted fields
     */
    window['parseRepeaterField'] = function (object) {

        var fields = [];
        $.each(object, function (name, values) {

            var att_values = values.split('::sep::');

            for (var i = 0; i < att_values.length; i++) {

                if (!fields[i]) {
                    fields[i] = {};
                }

                fields[i][name] = att_values[i];
            }
        });

        return fields;
    }
})(jQuery);