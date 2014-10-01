(function() {
    tinymce.create('tinymce.plugins.usl', {

        init : function(ed, url) {
            ed.addButton('usl', {
                title : 'Shortcodes',
                cmd : 'usl',
                class: 'mce-usl'
            });

            ed.addCommand('usl', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '[usl]';
                ed.execCommand('mceInsertContent', 0, return_text);
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