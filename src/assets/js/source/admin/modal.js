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
        usl_modal_open = false,
        selection;

    USL_Modal = {

        init: function () {
            this.establish_elements();
            this.binds();
            this.store_original_values();
            this.prevent_window_scroll();
            this.activate_shortcode();
            this.search();
            this.sliders();
            this.colorpickers();
        },

        load: function () {
        },

        resize: function () {
            this.list_height();
        },

        establish_elements: function () {
            elements.wrap = $('#usl-modal-wrap');
            elements.submit = $('#usl-modal-submit');
            elements.backdrop = $('#usl-modal-backdrop');
            elements.cancel = elements.wrap.find('.usl-modal-cancel');
            elements.close = elements.wrap.find('.usl-modal-close');
            elements.title = elements.wrap.find('.usl-modal-title');
            elements.search = elements.wrap.find('.usl-modal-search');
            elements.categories = elements.wrap.find('.usl-modal-categories');
            elements.footer = elements.wrap.find('.usl-modal-footer');
            elements.list = elements.wrap.find('.usl-modal-shortcodes');
        },

        binds: function () {

            // Submit the form
            elements.submit.off('click').click(function (event) {
                event.preventDefault();
                USL_Modal.update();
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
                USL_Modal.filter_by_category($(this));
            });

            // Show advanced atts
            elements.list.find('.show-advanced-atts').click(function () {
                USL_Modal.toggle_advanced_atts($(this));
                return false;
            });

            // Submit with enter
            $(document).keypress(function (e) {
                if (usl_modal_open && e.which == 13) {
                    USL_Modal.update();
                }
            });
        },

        store_original_values: function () {
            elements.list.find('input').each(function () {
                $(this).data('original-value', $(this).val());
            });
        },

        colorpickers: function () {
            elements.list.find('.colorpicker').each(function () {
                var data = $(this).data();

                // Store the original color for later refresh use
                $(this).data('original-color', $(this).val());

                $(this).wpColorPicker(data);
            });
        },

        sliders: function () {

            elements.list.find('.slider').each(function () {
                var $e = $(this),
                    data = $e.data(),
                    indicator = $e.siblings('.slider-value');

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

            var _search_timeout, _search_delay,
                search_loading = false,
                search_delay = 1000,
                search_fade = 300;

            elements.wrap.find('input[name="usl-modal-search"]').on('keyup', function (e) {

                if (e.which == 13) {
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

                    elements.list.find('> li').each(function () {
                        var title = $(this).find('.title').text(),
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

        activate_shortcode: function () {

            elements.list.find('.accordion-section-title, .usl-modal-sc-title').off('click').click(function (e) {

                var e_container = $(this).closest('li'),
                    active = e_container.hasClass('active');

                elements.list.find('li').removeClass('active');

                if (!$(this).hasClass('accordion-section-title')) {
                    elements.list.find('li').removeClass('open');
                }

                if (!active) {
                    e_container.addClass('active');
                }
            });
        },

        toggle_advanced_atts: function (e) {
            var e_advancedatts = e.next('.advanced-atts'),
                txt = e_advancedatts.is(':visible') ? 'Show advanced options' : 'Hide advanced options';
            e_advancedatts.toggle();
            e.text(txt);
        },

        prevent_window_scroll: function () {

            elements.list.bind('mousewheel', function (e) {

                $(this).scrollTop($(this).scrollTop() - e.originalEvent.wheelDeltaY);
                return false;
            });
        },

        filter_by_category: function (e) {
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

        list_height: function () {
            var height = elements.wrap.innerHeight()
                - elements.title.outerHeight(true)
                - elements.search.outerHeight(true)
                - elements.categories.outerHeight(true)
                - elements.wrap.find('.dashicons-leftright').outerHeight(true)
                - elements.footer.outerHeight(true);

            elements.list.height(height);
        },

        open: function (_selection) {

            selection = _selection;
            usl_modal_open = true;

            $(document).trigger('usl-modal-open');

            elements.wrap.show();
            elements.backdrop.show();

            elements.search.find('input[name="usl-modal-search"]').focus();

            this.list_height();
        },

        close: function () {

            usl_modal_open = false;

            $(document).trigger('usl-modal-close');

            elements.wrap.hide();
            elements.backdrop.hide();

            var all_shortcodes = elements.list.find('li');
            this.refresh(all_shortcodes);
        },

        update: function () {

            $(document).trigger('usl-modal-update');

            var e_active = elements.list.find('li.active'),
                atts = e_active.find('.usl-modal-shortcode-form').serializeArray(),
                code = e_active.attr('data-code'),
                props, output;

            if (e_active.length === 0) {
                return;
            }

            if (!this.validate()) {
                return;
            }

            props = USL_Data.all_shortcodes[code];

            output = '[' + code;

            // Add on atts if they exist
            if (atts.length) {
                for (i = 0; i < atts.length; i++) {
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
            this.refresh(e_active);

            return output;
        },

        validate: function () {
            var e_active = elements.list.find('li.active'),
                validated = true;

            e_active.find('.usl-modal-sc-att-field').each(function () {
                var required = $(this).attr('data-required'),
                    val = $(this).find('input, select').val();

                if (required === '1' && !val) {
                    $(this).closest('.usl-modal-sc-att-row').addClass('invalid');
                    validated = false;
                } else {
                    $(this).closest('.usl-modal-sc-att-row').removeClass('invalid');
                }
            });

            return validated;
        },

        refresh: function (e) {

            e.each(function () {

                e.find('.text-input').each(function () {
                    $(this).val($(this).data('original-value'))
                });

                e.find('select').prop('selectedIndex', 0);

                e.find('.slider').each(function () {
                    $(this).slider('value', $(this).data('original-value'));
                });

                e.find('.colorpicker').each(function () {
                    $(this).iris('color', $(this).data('original-value'));
                });

                e.removeClass('active open');
            });
        }
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