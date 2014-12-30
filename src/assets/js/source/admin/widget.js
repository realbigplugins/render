/**
 * Functionality for using the shortcode widget.
 *
 * @since Render 1.0.0
 *
 * @global Render_Modal
 * @global Render_Data
 *
 * @package Render
 * @subpackage Widget
 */

var Render_Widget;
(function ($) {
    var active_widget, self;

    Render_Widget = {
        init: function () {

            self = this;
            this.binds();

            if (!$('body').hasClass('wp-customizer')) {
                $('.render-widget-customizer-message').show();
            }
        },

        binds: function () {

            $(document).on('click', '.render-widget-add-shortcode', function () {
                Render_Widget.open($(this));
            });

            $(document).on('render-modal-update', Render_Widget.update);

            $(document).on('render-modal-remove', Render_Widget.remove);
        },

        open: function ($e) {

            active_widget = $e.closest('.widget-content');

            var title = active_widget.find('.render-widget-shortcode-preview').html(),
                shortcode = active_widget.find('.render-widget-shortcode').val();

            if (title.toLowerCase().trim() != 'no shortcode yet') {
                Render_Modal.modify(shortcode);
            } else {
                Render_Modal.open();
            }
        },

        update: function () {

            self._change(Render_Modal.output.all, Render_Modal.output.title, 'Modify / Remove Shortcode', Render_Modal.output.title);
        },

        remove: function () {

            Render_Modal.close();
            self._change('', '', 'Add Shortcode', 'No shortcode yet');
        },

        _change: function (value, title, button_text, preview_text) {

            active_widget.find('.render-widget-add-shortcode').text(button_text);
            active_widget.find('.render-widget-shortcode-preview').text(preview_text);
            active_widget.find('.render-widget-shortcode-title').val(title);
            active_widget.find('.render-widget-shortcode').val(value);
            active_widget.find('.render-widget-shortcode').change();
        }
    };

    $(function () {

        var $body = $('body');

        // Only init on widgets page
        if ($body.hasClass('widgets-php') || $body.hasClass('wp-customizer')) {
            Render_Widget.init();
        }
    });
})(jQuery);