/**
 * The main file for USL scripts. Houses generic, broad functionality for the plugin.
 *
 * @since USL 1.0.0
 *
 * @package USL
 * @subpackage Scripts
 */
var USL;
(function ($) {
	USL = {
		init: function () {

		},
		/**
		 * Toggles the visibility of an element.
		 *
		 * @since USL 1.0.0
		 *
		 * @param id The ID of the node to target.
		 */
		toggle_visibility: function (id) {
			var e = document.getElementById(id);
			if(e.style.display == 'block')
				e.style.display = 'none';
			else
				e.style.display = 'block';
		}
	};

	$(function () {
		USL.init();
	})
})(jQuery);