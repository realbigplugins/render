/**
 * Accordion shortcode functionality.
 *
 * @since {{VERSION}}
 *
 * @package Render
 * @subpackage Scripts
 */
(function ($) {

	$(function () {

        // Hide accordion content (except for first)
        $('.render-accordion-wrapper').each(function () {

            var i = 0;

            $(this).find('> .render-accordion-section').each(function () {

                i++;
                if (i === 1) {
                    return true; // continue $.each
                }

                $(this).find('> .render-accordion-section-content').hide();
            });
        });

        // Toggle accordion sections
        $('.render-accordion-section-heading').click(function () {

            var visible = $(this).closest('.render-accordion-section').find('.render-accordion-section-content').is(':visible');

            // Hide all of them
            $(this).closest('.render-accordion-wrapper').find('.render-accordion-section-content').hide();

            // Reveal this one if it wasn't already visible
            if (!visible) {
                $(this).closest('.render-accordion-section').find('.render-accordion-section-content').show();
            }
        });
	})
})(jQuery);