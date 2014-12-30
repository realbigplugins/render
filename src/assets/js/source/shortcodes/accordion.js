/**
 * The main file for Render scripts. Houses generic, broad functionality for the plugin.
 *
 * @since Render 1.0.0
 *
 * @package Render
 * @subpackage Scripts
 */
var Render_Accordion;
(function ($) {
    Render_Accordion = {
        init: function () {
            $('.render-accordion-heading').click(function () {

                var $content = $(this).siblings('.render-accordion-content'),
                    $container = $(this).closest('.render-accordion'),
                    transition = 300;

                    $container.toggleClass('open');

                if ($container.hasClass('open')) {

                    var current_height = $content.height();

                    $content.css('height', 'auto');

                    var auto_height = $content.height();

                    $content.height(current_height).animate({
                        height: auto_height
                    }, transition);
                } else {

                    $content.animate({
                        height: 0
                    }, transition);
                }
            });
        }
    };

    $(function () {
        Render_Accordion.init();
    })
})(jQuery);