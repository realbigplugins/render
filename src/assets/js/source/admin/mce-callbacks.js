var Render_MCECallbacks;
(function ($) {
    var editor,
        pattern_visual_codes = /<(?:span|div).*?class="render-tinymce-shortcode-wrapper.*?data-code="([^"]*)".*?data-atts="([^"]*)".*?render-tinymce-shortcode-content-start"><\/span>(.*?)<span class="render-tinymce-shortcode-content-end.*?render-tinymce-shortcode-wrapper-end\s+\1"><\/span><\/(?:span|div)>/g;
    Render_MCECallbacks = {

        convertLiteralToRendered: function (content, editor) {

            Render_tinymce.loading(true);

            var data;

            if (typeof Render_Data.render_data !== 'undefined') {
                data = Render_Data.render_data;
            }

            data.action = 'render_render_shortcodes';
            data.content = content;
            data.shortcode_data = Render_Data.rendered_shortcodes;

            $.post(
                ajaxurl,
                data,
                function (response) {

                    // Remove any previorendery existing dividers
                    response = response.replace(/(&#8203;)+/g, '');

                    editor.setContent(response);
                    Render_tinymce.loading(false);

                    $(document).trigger('render-tinymce-post-render');
                }
            );
        },

        convertRenderedToLiteral: function (content) {

            // FIXME Something (not necessarily here) is causing a JS error in WP JS when clicking around in the visual editor. To replicate, have sentence with button, then a column 2 with text in it.


            var $container = $('<div />').append($(content)),
                $shortcodes = $container.find('.render-tinymce-shortcode-wrapper').sortByDepth();

            $shortcodes.each(function () {

                var atts = $(this).attr('data-atts'),
                    code = $(this).attr('data-code'),
                    shortcode_content = $(this).find('.render-tinymce-shortcode-content').first().html(),
                    output = '[' + code;

                if (atts) {
                    atts = JSON.parse(atts);
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

            function replaceShortcodes($e) {

                //$e.children().each(function () {
                //    $e = replaceShortcodes($(this));
                //});

                return $e;
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

        showContent: function (content, classes) {

            var maybe_classes = classes.length ? ' ' + classes.join(' ') : '';

            return '<span class="render-tinymce-shortcode-content' + maybe_classes + '">' + content + '</span>';
        }
    };

    // From https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex/6969486#6969486
    function _reGexPrepare(reG) {
        return reG.replace(/[\-\[\]\/\{}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }
})(jQuery);