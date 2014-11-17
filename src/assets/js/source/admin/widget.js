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
    var active_widget;

    USL_Widget = {
        init: function () {
            this.binds();
        },

        binds: function () {
            $(document).on('click', '.usl-widget-add-shortcode', function () {
                USL_Widget.open($(this));
            });

            $(document).on('usl-modal-update', function () {
                USL_Widget.update();
            });

            $(document).on('usl-modal-remove', function () {
                USL_Widget.remove();
            });
        },

        open: function ($e) {

            var active_widget = $e.closest('.widget-content'),
                container = active_widget.find('.usl-widget-shortcode');

            if (container.text().trim().toLowerCase() !== 'no shortcode yet') {
                USL_Modal.modify(container.text());
            } else {
                USL_Modal.open();
            }
        },

        update: function () {

            active_widget.find('.usl-widget-shortcode').text(USL_Modal.output);
            active_widget.find('.usl-widget-shortcode-field').val(USL_Modal.output);
            active_widget.find('.usl-widget-add-shortcode').text('Modify / Remove Shortcode');
        },

        remove: function () {

            USL_Modal.close();

            active_widget.find('.usl-widget-shortcode').text('No shortcode yet');
            active_widget.find('.usl-widget-shortcode-field').val('');
            active_widget.find('.usl-widget-add-shortcode').text('Add Shortcode');
        }
    };

    $(function () {

        // Only init on widgets page
        if ($('body').hasClass('widgets-php')) {
            USL_Widget.init();
        }
    });
})(jQuery);