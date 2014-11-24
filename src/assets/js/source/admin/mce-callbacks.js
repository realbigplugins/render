var USL_MCECallbacks;
(function ($) {
    var editor,
        pattern_visual_codes = /<(?:span|div).*?class="usl-tinymce-shortcode-wrapper.*?data-code="([^"]*)".*?data-atts="([^"]*)".*?usl-tinymce-shortcode-content-start"><\/span>(.*?)<span class="usl-tinymce-shortcode-content-end.*?usl-tinymce-shortcode-wrapper-end\s+\1"><\/span><\/(?:span|div)>/g;
    USL_MCECallbacks = {

        convertLiteralToRendered: function (content, editor) {

            USL_tinymce.loading(true);

            var data;

            if (typeof USL_Data.render_data !== 'undefined') {
                data = USL_Data.render_data;
            }

            data.action = 'usl_render_shortcodes';
            data.content = content;
            data.shortcode_data = USL_Data.rendered_shortcodes;

            $.post(
                ajaxurl,
                data,
                function (response) {

                    response = response.replace(/(&#8203;)+/g, "$1");
                    editor.setContent(response);
                    USL_tinymce.loading(false);
                }
            );
        },

        convertRenderedToLiteral: function (content) {

            var $container = $('<div />').append($(content));

            $container.closestChildren('.usl-tinymce-shortcode-wrapper').each(function () {
                $container = replaceShortcodes($(this), $container);
            });

            function replaceShortcodes($e, $container) {

                $e.closestChildren('.usl-tinymce-shortcode-wrapper').each(function () {
                    $container = replaceShortcodes($(this), $container);
                });

                var atts = $e.attr('data-atts'),
                    code = $e.attr('data-code'),
                    shortcode_content = $e.closestChildren('.usl-tinymce-shortcode-content').html(),
                    output = '[' + code;

                if (atts) {
                    atts = JSON.parse(atts);
                    var _atts = '';
                    $.each(atts, function (name, value) {
                        _atts += ' ' + name + '="' + value + '"';
                    });
                    output += _atts;
                }

                output += ']';

                if (shortcode_content) {
                    output += shortcode_content + '[/' + code + ']';
                }

                $e.replaceWith(output);
                return $container;
            }

            return $container.html();
        },

        _convertToRaw: function (text) {

            var result = "";
            for (var i = 0; i < text.length; i++) {
                if (text.charCodeAt(i) > 1000)
                    result += "&#" + text.charCodeAt(i) + ";";
                else
                    result += text[i];
            }

            return result;
        },

        _cleanContent: function (content) {

            content = content.replace(/&#160;/g, '&nbsp;');
            return content;
        },

        updateCounter: function () {

            this.visualLoadCounter.count++;
            if (this.visualLoadCounter.count == this.visualLoadCounter.total) {
                var content = editor.getContent({format: 'numeric'});
                editor.setContent(content.replace(/(&#8203;)+/g, '&#8203;'));
                USL_tinymce.loading(false);
            }
        },

        getLiteralAtts: function (content, default_args) {

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

        getLiteralContent: function (shortcode) {

            var matches = shortcode.match(/\](.*)\[/);

            return matches !== null ? matches[1] : false;
        },

        getLiteralCode: function (shortcode) {

            var matches = shortcode.match(/\[(.*?)(?=\s|])/);

            return matches !== null ? matches[1] : false;
        },

        getVisualAtts: function (shortcode) {

            var atts = shortcode.match(/<[span|div].*?class="usl-tinymce-shortcode-wrapper.*?data-atts="(.*?)".*?>/i);

            if (atts && atts[1]) {
                return JSON.parse(atts[1].replace(/&quot;/ig, '"'));
            } else {
                return false;
            }
        },

        getVisualContent: function (shortcode) {

            var content = shortcode.match(/<[span|div].*?class="usl-tinymce-shortcode-wrapper.*?data-content="(.*?)".*?>/i);

            if (content) {
                return content[1];
            } else {
                return false;
            }
        },

        getVisualCode: function (shortcode) {
            var code = shortcode.match(/<[span|div].*?class="usl-tinymce-shortcode-wrapper.*?data-code="(.*?)".*?>/i);
            return code[1];
        },

        showContent: function (content, classes) {

            var maybe_classes = classes.length ? ' ' + classes.join(' ') : '';

            return '<span class="usl-tinymce-shortcode-content' + maybe_classes + '">' + content + '</span>';
        }
    };

    // From https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex/6969486#6969486
    function _reGexPrepare(reG) {
        return reG.replace(/[\-\[\]\/\{}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }
})(jQuery);