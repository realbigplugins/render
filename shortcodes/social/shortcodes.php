<?php
/*-------------------------------
Facebook Like Button
-------------------------------*/
function usl_fb_like() {
return '<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=80&amp;appId=642951445729849" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:80px;" allowTransparency="true"></iframe>';
}
add_usl_shortcode( 'usl_fb_like', 'usl_fb_like', 'Facebook Like Button', 'Renders a perfect little Facebook Like button.', 'Social' );