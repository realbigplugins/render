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
                if ($(this).data('attObj') || $(this).attr('data-no-init')) {
                    return true; // Continue $.each
                }

                var att_type = $(this).attr('data-att-type'),
                    $container = $(this).find('.render-modal-att-field'),
                    attObj;

                // Initialize each type of att (this is as big one!)
                switch (att_type) {
                    case 'hidden':

                        attObj = new Hidden($(this));
                        break;

                    case 'selectbox':

                        attObj = new Selectbox($(this));

                        // Apply Chosen
                        var $chosen = $(this).find('.chosen'),
                            options = {
                                width: '100%',
                                search_contains: true,
                                allow_single_deselect: true
                            };

                        if ($chosen.hasClass('allow-icons')) {
                            options.disable_search = true;
                        }

                        $chosen.chosen(options);

                        // Fix scroll issue
                        $container.find('.chosen-results').bind('mousewheel', function (e) {
                            $(this).scrollTop($(this).scrollTop() - e.originalEvent.wheelDeltaY);
                            return false;
                        });

                        // Extend functionality to allow icons
                        if ($chosen.hasClass('allow-icons')) {

                            $chosen.on('chosen:showing_dropdown chosen:updated', function () {

                                $(this).find('option').each(function (index) {

                                    var icon = $(this).attr('data-icon');

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

                            // Use the custom value when hiding the dropdown
                            $chosen.on('chosen:hiding_dropdown', function (e, a) {

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
                        break;
                    case 'colorpicker':

                        attObj = new Colorpicker($(this));

                        $(this).find('.render-modal-att-colorpicker').each(function () {
                            var data = $(this).data();
                            $(this).wpColorPicker(data);
                        });
                        break;

                    case 'media':

                        attObj = new Media($(this));

                        $(this).find('.render-modal-att-media-upload').click(function (event) {

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

                        break;

                    case 'slider':

                        attObj = new Slider($(this));

                        $(this).find('.render-modal-att-slider').each(function () {

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

                                var _data = $this.attr('data-' + allowed_data[i]);
                                if (_data) {
                                    data[allowed_data[i]] = _data;
                                }
                            }

                            // Make sure various values are int
                            var int_vals = [
                                'max',
                                'min',
                                'step',
                                'value'
                            ];

                            for (i = 0; i < int_vals.length; i++) {
                                if (data[int_vals[i]]) {
                                    data[int_vals[i]] = parseInt(data[int_vals[i]]);
                                }
                            }

                            // Bool values
                            var bool_vals = [
                                'range',
                                'disabled',
                                'animate'
                            ];

                            for (i = 0; i < bool_vals.length; i++) {
                                if (data[bool_vals[i]]) {
                                    data[bool_vals[i]] = data[bool_vals[i]] === '1';
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

                                if ($slider.attr('data-range')) {

                                    // Range slider
                                    var $text = $(this).siblings('.render-modal-att-slider-range-text'),
                                        values = $(this).val().split('-');

                                    $text.find('.render-modal-att-slider-range-text-value1').html(values[0]);
                                    $text.find('.render-modal-att-slider-range-text-value2').html(values[1]);

                                    $slider.slider('values', values);
                                } else {

                                    // Normal slider
                                    var min = parseInt($slider.attr('data-min')),
                                        max = parseInt($slider.attr('data-max')),
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

                        break;

                    case 'counter':

                        attObj = new Counter($(this));

                        var shift_down = false,
                            $input = $(this).find('.render-modal-att-counter'),
                            $button_down = $input.siblings('.render-modal-counter-down'),
                            $button_up = $input.siblings('.render-modal-counter-up'),
                            min = parseInt($input.attr('data-min')),
                            max = parseInt($input.attr('data-max')),
                            step = parseInt($input.attr('data-step')),
                            shift_step = parseInt($input.attr('data-shift-step'));

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
                        $(this).find('.render-modal-counter-up').click(function () {
                            $input.val(parseInt($input.val()) + (shift_down ? shift_step : step));
                            $input.change();
                        });

                        // Click on the "-"
                        $(this).find('.render-modal-counter-down').click(function () {
                            $input.val(parseInt($input.val()) - (shift_down ? shift_step : step));
                            $input.change();
                        });

                        // Keep the number within its limits
                        $input.off().change(function () {

                            var $button_up = $(this).siblings('.render-modal-counter-up'),
                                $button_down = $(this).siblings('.render-modal-counter-down');

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
                        var $select = $(this).find('select');

                        if ($select.length) {

                            $select.chosen({
                                width: '100px',
                                disable_search: true
                            });
                        }

                        break;

                    case 'repeater':

                        attObj = new Repeater($(this));

                        initRepeaterButtons($(this));

                        break;

                    case 'checkbox':

                        attObj = new Checkbox($(this));

                        $(this).find('.render-modal-att-checkbox').change(function () {

                            if ($(this).prop('checked')) {
                                $container.find('.render-modal-att-checkbox-label').addClass('checked');
                            } else {
                                $container.find('.render-modal-att-checkbox-label').removeClass('checked');
                            }
                        });

                        $(this).find('.render-modal-att-checkbox-label').click(function () {
                            var $checkbox_input = $container.find('.render-modal-att-checkbox');
                            $checkbox_input.prop('checked', !$checkbox_input.prop('checked')).trigger('change');
                        });

                        break;

                    case 'toggle':

                        attObj = new Toggle($(this));
                        break;

                    case 'textarea':

                        attObj = new Textbox($(this));

                        // Disable Render Modal actions
                        $(this).find('textarea').keyup(function (e) {

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
                        $(this).delegate('textarea', 'keydown', function (e) {

                            var keyCode = e.keyCode || e.which;

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

                        break;

                    default:

                        attObj = new Textbox($(this));
                        break;
                }

                $(this).data('attObj', attObj);

                // Custom callback
                if ($(this).attr('data-init-callback')) {
                    window[$(this).attr('data-init-callback')]($(this), attObj);
                }
            });
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
                            code = $(this).attr('data-code'),
                            source = $(this).attr('data-source'),
                            tags = $(this).attr('data-tags'),
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

            this.clearShortcodeErrors();

            // Bail if the shortcode is disabled
            if ($container.hasClass('disabled')) {

                // Error message
                var $description = $container.find('.render-modal-shortcode-description');
                $description.data('shortcodeDescriptionText', $description.html());

                // TODO Make translatable
                $description.html($container.data('shortcodeErrorMessage'));

                $container.addClass('render-modal-shortcode-error-message');

                highlight($container);

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
            this.active_shortcode = $container.attr('data-code');

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
         * Clears error messages in the shortcode item.
         *
         * @since 1.0.0
         */
        clearShortcodeErrors: function () {

            // Remove any previous error messages
            elements.list.find('.render-modal-shortcode.render-modal-shortcode-error-message').
                find('.render-modal-shortcode-description').each(function () {
                    $(this).html($(this).data('shortcodeDescriptionText'));
                    $(this).closest('.render-modal-shortcode').removeClass('render-modal-shortcode-error-message');
                });
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

            var category = $e.attr('data-category'),
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
                    if (category !== $(this).attr('data-category')) {
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
                atts[match[3]] = match[4];
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
         * Populates the shortcode item with attributes.
         *
         * @since 1.0.0
         *
         * @param atts The attributes to use.
         */
        populateShortcode: function (atts) {

            $.each(atts, function (name, value) {

                var attObj = elements.active_shortcode.find('.render-modal-att-row[data-att-name="' + name + '"]').data('attObj');

                if (attObj) {
                    attObj._setValue(value);
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
            this.clearShortcodeErrors();
            this.clearSearch();

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

            var code = elements.active_shortcode.attr('data-code'),
                title = elements.active_shortcode.attr('data-title'),
                props = render_data[code],
                atts = {},
                att_output = '',
                content = '',
                selection = this.selection,
                output, nested;

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
            });

            // Get the content
            if (props.wrapping) {
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

                    atts.nested_children_count = count;

                    for (var i = 0; i < count; i++) {

                        // Get the attributes
                        var attributes = '',
                            child_content = typeof fields[i].content != 'undefined' && fields[i].content != '' ? fields[i].content : '';

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

                    // Make sure the value is always text
                    value = value.toString();

                    // Add the att to the shortcode output
                    if (value && value.length) {
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

                // Skip if no attObj
                if (!attObj) {
                    return true; // Continue $.each
                }

                var required = attObj.$container.attr('data-required'),
                    validate = attObj.$container.attr('data-validate'),
                    att_value = attObj._getValue(),
                    att_valid = true;

                // Basic required and field being empty
                if (required === '1' && !att_value) {
                    att_valid = false;
                    validated = false;
                    attObj.setInvalid('This field is required');
                    return true; // continue $.each iteration
                } else if (!att_value) {
                    return true; //continue $.each iteration
                }

                // If there's validation, let's do it
                if (validate.length) {

                    validate = Render_Modal._stringToObject(validate);

                    $.each(validate, function (type, value) {

                        var regEx,
                            url_pattern = '[(http(s)?):\\/\\/(www\\.)?a-zA-Z0-9@:%._\\+~#=]{2,256}\\.[a-z]{2,6}\\b' +
                                '([-a-zA-Z0-9@:%_\\+.~#?&//=]*)',
                            email_pattern = '\\b[\\w\\.-]+@[\\w\\.-]+\\.\\w{2,4}\\b';


                        // Validate for many different types
                        switch (type) {

                            // Url validation
                            case 'url':
                                regEx = new RegExp(url_pattern, 'ig');

                                if (!att_value.match(regEx)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid('Please enter a valid URL');
                                }
                                break;

                            // Email validation
                            case 'email':

                                regEx = new RegExp(email_pattern, 'ig');

                                if (!att_value.match(regEx)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid('Please enter a valid Email');
                                }
                                break;

                            // Maximum character count
                            case 'maxchar':

                                if (att_value.length > parseInt(value)) {

                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid((att_value.length - parseInt(value)) + ' too many characters.');
                                }
                                break;

                            // Minimum character count
                            case 'minchar':

                                if (att_value.length < parseInt(value)) {

                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid((parseInt(value)) - att_value.length + ' too few characters.');
                                }
                                break;

                            // No numbers allowed
                            case 'charonly':

                                if (att_value.match(/[0-9]/)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid('No numbers please');
                                }
                                break;

                            // Only numbers allowed
                            case 'intonly':

                                var numbers = att_value.match(/[0-9]+/);

                                if (!numbers || (numbers[0] !== numbers.input)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid('Only numbers please');
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

                var sanitize = Render_Modal._stringToObject($(this).attr('data-sanitize')),
                    att_value = attObj._getValue();

                if (sanitize && att_value !== null && att_value.length) {
                    $.each(sanitize, function (type, value) {

                        switch (type) {
                            case 'url':
                                if (!att_value.match(/https?:\/\//)) {
                                    attObj._setValue('http://' + att_value);
                                }
                                break;

                            // If no matches, throw an error
                            default:
                                throw new Error('Render -> Unsupported sanitation method "' + type + '" for the shortcode "' + attObj.shortcode + '" at field "' + attObj.fieldname + '"');

                        }
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

            shortcode = typeof shortcode !== 'undefined' ? shortcode : elements.active_shortcode;

            if (shortcode) {

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
         * Initializes the object.
         *
         * @since 1.0.0
         *
         * @param {HTMLElement} $e The attribute row container.
         */
        this.init = function ($e) {

            // Setup properties
            this.$container = $e;
            this.$input = this.$container.find('.render-modal-att-input');
            this.name = $e.attr('data-att-name');
            this.fieldname = this.$container.find('.render-modal-att-name').text().trim();
            this.shortcode = this.$container.closest('.render-modal-shortcode').attr('data-code');

            this.storeOriginalValue();
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
         *
         * @private
         */
        this._getValue = function () {
            this.$input.trigger('render:att_getValue');
            return this.getValue();
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
         * @private
         *
         * @param {*} value The value to set to.
         */
        this._setValue = function (value) {
            this.$input.trigger('render:att_setValue');
            this.setValue(value);
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
            this.$input.trigger('render:att_setValue');
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
            this.$input.trigger('render:att_setInvalid');
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
    var Textbox = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

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

            if (this.$input.prop('tagName') === 'textarea') {
                return this.$input.text();
            } else {
                return this.$input.val();
            }
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

                this.$input.trigger('chosen:updated');
                return;
            }

            // Custom input (value doesn't exist in options)
            if (!this.$input.find('option[value="' + value + '"]').length) {
                this.$container.find('.chosen-search input[type="text"]').val(value);
                this.$input.trigger('chosen:hiding_dropdown');
                return;
            }

            // Account for multi-select
            if (this.$input.attr('multiple')) {
                this.$input.val(value.split(','));
            } else {
                this.$input.val(value);
            }

            this.$input.trigger('chosen:updated');
        };

        /**
         * Reverts the attribute to its original values.
         *
         * Removes the custom value first, then sets to the original value.
         *
         * @since 1.0.0
         */
        this.revert = function () {
            this.$container.find('.chosen-custom-input').remove();
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
         * Sets the attribute field to a specified value.
         *
         * Triggers Iris colorpicker with new color.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {
            this.$input.iris('color', value);
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
         * Sets the attribute field to a specified value.
         *
         * Triggers the change to update jQuery UI Slider.
         *
         * @since 1.0.0
         *
         * @param {*} value The value to set to.
         */
        this.setValue = function (value) {
            this.$input.val(value);
            this.$input.change();
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
            var min = this.$input.attr('data-min'),
                max = this.$input.attr('data-max');

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

            // If a unit was found
            if (values.length > 1) {
                this.$container.find('.render-modal-counter-unit-input').val(values[1]); // The unit
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

    // Fires whenever the window is resized
    $(window).resize(function () {
        Render_Modal.resize();
    });

    // ---------------- //
    // Helper functions //
    // ---------------- //

    /**
     * Initializes the repeater field buttons.
     *
     * Sets up handlers for the repeater field add and remove buttons.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The attribute row container.
     */
    function initRepeaterButtons($e) {

        var $container = $e.find('.render-modal-att-field');

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
            initRepeaterButtons($e);
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
    }

    /**
     * jQuery animation for highlighting input fields.
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} $e The input field to highlight.
     * @param {string} color The color to highlight.
     * @param {string} font_color The color of the font to use when highlighting.
     * @param {int} transition The animation time.
     */
    function highlight($e, color, font_color, transition) {

        color = typeof color !== 'undefined' ? color : error_color;
        font_color = typeof font_color !== 'undefined' ? font_color : '#fff';
        transition = typeof transition !== 'undefined' ? transition : 300;

        // Get and store the original color
        var orig_colors = {};
        if ($e.data('highlightOriginalColors')) {
            orig_colors = $e.data('highlightOriginalColors');
        } else {
            orig_colors.background = $e.css('backgroundColor');
            orig_colors.font = $e.css('color');
            $e.data('highlightOriginalColors', orig_colors);
        }

        // Animate the color
        $e.css({
            backgroundColor: color,
            color: font_color
        }).stop().animate({
            backgroundColor: orig_colors.background,
            color: orig_colors.font
        }, {
            duration: transition,
            complete: function () {
                $(this).removeAttr('style');
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

        if (typeof window.switchEditors !== 'undefined' ) {
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
     * @since {{VERSION}}
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