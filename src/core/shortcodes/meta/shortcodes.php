<?php
/**
 * Created by PhpStorm.
 * User: kylemaurer
 * Date: 9/10/14
 * Time: 12:23 AM
 */

class USL_Meta_Shortcodes {

	/**
	 * Current Post ID
	 * @return integer
	 * @since 0.3
	 */
	public function id() {
		return get_the_ID();
	}
	/**
	 * Current Post
	 * @return object
	 * @since 0.3
	 */
	public function post() {
		return get_post( $this->id(), OBJECT );
	}

	/**
	 * Current Post Author
	 * @return string
	 * @since 0.3
	 */
	public function author() {
		return get_userdata( $this->post()->post_author )->user_nicename;
	}
	/**
	 * Current Post Title
	 * @return string
	 * @since 0.3
	 */
	public function title() {
		return $this->post()->post_title;
	}
	/**
	 * Current Post published date
	 * @return string
	 * @since 0.3
	 */
	public function published() {
		return $this->post()->post_date;
	}
	/**
	 * Current Post status
	 * @return string
	 * @since 0.3
	 */
	public function status() {
		return $this->post()->post_status;
	}
	/**
	 * Current Post type
	 * @return string
	 * @since 0.3
	 */
	public function type() {
		return $this->post()->post_type;
	}
	/**
	 * Current Post excerpt
	 * @return string
	 * @since 0.3
	 */
	public function excerpt() {
		return $this->post()->post_excerpt;
	}
	public function __construct() {
		add_usl_shortcode( 'usl_id', array( $this, 'id' ), 'Post ID', 'Displays the id of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_author', array( $this, 'author' ), 'Post Author', 'Displays the author of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_title', array( $this, 'title' ), 'Post Title', 'Displays the title of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_published', array( $this, 'published' ), 'Published Date', 'Displays the published of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_status', array( $this, 'status' ), 'Post Status', 'Displays the status of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_type', array( $this, 'type' ), 'Post Type', 'Displays the post type of the current post.', 'Meta' );
		add_usl_shortcode( 'usl_excerpt', array( $this, 'excerpt' ), 'Post Excerpt', 'Displays the excerpt of the current post.', 'Meta' );
	}
}
$uslmeta = new USL_Meta_Shortcodes();