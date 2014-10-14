/**
 * Functionality for the USL tinyMCE button in the editor.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage TinyMCE
 */
(function ($) {
    tinymce.PluginManager.add('usl_button',function (editor, url) {

        // Add our shortcodes button!
        editor.addButton('usl_button', {

            // Establishes an icon class with the prefix "mce-i-"
            icon: 'usl-mce-icon',

            onclick: function () {


                // The modal
                editor.windowManager.open({
                    title: 'Something',
                    body: [
                        {
                            type: 'textbox',
                            name: 'textboxname',
                            label: 'Text Box',
                            value: '30'
                        }
                    ],
                    onsubmit: function (e) {
                        editor.insertContent(e.data.textboxname);
                    }
                });
            }
        });
    });
})(jQuery);