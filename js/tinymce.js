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
                    title: 'Shortcodes',
                    body: [{
                        type: 'container',
                        html: uslCodes()
                    },
                    {
                        type: 'textbox',
                        name: 'title',
                        label: 'Stuff'
                    }
                    ],
                    onsubmit: function( e ) {
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