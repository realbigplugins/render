var USL_MCECallbacks;
(function ($) {
    USL_MCECallbacks = {

        visualLoadCounter: {
            count: 0,
            total: 0
        },

        preShortcode: function (_shortcode, _atts, tag, classes, nostyle) {

            tag = !tag ? 'span' : tag;
            classes = !classes ? '' : classes;
            nostyle = typeof nostyle === 'undefined' ? false : nostyle;

            var atts = '',
                shortcode = '<span class="usl-tinymce-shortcode-code" data-code="' + _shortcode + '"></span>';

            classes += nostyle ? '' : 'styled';

            if (_atts) {
                atts += '<span class="usl-tinymce-shortcode-atts" data-atts=\'';
                $.each(_atts, function (name, value) {
                    atts += '"' + name + '": "' + value + '", ';
                });
                atts += '\'></span>';
            }

            return '<' + tag + ' class="usl-tinymce-shortcode-wrapper ' + _shortcode + ' ' + classes + '">' + atts + shortcode;
        },

        /**
         * Closes the wrapper for a USL shortcode.
         *
         * Note the nbsp. This is an invisible character with zero width. It's used so that when clicking
         * after the shortcode in the editor, you can insert the caret after the shortcode. Otherwise, it would default
         * to inside the shortcode.
         *
         * @since USL 1.0.0
         *
         * @param {string} tag Either a div or a span.
         * @returns {string} The ending of the wrapper.
         */
        postShortcode: function (tag) {

            tag = typeof tag === 'undefined' ? 'span' : tag;
            //&#8203;
            return '<span class="usl-tinymce-shortcode-wrapper-end"></span></' + tag + '>&nbsp;';
        },

        convertLiteralToRendered: function (_content, editor) {

            var regExpAllCodes = new RegExp('\\[.*?](.*?\\[\\/.*?])?', 'gi'),
                matches = _content.match(regExpAllCodes);

            if (!matches) {
                return;
            }

            USL_tinymce.loading(true);

            this.visualLoadCounter.total = matches.length;
            this.visualLoadCounter.count = 0;

            // Loop through all instances of this shortcode
            for (var i = 0; i < matches.length; i++) {

                var current_code = matches[i],
                    atts = this.getAtts(current_code),
                    _shortcode = matches[i].match(/\[(.*?)(?=\s|])/),
                    shortcode = _shortcode ? _shortcode[1] : false,
                    data = {};

                if (!shortcode) {
                    this.updateCounter();
                    continue;
                }

                // Is this a shortcode at all?
                if (!(shortcode in USL_Data.all_shortcodes)) {
                    this.updateCounter();
                    continue;
                }

                // Is this a renderable shortcode?
                if (shortcode in USL_Data.rendered_shortcodes) {

                    if (typeof USL_Data.render_data !== 'undefined') {
                        data = USL_Data.render_data;
                    }

                    data.action = 'usl_render_shortcode';
                    data.shortcode = current_code;
                    data.atts = atts;
                    data.code = shortcode;
                    data.total = matches.length;

                    $.post(
                        ajaxurl,
                        data,
                        function (response) {

                            var output = '',
                                content = editor.getContent(),
                                reG = response.shortcode,
                                tag = USL_Data.rendered_shortcodes[response.code].displayBlock ? 'div' : 'span',
                                classes = USL_Data.rendered_shortcodes[response.code].classes ?
                                    USL_Data.rendered_shortcodes[response.code].classes :
                                    '',
                                nostyle = USL_Data.rendered_shortcodes[response.code].noStyle ?
                                    USL_Data.rendered_shortcodes[response.code].noStyle :
                                    '';


                            // Escape the string for RegExp()
                            // From https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex/6969486#6969486
                            reG = reG.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");

                            output += USL_MCECallbacks.preShortcode(response.code, response.atts, tag, classes, nostyle);
                            output += response.output;
                            output += USL_MCECallbacks.postShortcode(tag);

                            editor.setContent(content.replace(new RegExp(reG, 'g'), output));

                            USL_MCECallbacks.updateCounter();
                        }
                    )
                } else {

                    var output = '',
                        content = editor.getContent();

                    // Escape the string for RegExp()
                    // From https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex/6969486#6969486
                    var reG = current_code.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
                    reG = new RegExp(reG, 'g');

                    output += USL_MCECallbacks.preShortcode(shortcode);
                    output += shortcode;
                    output += USL_MCECallbacks.postShortcode();

                    editor.setContent(content.replace(reG, output));
                    this.updateCounter();
                }
            }
        },

        convertRenderedToLiteral: function (content) {

            var regExpAllCodes = new RegExp('<(span|div)\\s*?class="usl-tinymce-shortcode-wrapper.*?(data-atts="(.*?)")?.*?data-code="(.*?)".*?(<span class="usl-tinymce-shortcode-content">(.*?)<\\/span>)?.*?usl-tinymce-shortcode-wrapper-end"><\\/span><\\/(span|div)>', 'gi'),
                matches = content.match(regExpAllCodes);

            if (!matches) {
                return content;
            }

            // Loop through all instances of this shortcode
            for (var i = 0; i < matches.length; i++) {

                var current_code = matches[i],
                    atts = this.getVisualAtts(matches[i]),
                    shortcode_content = this.getVisualContent(matches[i]),
                    code = this.getVisualCode(matches[i]),
                    output = '[' + code;

                if (atts) {
                    $.each(atts, function (name, value) {
                        output += ' ' + name + '="' + value + '"';
                    });
                }

                output += ']';

                if (shortcode_content.length) {
                    output += shortcode_content + '[/' + code + ']';
                }

                content = content.replace(new RegExp(current_code, 'g'), output);
            }

            return content;
        },

        updateCounter: function () {
            this.visualLoadCounter.count++;
            if (this.visualLoadCounter.count == this.visualLoadCounter.total) {
                USL_tinymce.loading(false);
            }
        },

        getAtts: function (content, default_args) {

            var _atts = content.match(/\s.*?(?==)/g),
                values = content.match(/"([^"]+)"/g),
                atts = {};

            // Create the key => value pairs (after trimming off whitespace from the atts, and the quotes from the values)
            if (_atts !== null) {
                for (var i2 = 0; i2 < _atts.length; i2++) {
                    atts[_atts[i2].trim()] = values[i2].substring(1, values[i2].length - 1);
                }
            }

            if (default_args) {
                $.each(default_args, function (name, value) {
                    if (!atts[name]) {
                        atts[name] = value;
                    }
                });
            }

            return atts;
        },

        getShortcodeContent: function (content) {

            var matches = content.match(/\](.*)\[/);

            return matches !== null ? matches[1] : '';
        },

        getVisualAtts: function (shortcode) {

            var atts = shortcode.match(/<span class="usl-tinymce-shortcode-atts.*?data-atts="(.*?), ".*?<\/span>/i);

            if (atts) {
                return JSON.parse('{' + atts[1].replace(/&quot;/ig, '"') + '}');
            } else {
                return false;
            }
        },

        getVisualContent: function (shortcode) {

            var content = shortcode.match(/<span class="usl-tinymce-shortcode-content.*?>(.*?)<\/span>/i);

            if (content) {
                return content[1];
            } else {
                return false;
            }
        },

        getVisualCode: function (shortcode) {
            var code = shortcode.match(/<span class="usl-tinymce-shortcode-code.*?data-code="(.*?)".*?<\/span>/i);
            return code[1];
        },

        showContent: function (content, classes) {

            var maybe_classes = classes.length ? ' ' + classes.join(' ') : '';

            return '<span class="usl-tinymce-shortcode-content' + maybe_classes + '">' + content + '</span>';
        }
    };
})(jQuery);