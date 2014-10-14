/**
 * Functionality for the USL tinyMCE button in the editor.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Scripts
 */
(function () {
    tinyMCE.create('tinyMCE.plugins.usl', {

        init: function (ed, url) {
            ed.addButton('usl', {
                title: 'Shortcodes',
                cmd: 'usl',
                class: 'mce-usl'
            });

            /*
             * We need to cycle through all of our shortcodes, that we've localized, and convert that into something
             * that tinymce can use to output our modal.
             */
            ed.addCommand('usl', function () {
                var body = [],
                    lists = [],
                    selected_text = tinyMCE.activeEditor.selection.getContent( {format : "text"} );

                // Sort our new array by shortcode category
                for (var i = 0; i < usl_mce_options.length; i++) {

                    // If this category does not yet exist, create it in our lists array
                    if (typeof lists[usl_mce_options[i].category] === 'undefined') {
                        lists[usl_mce_options[i].category] = [];
                    }

                    // Push on our new shortcode to the current category
                    lists[usl_mce_options[i].category].push(usl_mce_options[i]);
                }

                // Cycle through categories
                for (key in lists) {
                    if (lists.hasOwnProperty(key)) {
                        var list = {
                            type: 'listbox',
                            name: key,
                            label: key,
                            values: [
                                {
                                    text: '- Select -',
                                    value: '0'
                                }
                            ]
                        };

                        // Cycle through each categories' shortcodes
                        for (i = 0; i < lists[key].length; i++) {
                            var value_output = '';

                            // If this shortcode has the wrapping set to true, then close off the shortcode in the output
                            if ( lists[key][i].wrapping === '1' ) {
                                value_output = '[' + lists[key][i].code + ']' + selected_text + '[/' + lists[key][i].code + ']'
                            } else {
                                value_output = '[' + lists[key][i].code + ']';
                            }

                            // Create the new value (for the selectbox) and push it
                            var value = {
                                text: lists[key][i].title,
                                value: value_output
                            };

                            list.values.push(value);
                        }

                        // Push our new selectbox category to the body output
                        body.push(list);
                    }
                }

                ed.windowManager.open({

                    title: 'Shortcodes',
                    body: body,
                    onsubmit: function (e) {

                        var output = '';

                        // Cycle through all of the data and decide if it should be output or not
                        for (key in e.data) {
                            if (e.data.hasOwnProperty(key)) {

                                // If the value is '0' (not set), don't add it to the output
                                if (e.data[key] !== '0') {
                                    output += (e.data[key]);
                                }
                            }
                        }

                        // Insert our content into the editor
                        ed.insertContent(output);
                    }
                });
            });
        },

        createControl: function (n, cm) {
            return null;
        },

        getInfo: function () {
            return {
                longname: 'Ultimate Shortcodes Library',
                author: 'Kyle Maurer',
                authorurl: 'http://realbigplugins.com',
                infourl: 'http://realbigplugins.com',
                version: "0.3"
            };
        }

    });

    // Register plugin
    tinymce.PluginManager.add('usl', tinymce.plugins.usl);
})();