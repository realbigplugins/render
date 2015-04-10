<?php
// Exit if loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Render_Integrations
 *
 * Houses all integration information for other plugins.
 *
 * @since      1.0.0
 *
 * @package    Render
 * @subpackage Modal
 */
class Render_Integrations {

	/**
	 * An array of all Render integrations and their shortcodes.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array
	 */
	public $integrations;

	/**
	 * A curated list of all shortcodes and properties about each one.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array
	 */
	public $all_shortcodes;

	/**
	 * Initializes the class.
	 *
	 * @since {{VERSION}}
	 */
	public function __construct() {

		$this->integrations = $this->_get_integrations();
		$this->all_shortcodes = $this->_curate_shortcodes();
	}

	/**
	 * Grabs information about available Render integrations.
	 *
	 * For now, this is hard-coded data. Eventually, we will create a remote API call to home base (renderwp.com) to
	 * get the most up-to-date information.
	 *
	 * @since {{VERSION}}
	 */
	private function _get_integrations() {

		$integrations = array(
			'woocommerce' => array(
				'name' => 'woocommerce/woocommerce.php',
				'title' => 'WooCommerce',
				'link' => 'http://realbigplugins.com/plugins/render-woocommerce/',
				'supported_version' => '',
				'shortcodes' => array(
					'add_to_cart' => array(),
					'add_to_cart_url' => array(),
					'best_selling_products' => array(),
					'featured_products' => array(),
					'product' => array(),
					'product_attribute' => array(),
					'product_categories' => array(),
					'product_category' => array(),
					'product_page' => array(),
					'products' => array(),
					'recent_products' => array(),
					'related_products' => array(),
					'sale_products' => array(),
					'shop_messages' => array(
						'noDisplay' => true,
					),
					'woocommerce_messages' => array(),
					'top_rated_products' => array(),
					'woocommerce_cart' => array(),
					'woocommerce_checkout' => array(),
					'woocommerce_my_account' => array(),
					'woocommerce_order_tracking' => array(),
				),
			),
			'easydigitaldownloads' => array(
				'name' => 'easy-digital-downloads/easy-digital-downloads.php',
				'title' => 'Easy Digital Downloads',
				'link' => 'http://realbigplugins.com/plugins/render-easy-digital-downloads/',
				'supported_version' => '',
				'shortcodes' => array(
					'download_cart' => array(),
					'download_checkout' => array(),
					'download_history' => array(),
					'purchase_history' => array(),
					'download_discounts' => array(),
					'edd_profile_editor' => array(),
					'edd_login' => array(),
					'edd_register' => array(),
					'edd_price' => array(),
					'edd_receipt' => array(),
					'purchase_link' => array(),
					'purchase_collection' => array(),
					'downloads' => array(),
				),
			),
			'projectpanorama' => array(
				'name' => '',
				'title' => 'Project Panorama',
				'link' => '',
				'supported_version',
				'shortcodes' => array(
					'project_list' => array(),
					'project_status' => array(),
					'project_status_part' => array(),
					'panorama_dashboard' => array(
						'noDisplay' => true,
					),
				),
			),
		);

		return $integrations;
	}

	/**
	 * Creates a list of all shortcodes.
	 *
	 * @since {{VERSION}}
	 */
	private function _curate_shortcodes() {

		if ( empty( $this->integrations ) ) {
			return array();
		}

		$shortcodes = array();

		foreach ( $this->integrations as $plugin => $info ) {

			foreach ( $info['shortcodes'] as $code => $shortcode ) {

				$shortcodes[ $code ] = wp_parse_args( array(
					'plugin' => $plugin,
				), $shortcode );
			}
		}

		return $shortcodes;
	}
}