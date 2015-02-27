/**
 * Tabs shortcode functionality.
 *
 * @since 1.1-beta-2
 *
 * @package Render
 * @subpackage Scripts
 */
(function ($) {

	$(function () {

        // Hide tab content (except for first)
        $('.render-tabs-section-wrapper').each(function () {

            var i = 0;

            $(this).find('> .render-tab-section').each(function () {

                i++;
                if (i === 1) {
                    return true; // continue $.each
                }

                $(this).hide();
            });
        });

        // Toggle tab sections
        $('.render-tabs-navigation-tab').click(function () {

            var index = $(this).index(),
                $wrapper = $(this).closest('.render-tabs-wrapper');

            // Hide all of them
            $wrapper.find('.render-tab-section').hide();

            // Remove active from all of them
            $wrapper.find('.render-tabs-navigation-tab.render-tabs-navigation-tab-active')
                .removeClass('render-tabs-navigation-tab-active');

            // Reveal this one if it wasn't already visible
            $wrapper.find('.render-tab-section:eq(' + index + ')').show();

            // Add active to the current navigation tab
            $wrapper.find('.render-tabs-navigation-tab:eq(' + index + ')').addClass('render-tabs-navigation-tab-active');
        });
	})
})(jQuery);