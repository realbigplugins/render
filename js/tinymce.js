(function() {
    tinymce.create('tinymce.plugins.usl', {

        init : function(ed, url) {
            ed.addButton('usl', {
                title : 'Shortcodes',
                cmd : 'usl',
                class: 'mce-usl'
            });

            ed.addCommand('usl', function() {
                ed.windowManager.open( {
                    title: 'Insert h3 tag',
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Your title'
                    },
                    {
                    type: 'container',
                    html: 'Test'
                    }
                    ],
                    onsubmit: function( e ) {
                        console.log(usl_mce_options);
                        ed.insertContent( '<h3>' + e.data.title + '</h3>');
                    }
                });
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : 'Ultimate Shortcodes Library',
                author : 'Kyle Maurer',
                authorurl : 'http://realbigplugins.com',
                infourl : 'http://realbigplugins.com',
                version : "0.3"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'usl', tinymce.plugins.usl );
})();