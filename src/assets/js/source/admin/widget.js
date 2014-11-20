/**
 * Functionality for using the shortcode widget.
 *
 * @since USL 1.0.0
 *
 * @global USL_Modal
 * @global USL_Data
 *
 * @package USL
 * @subpackage Widget
 */

var USL_Widget;
(function ($) {
    var active_widget, self;

    USL_Widget = {
        init: function () {

            self = this;
            this.binds();

            if (!$('body').hasClass('wp-customizer')) {
                $('.usl-widget-customizer-message').show();
            }
        },

        binds: function () {

            $(document).on('click', '.usl-widget-add-shortcode', function () {
                USL_Widget.open($(this));
            });

            $(document).on('usl-modal-update', USL_Widget.update);

            $(document).on('usl-modal-remove', USL_Widget.remove);
        },

        open: function ($e) {

            active_widget = $e.closest('.widget-content');

            var title = active_widget.find('.usl-widget-shortcode-preview').html(),
                shortcode = active_widget.find('.usl-widget-shortcode').val();

            if (title.toLowerCase().trim() != 'no shortcode yet') {
                USL_Modal.modify(shortcode);
            } else {
                USL_Modal.open();
            }
        },

        update: function () {

            self._change(USL_Modal.output.all, USL_Modal.output.title, 'Modify / Remove Shortcode', USL_Modal.output.title);
        },

        remove: function () {

            USL_Modal.close();
            self._change('', '', 'Add Shortcode', 'No shortcode yet');
        },

        _change: function (value, title, button_text, preview_text) {

            active_widget.find('.usl-widget-add-shortcode').text(button_text);
            active_widget.find('.usl-widget-shortcode-preview').text(preview_text);
            active_widget.find('.usl-widget-shortcode-title').val(title);
            active_widget.find('.usl-widget-shortcode').val(value);
            active_widget.find('.usl-widget-shortcode').change();
        }
    };

    $(function () {

        var $body = $('body');

        // Only init on widgets page
        if ($body.hasClass('widgets-php') || $body.hasClass('wp-customizer')) {
            USL_Widget.init();
        }
    });
})(jQuery);