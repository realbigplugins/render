/**
 * The main file for Render scripts. Houses generic, broad functionality for the plugin.
 *
 * @since 1.0.0
 *
 * @package Render
 * @subpackage Scripts
 */
var Render;
(function ($) {
	Render = {
		init: function () {

		},
		/**
		 * Toggles the visibility of an element.
		 *
		 * @since 1.0.0
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
		Render.init();
	})
})(jQuery);