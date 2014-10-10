function insertCodes() {
    if (document.getElementById("usl-tiny-modal")) {
    document.getElementById("usl-tiny-modal").innerHTML = "Paragraph changed.";
    }
}
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
                    file: url + '/form.html',
                    width : 450 + parseInt(ed.getLang('example.delta_width', 0)),
                    height : 450 + parseInt(ed.getLang('example.delta_height', 0)),
                    inline: 1,
                    title: 'Shortcodes',
                    body: [{
                        type: 'container',
                        html: '<div id="usl-tiny-modal"></div>'
                    },
                    {
                        type: 'container',
                        html: insertCodes()
                    },
                    {
                        type: 'textbox',
                        name: 'title',
                        label: 'Stuff'
                    }],
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