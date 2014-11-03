/**
 * Functionality for the USL tinyMCE button in the editor.
 *
 * @since USL 1.0.0
 *
 * @global tinymce
 * @global USL_Data
 *
 * @package USL
 * @subpackage TinyMCE
 */

if (typeof USL_MCE === 'undefined' && typeof tinymce !== 'undefined') {
    var USL_MCE;
    (function ($) {
        var elements = {},
            usl_tinymce_open = false,
            editor, selection;

        USL_MCE = {

            init: function () {
                this.establish_elements();
                this.binds();
                this.store_original_values();
                this.add_to_mce();
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
                elements.wrap = $('#usl-mce-wrap');
                elements.submit = $('#usl-mce-submit');
                elements.backdrop = $('#usl-mce-backdrop');
                elements.cancel = elements.wrap.find('.usl-mce-cancel');
                elements.close = elements.wrap.find('.usl-mce-close');
                elements.title = elements.wrap.find('.usl-mce-title');
                elements.search = elements.wrap.find('.usl-mce-search');
                elements.categories = elements.wrap.find('.usl-mce-categories');
                elements.footer = elements.wrap.find('.usl-mce-footer');
                elements.list = elements.wrap.find('.usl-mce-shortcodes');
            },

            binds: function () {

                // Submit the form
                elements.submit.off('click').click(function (event) {
                    event.preventDefault();
                    USL_MCE.update();
                });

                // Close the form
                elements.cancel.click(function (event) {
                    event.preventDefault();
                    USL_MCE.close();
                });
                elements.close.click(function (event) {
                    event.preventDefault();
                    USL_MCE.close();
                });

                // Filter shortcodes by category
                elements.categories.find('li').click(function () {
                    USL_MCE.filter_by_category($(this));
                });

                // Show advanced atts
                elements.list.find('.show-advanced-atts').click(function () {
                    USL_MCE.toggle_advanced_atts($(this));
                    return false;
                });

                // Submit with enter
                $(document).keypress(function (e) {
                    if (usl_tinymce_open && e.which == 13) {
                        USL_MCE.update();
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

                elements.wrap.find('input[name="usl-mce-search"]').on('keyup', function (e) {

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

                elements.list.find('.accordion-section-title, .usl-mce-sc-title').off('click').click(function (e) {

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

            add_to_mce: function () {
                tinymce.PluginManager.add('usl_button', function (editor) {

                    editor.addButton('usl_button', {

                        // Establishes an icon class with the prefix "mce-i-"
                        icon: 'usl-mce-icon',
                        cmd: 'usl-open'
                    });

                    editor.addCommand('usl-open', function () {
                        USL_MCE.open();
                    });
                });
            },

            open: function () {

                usl_tinymce_open = true;

                // Get the tinymce editor object
                if (typeof tinymce !== 'undefined') {
                    var _editor = tinymce.get(wpActiveEditor);

                    if (_editor && !_editor.isHidden()) {
                        editor = _editor;
                    } else {
                        editor = null;
                    }
                }

                selection = editor.selection.getContent();

                $(document).trigger('usl-mce-open');

                elements.wrap.show();
                elements.backdrop.show();

                elements.search.find('input[name="usl-mce-search"]').focus();

                this.list_height();
            },

            close: function () {

                usl_tinymce_open = false;

                $(document).trigger('usl-mce-close');

                elements.wrap.hide();
                elements.backdrop.hide();

                var all_shortcodes = elements.list.find('li');
                this.refresh(all_shortcodes);

                editor.focus();
            },

            update: function () {

                var e_active = elements.list.find('li.active'),
                    atts = e_active.find('.usl-mce-shortcode-form').serializeArray(),
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

                console.log(atts);

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

                editor.insertContent(output);
                this.close();
                this.refresh(e_active);
            },

            validate: function () {
                var e_active = elements.list.find('li.active'),
                    validated = true;

                e_active.find('.usl-mce-sc-att-field').each(function () {
                    var required = $(this).attr('data-required'),
                        val = $(this).find('input, select').val();

                    if (required === '1' && !val) {
                        $(this).closest('.usl-mce-sc-att-row').addClass('invalid');
                        validated = false;
                    } else {
                        $(this).closest('.usl-mce-sc-att-row').removeClass('invalid');
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
            USL_MCE.init();
        });

        $(window).load(function () {
            USL_MCE.load();
        });

        $(window).resize(function () {
            USL_MCE.resize();
        });
    })(jQuery);
}