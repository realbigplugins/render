/**
 * Adds WP pointers.
 *
 * @since {{VERSION}}
 *
 * @global Render_Data
 * @global ajaxurl
 *
 * @package Render
 * @subpackage Pointers
 */
(function ($) {

    var render_data = Render_Data;

    $(function () {

        // If no pointers, get out of here!
        if (typeof render_data.pointers == 'undefined') {
            return;
        }

        var pointers = render_data.pointers;

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

                target = typeof custom_target != 'undefined' ? custom_target : target;

                $(target).pointer({
                    content: title + content,
                    position: position,
                    pointerClass: classes,
                    close: function () {
                        $.post(ajaxurl, {
                            action: 'dismiss-wp-pointer',
                            pointer: 'render_' + pointer_ID
                        });
                    }
                }).pointer('open');
            });
        });

        $(document).trigger('render-init-pointers');
    });
})(jQuery);