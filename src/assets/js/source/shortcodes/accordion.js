/**
 * The main file for USL scripts. Houses generic, broad functionality for the plugin.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Scripts
 */
var USL_Accordion;
(function ($) {
    USL_Accordion = {
        init: function () {
alert ('ya!');
            $('.usl-accordion-heading').click(function () {

                var $content = $(this).siblings('.usl-accordion-content'),
                    $container = $(this).closest('.usl-accordion'),
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
        USL_Accordion.init();
    })
})(jQuery);