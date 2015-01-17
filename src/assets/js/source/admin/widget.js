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

    // FIXME Translation ready
var Render_Widget;
(function ($) {
    var active_widget, self;

    Render_Widget = {
        init: function () {

            self = this;
            this.binds();

            if ($('body').hasClass('wp-customizer')) {
                $('.render-widget-customizer-message').hide();
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

            var shortcode = active_widget.find('.render-widget-shortcode').val();

            if (shortcode) {
                Render_Modal.modify(shortcode);
            } else {
                Render_Modal.open();
            }
        },

        update: function () {

            // TODO Make this translatable
            self.change(Render_Modal.output.all, Render_Modal.output.title, 'modify-remove', Render_Modal.output.title);
        },

        remove: function () {

            Render_Modal.close();
            // TODO Make this translatable
            self.change('', '', 'add', false);
        },

        change: function (value, title, button_text, preview_text) {

            if (button_text == 'add') {
                active_widget.find('.add').show();
                active_widget.find('.modify-remove').hide();
            } else {
                active_widget.find('.modify-remove').show();
                active_widget.find('.add').hide();
            }

            if (!preview_text) {
                active_widget.find('.nothing-added').show();
                active_widget.find('.shortcode-title').hide();
            } else {
                active_widget.find('.shortcode-title').show().html(preview_text);
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