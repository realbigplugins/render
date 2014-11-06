var USL_MCECallbacks;
(function ($) {
    USL_MCECallbacks = {
        callbacks: {},

        _preShortcode: function (object) {

            var atts = '',
                shortcode = '<span class="usl-tinymce-shortcode-code" data-code="' + object.shortcode + '"></span>';

            if (object.atts) {
                atts += '<span class="usl-tinymce-shortcode-atts" data-atts=\'';
                $.each(object.atts, function (name, value, i) {
                    atts += '"' + name + '": "' + value + '", ';
                });
                atts += '\'></span>';
            }

            if (typeof object.preShortcode !== 'undefined') {
                return object.preShortcode() + atts + shortcode;
            }

            return '<span class="usl-tinymce-shortcode-wrapper">' + atts + shortcode;
        },

        _postShortcode: function (object) {

            if (typeof object.postShortcode !== 'undefined') {
                return object.postShortcode();
            }

            return '</span>';
        },

        _parseVisualContent: function (object, content) {

            // Finds ALL of the shortcodes (GI: global, ignore case)
            var reGI = new RegExp('(<p>)?(\\[' + object.shortcode + '.*].*\\[\/' + object.shortcode + '])(<\\/p>)?', 'gi');

            // Finds the first shortcode (I: ignore case)
            var reI = new RegExp('(<p>)?(\\[' + object.shortcode + '.*].*\\[\/' + object.shortcode + '])(<\\/p>)?', 'i');

            if (content.indexOf('[' + object.shortcode) !== -1) {

                var shortcodes = content.match(reGI);

                if (shortcodes.length) {
                    for (var i = 0; i < shortcodes.length; i++) {

                        var default_args = object.default_args ? object.default_args : false,
                            output = '';

                        object.atts = USL_MCECallbacks.getAtts(shortcodes[i], default_args);
                        object.shortcode_content = USL_MCECallbacks.getShortcodeContent(shortcodes[i]);
                        object.showContent = USL_MCECallbacks._showContent;

                        output += USL_MCECallbacks._preShortcode(object);
                        output += object.callback(content);
                        output += USL_MCECallbacks._postShortcode(object);

                        content = content.replace(reI, output);
                    }
                }
            }

            return content;
        },

        _parseTextContent: function (object, content) {

            // Finds ALL of the shortcodes (GI: global, ignore case)
            var reGI = new RegExp('<span class="usl-tinymce-shortcode-wrapper.*?>'
            + object.search.open + '.*?' + object.search.close + '<\/span>', 'gi');

            // Finds the first shortcode (I: ignore case)
            var reI = new RegExp('<span class="usl-tinymce-shortcode-wrapper.*?>'
            + object.search.open + '.*?' + object.search.close + '<\/span>', 'i');

            var shortcodes = content.match(reGI);

            if (shortcodes) {

                for (var i = 0; i < shortcodes.length; i++) {

                    var atts = USL_MCECallbacks._getVisualAtts(shortcodes[i]),
                        shortcode_content = USL_MCECallbacks._getVisualContent(shortcodes[i]),
                        code = USL_MCECallbacks._getVisualCode(shortcodes[i]),
                        output = '[' + code;

                    if (atts) {
                        $.each(atts, function (name, value) {
                            if (value.length) {
                                output += ' ' + name + '="' + value + '"';
                            }
                        });
                    }

                    output += ']';

                    if (shortcode_content.length) {
                        output += shortcode_content + '[/' + code + ']';
                    }

                    content = content.replace(reI, output);
                }
            }

            return content;
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

        _interaction: function (object) {

            var output = '';

            output += '<span class="usl-tinymce-shortcode-interaction">';

            if (typeof object.interaction !== 'undefined') {
                object.interaction();
            } else {

                output += '<span class="usl-tinymce-shortcode-interaction-edit"></span>';
                output += '<span class="usl-tinymce-shortcode-interaction-remove"></span>';
            }

            output += '</span>';
        },

        _getVisualAtts: function (shortcode) {
            var atts = shortcode.match(/<span class="usl-tinymce-shortcode-atts.*?data-atts="(.*?), ".*?<\/span>/i);
            return JSON.parse('{' + atts[1].replace(/&quot;/ig, '"') + '}');
        },

        _getVisualContent: function (shortcode) {
            var content = shortcode.match(/<span class="usl-tinymce-shortcode-content.*?>(.*?)<\/span>/i);
            return content[1];
        },

        _getVisualCode: function (shortcode) {
            var code = shortcode.match(/<span class="usl-tinymce-shortcode-code.*?data-code="(.*?)".*?<\/span>/i);
            return code[1];
        },

        _showContent: function (content, classes) {

            var maybe_classes = classes.length ? ' ' + classes.join(' ') : '';

            return '<span class="usl-tinymce-shortcode-content' + maybe_classes + '">' + content + '</span>';
        }
    };
})(jQuery);

USL_MCECallbacks.callbacks.button = {

    shortcode: 'usl_button',

    search: {
        open: '<a class="usl-button',
        close: '</a>'
    },

    default_args: {
        link: '#',
        size: 'medium',
        color: '#bada55',
        color_hover: '#84A347',
        font_color: '#fff',
        shape: '',
        icon: ''
    },

    callback: function (content) {

        var output;

        var button_class = '';
        button_class += this.atts.size ? '-' + this.atts.size : '';
        button_class += this.atts.shape ? '-' + this.atts.shape : '';

        output = '<a class="usl-button' + button_class + '" href="' + this.atts.link + '"';
        output += ' style="background: ' + this.atts.color + '; color: ' + this.atts.font_color + '"';
        output += '>';
        output += '<span class="hover" style="background: ' + this.atts.color_hover + '"></span>';
        output += this.atts.icon ? '<span class="icon dashicons ' + this.atts.icon + '"></span>' : '';
        output += this.showContent(this.shortcode_content, ['content']);
        output += '</a>';

        return output;
    }
};