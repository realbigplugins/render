/**
 * Functionality for the Render admin, globally.
 *
 * @since 1.0.0
 *
 * @global Render_Data
 *
 * @package Render
 * @subpackage Modal
 */
var Render_Admin;
(function ($) {
    Render_Admin = {
        init: function () {
            // ... get out of here!
        }
    };

    $(function () {
        Render_Admin.init();
    });
})(jQuery);

// Sort by depth plugin
jQuery.fn.sortByDepth = function () {
    var ar = this.map(function () {
            return {length: jQuery(this).parents().length, elt: this}
        }).get(),
        result = [],
        i = ar.length;


    ar.sort(function (a, b) {
        return a.length - b.length;
    });

    while (i--) {
        result.push(ar[i].elt);
    }
    return jQuery(result);
};

// Get unique array
Array.prototype.getUnique = function(){
    var u = {}, a = [];
    for(var i = 0, l = this.length; i < l; ++i){
        if(u.hasOwnProperty(this[i])) {
            continue;
        }
        a.push(this[i]);
        u[this[i]] = 1;
    }
    return a;
}