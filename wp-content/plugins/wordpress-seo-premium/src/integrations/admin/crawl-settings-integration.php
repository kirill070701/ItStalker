<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use WPSEO_Option;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Form;

/**
 * Crawl_Settings_Integration class
 */
class Crawl_Settings_Integration implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * In this case: when on an admin page.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Registers an action to add a new tab to the General page.
	 */
	public function register_hooks() {
		\add_action( 'wpseo_settings_tab_crawl_cleanup', [ $this, 'add_crawl_settings_tab_content' ] );
		\add_action( 'wpseo_settings_tab_crawl_cleanup_network', [ $this, 'add_crawl_settings_tab_content_network' ] );
	}

	/**
	 * Adds content to the Crawl Cleanup tab.
	 *
	 * @param Yoast_Form $yform The yoast form object.
	 */
	public function add_crawl_settings_tab_content( $yform ) {
		$yform->toggle_switch(
			'remove_feed_post_comments',
			[

				'off' => __( 'Keep', 'wordpress-seo-premium' ),
				'on'  => __( 'Remove', 'wordpress-seo-premium' ),
			],
			__( 'Post comment feeds', 'wordpress-seo-premium' )
		);
	}

	/**
	 * Adds content to the Crawl Cleanup network tab.
	 *
	 * @param Yoast_Form $yform The yoast form object.
	 */
	public function add_crawl_settings_tab_content_network( $yform ) {
		$yform->toggle_switch(
			WPSEO_Option::ALLOW_KEY_PREFIX . 'remove_feed_post_comments',
			[
				// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Reason: text is originally from Yoast SEO.
				'on'  => __( 'Allow Control', 'wordpress-seo' ),
				// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Reason: text is originally from Yoast SEO.
				'off' => __( 'Disable', 'wordpress-seo' ),
			],
			__( 'Post comment feeds', 'wordpress-seo-premium' )
		);
	}
}
