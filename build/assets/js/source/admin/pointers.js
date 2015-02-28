/**
 * Adds WP pointers.
 *
 * @since 1.1-beta-2
 *
 * @global Render_Data
 * @global ajaxurl
 *
 * @package Render
 * @subpackage Pointers
 */
(function ($) {

    var pointers = Render_Data.pointers,
        dismissed_pointers = [];

    $(function () {

        // If no pointers, get out of here!
        if (typeof pointers == 'undefined') {
            return;
        }

        $.each(pointers, function (pointer_ID, pointer) {

            var target = pointer.target,
                title = pointer.title,
                content = pointer.content,
                position = pointer.position,
                trigger = pointer.trigger,
                classes = pointer.classes;

            // Setup content
            title = typeof title != 'undefined' ? '<h3>' + title + '</h3>' : '';
            content = typeof content != 'undefined' ? '<p>' + content + '</p>' : '';

            // Setup position
            position = typeof position != 'undefined' ? position : {
                edge: 'left',
                align: 'center'
            };

            // Setup trigger
            trigger = typeof trigger != 'undefined' ? trigger : 'render-init-pointers';

            // Setup classes
            classes = typeof classes != 'undefined' ? 'render-pointer ' + classes : 'render-pointer';

            // Launch pointer on trigger (allows pointers to be triggered by other events
            $(document).on(trigger, function (event, custom_target) {

                // Don't launch more than once
                if (dismissed_pointers.indexOf(pointer_ID) !== -1) {
                    return;
                }

                target = typeof custom_target != 'undefined' ? custom_target : target;

                $(target).pointer({
                    content: title + content,
                    position: position,
                    pointerClass: classes,
                    close: function () {

                        // Don't allow re-opening! (for pointers hooked to triggers that fire more than once on a page)
                        dismissed_pointers.push(pointer_ID);

                        // Let WP know the user has closed this pointer
                        $.post(ajaxurl, {
                            action: 'dismiss-wp-pointer',
                            pointer: 'render_' + pointer_ID
                        });
                    }
                }).pointer('open');
            });
        });

        // Launch default pointers
        $(document).trigger('render-init-pointers');
    });
})(jQuery);