// TODO Don't initialize each attribute's data object until needed

/**
 * Functionality for the USL modal.
 *
 * @since USL 1.0.0
 *
 * @global USL_Data
 *
 * @package USL
 * @subpackage Modal
 */

var USL_Modal;
(function ($) {
    var elements = {},
        shortcodes = {},
        usl_modal_open = false,
        editing = false,
        _search_timeout, search_loading;

    USL_Modal = {

        output: '',

        init: function () {

            this.establishElements();
            this.binds();
            this.keyboardShortcuts();
            this.attsInit();
            this.preventWindowScroll();
            this.search();
            this.slidersInit();
            this.colorpickersInit();
        },

        load: function () {
        },

        resize: function () {
            this.listHeight();
        },

        establishElements: function () {
            elements.wrap = $('#usl-modal-wrap');
            elements.submit = $('#usl-modal-submit');
            elements.backdrop = $('#usl-modal-backdrop');
            elements.cancel = elements.wrap.find('.usl-modal-cancel');
            elements.close = elements.wrap.find('.usl-modal-close');
            elements.remove = $('#usl-modal-remove');
            elements.title = elements.wrap.find('.usl-modal-title');
            elements.search = elements.wrap.find('.usl-modal-search');
            elements.categories = elements.wrap.find('.usl-modal-categories');
            elements.footer = elements.wrap.find('.usl-modal-footer');
            elements.list = elements.wrap.find('.usl-modal-shortcodes');
            elements.active_shortcode = '';
            elements.search_input = elements.wrap.find('input[name="usl-modal-search"]');
        },

        binds: function () {

            // Active a shortcode
            elements.list.find('.accordion-section-title, .usl-modal-sc-title').click(function () {
                USL_Modal.activateShortcode($(this));
            });

            // Submit the form
            elements.submit.off('click').click(function (event) {
                event.preventDefault();
                USL_Modal.update();
            });

            // Remove button
           elements.remove.click(function () {
                $(document).trigger('usl-modal-remove');
            });

            // Close the form
            elements.cancel.click(function (event) {
                event.preventDefault();
                USL_Modal.close();
            });
            elements.close.click(function (event) {
                event.preventDefault();
                USL_Modal.close();
            });

            // Filter shortcodes by category
            elements.categories.find('li').click(function () {
                USL_Modal.filterByCategory($(this));
            });

            // Show advanced atts
            elements.list.find('.usl-modal-show-advanced-atts').click(function () {
                USL_Modal.toggleAdvancedAtts($(this));
                return false;
            });
        },

        keyboardShortcuts: function () {

            // TODO Allow complete navigation with keyboard
            // TODO  - Tab from search to open first result

            $(document).keyup(function (e) {

                if (!usl_modal_open) {
                    return;
                }

                // Enter
                switch (e.which) {

                    // Enter
                    case 13:
                        USL_Modal.update();
                        break;

                    // Escape
                    case 27:
                        USL_Modal.close();
                        break;

                    // Down arrow
                    case 40:

                        var $next = elements.active_shortcode.next();

                        if ($next.length) {
                            USL_Modal.closeShortcode();
                            elements.active_shortcode = $next;
                            USL_Modal.openShortcode();
                        }
                        break;

                    // Up arrow
                    case 38:

                        var $prev = elements.active_shortcode.prev();

                        if ($prev.length) {
                            USL_Modal.closeShortcode();
                            elements.active_shortcode = $prev;
                            USL_Modal.openShortcode();
                        }
                        break;
                    default:
                }
                if (e.which == 13) {
                }
            });
        },

        attsInit: function () {

            elements.list.find('.usl-modal-att-row').each(function () {
                var att_type = $(this).attr('data-att-type'),
                    attObj;

                switch (att_type) {
                    case 'selectbox':
                        attObj = new Selectbox($(this));
                        break;
                    case 'colorpicker':
                        attObj = new Colorpicker($(this));
                        break;
                    case 'slider':
                        attObj = new Slider($(this));
                        break;
                    default:
                        attObj = new Textbox($(this));
                        break;
                }

                $(this).data('attObj', attObj);
            });
        },

        colorpickersInit: function () {
            elements.list.find('.usl-modal-att-colorpicker').each(function () {
                var data = $(this).data();
                $(this).wpColorPicker(data);
            });
        },

        slidersInit: function () {

            elements.list.find('.usl-modal-att-slider').each(function () {
                var $e = $(this),
                    data = $e.data(),
                    indicator = $e.siblings('.usl-modal-att-slider-value');

                data.slide = function (event, ui) {
                    indicator.val(ui.value);
                };

                indicator.change(function () {
                    $e.slider('value', $(this).val());
                });

                $e.slider(data);
            });
        },

        search: function () {

            var _search_delay,
                search_delay = 1000,
                search_fade = 300;

            elements.search_input.on('keyup', function (e) {

                if (e.which == 13 || e.which == 27 || e.which == 40 || e.which == 38) {
                    return;
                }

                var search_query = $(this).val();

                if (!search_loading) {
                    elements.list.animate({opacity: 0}, search_fade);
                }

                search_loading = true;

                if (search_query === '') {
                    _search_delay = search_fade;
                } else {
                    _search_delay = search_delay;
                }

                clearTimeout(_search_timeout);
                _search_timeout = setTimeout(function () {

                    search_loading = false;
                    elements.list.animate({opacity: 1}, search_fade);

                    USL_Modal.closeShortcode();

                    elements.list.find('.usl-modal-shortcode').each(function () {
                        var title = $(this).find('.usl-modal-shortcode-title').text(),
                            description = $(this).find('.description').text(),
                            code = $(this).attr('data-code'),
                            search_string = title + description + code;

                        if (search_string.indexOf(search_query) < 0) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }, _search_delay);
            });
        },

        clearSearch: function () {
            elements.search_input.val('');
            elements.list.find('.usl-modal-shortcode').show();
            clearTimeout(_search_timeout);
            elements.list.css('opacity', 1);
            search_loading = false;
        },

        activateShortcode: function ($e) {

            var container = $e.closest('.usl-modal-shortcode');

            if (container.hasClass('active')) {
                return;
            }

            USL_Modal.closeShortcode();

            elements.active_shortcode = container;

            USL_Modal.openShortcode();
        },

        toggleAdvancedAtts: function ($e) {
            if ($e.text() === 'Show advanced options') {
                this.showAdvancedAtts($e);
            } else {
                this.hideAdvancedAtts($e);
            }
        },

        showAdvancedAtts: function ($e) {

            var $container = $e.siblings('.usl-modal-advanced-atts');

            $container.show();
            $e.text('Hide advanced options');
        },

        hideAdvancedAtts: function ($e) {

            var $container = $e.siblings('.usl-modal-advanced-atts');

            $container.hide();
            $e.text('Show advanced options');
        },

        preventWindowScroll: function () {

            elements.list.bind('mousewheel', function (e) {

                $(this).scrollTop($(this).scrollTop() - e.originalEvent.wheelDeltaY);
                return false;
            });
        },

        filterByCategory: function (e) {
            var category = e.attr('data-category'),
                shortcodes = elements.list.find('li'),
                e_active = elements.list.find('li.active');

            // Clear previously activated and opened items and clear forms
            this.refresh(e_active);

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
        },

        resetScroll: function () {
            elements.list.scrollTop(0);
        },

        listHeight: function () {

            var height = elements.wrap.innerHeight()
                - elements.title.outerHeight(true)
                - elements.search.outerHeight(true)
                - elements.categories.outerHeight(true)
                - elements.wrap.find('.dashicons-leftright').outerHeight(true)
                - elements.footer.outerHeight(true);

            elements.list.height(height);
        },

        showRemoveButton: function () {
            elements.remove.show();
        },

        hideRemoveButton: function () {
            elements.remove.hide();
        },

        modify: function (code, _selection) {

            // Set the selection if it's set manually
            if (typeof _selection !== 'undefined') {
                selection = _selection;
            }

            // Crop off any whitespace (generally preceding)
            code = code.trim();

            // Use some regex to get our atts and values (needs to be separately), and our shortcode
            var atts = code.match(/\s.*?(?==)/g),
                values = code.match(/"([^"]+)"/g),
                _shortcode = code.match(/\[(.*?)[\s|\]]/),
                shortcode = _shortcode[1] !== null ? _shortcode[1].trim() : '',
                _content = code.match(/\](.*)\[/),
                content = _content !== null ? _content[1] : '',
                pairs = {};

            if (content.length) {
                pairs.content = content;
            }

            this.setActiveShortcode(shortcode);

            // Create the key => value pairs (after trimming off whitespace from the atts, and the quotes from the values)
            if (atts !== null) {
                for (var i = 0; i < atts.length; i++) {
                    pairs[atts[i].trim()] = values[i].substring(1, values[i].length - 1);
                }
            }

            this.populateShortcode(pairs);

            this.open();

            this.openShortcode();
        },

        setActiveShortcode: function (shortcode) {

            // Find our current shortcode
            elements.list.find('li').each(function () {
                if ($(this).attr('data-code') === shortcode) {
                    elements.active_shortcode = $(this);
                }
            });
        },

        populateShortcode: function (pairs) {

            elements.active_shortcode.find('.usl-modal-att-row').each(function () {

                var attObj = $(this).data('attObj'),
                    name = attObj.$input.attr('name');

                if (typeof pairs[name] !== 'undefined') {
                    attObj.setValue(pairs[name]);
                }
            });
        },

        closeShortcode: function () {

            if (elements.active_shortcode.length) {

                elements.active_shortcode.removeClass('active open');
                USL_Modal.hideAdvancedAtts(elements.active_shortcode.find('.usl-modal-show-advanced-atts'));
                USL_Modal.refresh();
            }
        },

        openShortcode: function () {

            if (elements.active_shortcode.length) {

                // Activate it (also open it if it's an accordion)
                elements.active_shortcode.addClass('active');

                if (elements.active_shortcode.hasClass('accordion-section')) {
                    elements.active_shortcode.addClass('open');
                }

                // Scroll it into view
                var shortcode_offset = elements.active_shortcode.position(),
                    scrollTop = elements.list.scrollTop(),
                    offset = shortcode_offset.top + scrollTop;

                elements.list.stop().animate({
                    scrollTop: offset
                });
            }
        },

        open: function (_selection) {

            if (typeof _selection !== 'undefined') {
                selection = _selection;
            }

            usl_modal_open = true;

            $(document).trigger('usl-modal-open');

            elements.wrap.show();
            elements.backdrop.show();

            elements.search.find('input[name="usl-modal-search"]').focus();

            this.listHeight();

            $(document).trigger('usl-modal-open');
        },

        close: function () {

            usl_modal_open = false;

            elements.wrap.hide();
            elements.backdrop.hide();

            this.resetScroll();
            this.closeShortcode();
            this.clearSearch();
            elements.list.find('.usl-mce-shortcode.active').removeClass('active open');

            $(document).trigger('usl-modal-close');
        },

        update: function () {

            var e_active = elements.list.find('li.active');

            if (e_active.length === 0) {
                return;
            }

            if (!this.validate()) {
                return;
            }

            this.sanitize();

            var atts = e_active.find('.usl-modal-shortcode-form').serializeArray(),
                code = e_active.attr('data-code'),
                props, output;

            props = USL_Data.all_shortcodes[code];

            output = '[' + code;

            // Add on atts if they exist
            if (atts.length) {
                for (i = 0; i < atts.length; i++) {

                    // Set up the selection to be content if it exists
                    if (atts[i].name === 'content') {
                        selection = atts[i].value;
                        continue;
                    }

                    // Add the att to the shortcode output
                    if (atts[i].value.length) {
                        output += ' ' + atts[i].name + '="' + atts[i].value + '"';
                    }
                }
            }

            output += ']';

            if (props.wrapping) {
                output += selection + '[/' + code + ']';
            }

            this.close();

            this.output = output;

            $(document).trigger('usl-modal-update');
        },

        validate: function () {

            var validated = true;

            elements.active_shortcode.find('.usl-modal-att-row').each(function () {
                var attObj = $(this).data('attObj'),
                    required = attObj.$container.attr('data-required'),
                    validate = attObj.$container.attr('data-validate'),
                    att_value = attObj.getValue(),
                    att_valid = true;

                // Basic required and field being empty
                if (required === '1' && !att_value && validated) {
                    att_valid = false;
                    validated = false;
                    attObj.setInvalid('This field is required');
                    return true; // continue $.each iteration
                } else if (!att_value) {
                    return true; //continue $.each iteration
                }

                // If there's validation, let's do it
                if (validate.length) {

                    validate = USL_Modal._stringToObject(validate);

                    $.each(validate, function (type, value) {

                        // Validate for many different types
                        switch (type) {

                            // Url validation and sanitation
                            case 'url':

                                var url_pattern = /[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/ig;

                                if (!att_value.match(url_pattern)) {
                                    att_valid = false;
                                    validated = false;
                                    attObj.setInvalid('Please enter a valid URL');
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
                                throw new Error('USL -> Unsupported validation method "' + type + '" for the shortcode "' + attObj.shortcode + '" at field "' + attObj.fieldname + '"');
                        }
                    });
                }

                if (att_valid) {
                    attObj.setValid();
                }
            });

            return validated;
        },

        sanitize: function () {
            elements.active_shortcode.find('.usl-modal-att-row').each(function () {
                var sanitize = USL_Modal._stringToObject($(this).attr('data-sanitize')),
                    attObj = $(this).data('attObj'),
                    att_value = attObj.getValue();

                if (!att_value.length) {
                    return true; // continue $.each iteration
                }

                if (sanitize) {
                    $.each(sanitize, function (type, value) {

                        switch (type) {
                            case 'url':
                                if (!att_value.match(/https?:\/\//)) {
                                    attObj.setValue('http://' + att_value);
                                }
                                break;

                            // If no matches, throw an error
                            default:
                                throw new Error('USL -> Unsupported sanitation method "' + type + '" for the shortcode "' + attObj.shortcode + '" at field "' + attObj.fieldname + '"');

                        }
                    });
                }
            });
        },

        refresh: function () {

            // TODO Causing error with tinymce when using ESC to close
            elements.active_shortcode.find('.usl-modal-att-row').each(function () {
                var attObj = $(this).data('attObj');
                attObj._revert();
            });
        },

        _stringToObject: function (string) {

            if (typeof string === 'undefined' || !string.length) {
                return false;
            }
            string = '"' + string.replace(/(:|,)/g, '"' + "$1" + '"') + '"';
            string = JSON.parse('{' + string + '}');
            return string;
        }
    };

    function AttAPI() {

        this.original_value = null;
        this.fieldname = null;
        this.shortcode = null;
        this.$container = null;
        this.$input = null;

        this.init = function ($e) {

            this.$container = $e;
            this.$input = this.$container.find('.usl-modal-att-input');
            this.fieldname = this.$container.find('.usl-modal-att-name').text().trim();
            this.shortcode = this.$container.closest('.usl-modal-shortcode').attr('data-code');

            this._storeOriginalValue();
        };

        this._storeOriginalValue = function () {

            this.original_value = this.$input.val();

            this.storeOriginalValue();
        };

        this._revert = function () {

            this.$input.val(this.original_value);
            this.setValid();

            this.revert();
        };

        this.getValue = function () {
            return this.$input.val();
        };

        this.setValue = function (value) {
            this.$input.val(value);
        };

        this.setInvalid = function (msg) {
            this.$container.addClass('invalid');
            this.errorMsg(msg);
        };

        this.setValid = function () {
            this.$container.removeClass('invalid');
        };

        this.errorMsg = function (msg) {
            if (typeof this.$errormsg === 'undefined') {
                this.$errormsg = this.$container.find('.usl-modal-att-errormsg');
            }

            this.$errormsg.html(msg);
        };

        this.storeOriginalValue = function () {
        };
        this.revert = function () {
        };
    }

    var Textbox = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        this.getValue = function () {
            if (this.$input.prop('tagName') === 'textarea') {
                return this.$input.text();
            } else {
                return this.$input.val();
            }
        };

        this.init($e);
    };

    var Selectbox = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        this.init($e);
    };

    var Colorpicker = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        this.revert = function () {
            this.$input.iris('color', this.original_value);
        };

        this.setValue = function (value) {
            this.$input.iris('color', value);
        };

        this.init($e);
    };

    var Slider = function ($e) {

        // Extends the AttAPI object
        AttAPI.apply(this, arguments);

        this.revert = function () {
            this.$slider.slider('value', this.original_value);
        };

        this.setValue = function (value) {
            this.$input.val(value);
            this.$slider.slider('value', value);
        };

        this.init($e);
        this.$slider = this.$container.find('.usl-modal-att-slider');
    };

    $(function () {
        USL_Modal.init();
    });

    $(window).load(function () {
        USL_Modal.load();
    });

    $(window).resize(function () {
        USL_Modal.resize();
    });
})(jQuery);