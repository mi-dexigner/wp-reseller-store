<?php
/**
 * GoDaddy Reseller Store display class.
 *
 * Handles the GoDaddy Reseller Store apperance.
 *
 * @class    Reseller_Store/Display
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Display {

	/**
	 * Class constructor.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

		add_action( 'enqueue_embed_scripts', [ $this, 'wp_enqueue_scripts' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @action wp_enqueue_scripts
	 * @since  0.2.0
	 */
	public function wp_enqueue_scripts() {

		$rtl = is_rtl() ? '-rtl' : '';

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'reseller-store-css', Plugin::assets_url( "css/store{$rtl}{$suffix}.css" ), [ 'dashicons' ], rstore()->version );

		wp_enqueue_script( 'js-cookie', Plugin::assets_url( "js/js-cookie{$suffix}.js" ), [], '2.1.3', true );
		wp_enqueue_script( 'reseller-store-js', Plugin::assets_url( "js/store{$suffix}.js" ), [ 'jquery', 'js-cookie' ], rstore()->version, true );
		wp_enqueue_script( 'reseller-store-domain-js', Plugin::assets_url( 'js/domain-search.min.js' ), [], rstore()->version, true );

		$data = [
			'pl_id'   => (int) rstore_get_option( 'pl_id' ),
			'urls'    => [
				'cart_api' => esc_url_raw( rstore()->api->url( 'api', 'cart/{pl_id}' ) ),
				'gui'      => rstore()->api->url( 'gui' ),
			],
			'cookies' => [
				'shopperId' => 'ShopperId' . rstore_get_option( 'pl_id' ),
			],
			'product' => [
				'id' => ( Post_Type::SLUG === get_post_type() ) ? rstore_get_product_meta( get_the_ID(), 'id', '' ) : '',
			],
			'i18n'    => [
				'error' => esc_html__( 'An error has occurred', 'reseller-store' ),
			],
		];

		wp_localize_script( 'reseller-store-js', 'rstore', $data );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  2.0.5
	 */
	public function admin_enqueue_scripts() {

		if ( rstore_is_admin_uri( 'post_type=' . Post_Type::SLUG, false ) ) {

			$rtl = is_rtl() ? '-rtl' : '';

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'reseller-store-admin-css', Plugin::assets_url( "css/admin{$rtl}{$suffix}.css" ), [], rstore()->version );

		}
	}

}
