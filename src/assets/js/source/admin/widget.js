/**
 * Functionality for using the shortcode widget.
 *
 * @since 1.0.0
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

        /**
         * Initializes the object.
         *
         * @since 1.0.0
         */
        init: function () {

            self = this;
            this.binds();

            if ($('body').hasClass('wp-customizer')) {
                $('.render-widget-customizer-message').hide();
            }
        },

        /**
         * Sets up handlers.
         *
         * @since 1.0.0
         */
        binds: function () {

            $(document).on('click', '.render-widget-add-shortcode', function () {
                Render_Widget.open($(this));
            });

            $(document).on('render-modal-update', Render_Widget.update);

            $(document).on('render-modal-remove', Render_Widget.remove);
        },

        /**
         * Fires when opening the Modal.
         *
         * @since 1.0.0
         *
         * @param $e The current widget.
         */
        open: function ($e) {

            active_widget = $e.closest('.widget-content');

            var shortcode = active_widget.find('.render-widget-shortcode').val();

            if (shortcode) {
                Render_Modal.modify(shortcode);
            } else {
                Render_Modal.open();
            }
        },

        /**
         * Fires when submitting the Modal.
         *
         * @since 1.0.0
         */
        update: function () {

            self.change(Render_Modal.output.all, Render_Modal.output.title, 'modify-remove', Render_Modal.output.title);
        },

        /**
         * Fires when removing the current shortcode.
         *
         * @since 1.0.0
         */
        remove: function () {

            Render_Modal.close();
            self.change('', '', 'add', false);
        },

        /**
         * Changes the shortcode in the widget.
         *
         * Changes the preview title, the button text, and the input value.
         *
         * @since 1.0.0
         *
         * @param value The shortcode to save.
         * @param title The title of the shortcode.
         * @param button Which button text to show.
         * @param preview Which preview text to show.
         */
        change: function (value, title, button, preview) {

            if (button == 'add') {
                active_widget.find('.add').show();
                active_widget.find('.modify-remove').hide();
            } else {
                active_widget.find('.modify-remove').show();
                active_widget.find('.add').hide();
            }

            if (!preview) {
                active_widget.find('.nothing-added').show();
                active_widget.find('.shortcode-title').hide();
            } else {
                active_widget.find('.shortcode-title').show().html(preview);
                active_widget.find('.nothing-added').hide();
            }

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