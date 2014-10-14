function insertCodes() {
    /* if (document.getElementById("usl-tiny-modal")) {
     document.getElementById("usl-tiny-modal").innerHTML = "Paragraph changed.";
     } */
    var newContent = document.getElementById("stuff").innerHTML;
    return newContent;
}
(function () {
    tinymce.create('tinymce.plugins.usl', {

        init: function (ed, url) {
            ed.addButton('usl', {
                title: 'Shortcodes',
                cmd: 'usl',
                class: 'mce-usl'
            });

            ed.addCommand('usl', function () {

                var body = [
                        {
                            type: 'container',
                            html: '<div id="usl-tiny-modal"></div>'
                        }
                    ],
                    lists = [];

                // Sort our new array by shortcode category
                for (var i = 0; i < usl_mce_options.length; i++) {

                    // If this category does not yet exist, create it
                    if (typeof lists[usl_mce_options[i].Category] === 'undefined') {
                        lists[usl_mce_options[i].Category] = [];
                    }

                    // Push on our new shortcode to the current category
                    lists[usl_mce_options[i].Category].push(usl_mce_options[i]);
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

                            if ( lists[key][i].Wrapping === '1' ) {
                                value_output = '[' + lists[key][i].Code + '][/' + lists[key][i].Code + ']'
                            } else {
                                value_output = '[' + lists[key][i].Code + ']';
                            }
                            var value = {
                                text: lists[key][i].Title,
                                value: value_output
                            };

                            list.values.push(value);
                        }

                        body.push(list);
                    }
                }

                console.log(body);

                ed.windowManager.open({

                    title: 'Shortcodes',
                    body: body,
                    onsubmit: function (e) {
                        var output = '';
                        for (key in e.data) {
                            if (e.data.hasOwnProperty(key)) {
                                console.log(e.data[key]);
                                if (e.data[key] !== '0') {
                                    output += (e.data[key]);
                                }
                            }
                        }

                        console.log(output);

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