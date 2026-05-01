<?php
/**
 * Entry Manager for Contact Form 7
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Entry Manager for Contact Form 7
 * Description: Never lose a lead again. Save, manage, and convert every Contact Form 7 submission directly in your WordPress dashboard.
 * Text Domain: cf7-entry-manager
 * Domain Path: /languages
 * Version: 0.1.0
 * Tested up to: 6.9
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: Fery Wardiyanto
 * Author URI: https://feryardiant.id
 * License: GPLv3 or later
 * Requires Plugins: contact-form-7
 */

use CF7_Entry_Manager\Item;
use CF7_Entry_Manager\Option;
use CF7_Entry_Manager\Submission;

defined( 'ABSPATH' ) || exit;

define( 'CF7EM_VERSION', '0.1.0' );
define( 'CF7EM_DEBUG', defined( 'WP_DEBUG' ) && boolval( WP_DEBUG ) );
define( 'CF7EM_PLUGIN_DIR', __DIR__ );

define( 'CF7EM__MINIMUM_WP_VERSION', '6.0' );
define( 'CF7EM__MINIMUM_WPCF7_VERSION', '6.1' );
define( 'CF7EM__MINIMUM_PHP_VERSION', '8.1' );

/**
 * Check if the version of WordPress in use on the site is supported by Entry Manager for Contact Form 7.
 */
if ( version_compare( PHP_VERSION, CF7EM__MINIMUM_PHP_VERSION, '<' ) ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-error is-dismissible"><p>';

			// phpcs:disable WordPress.Security.EscapeOutput
			printf(
				/* translators: %s: version of PHP required by Entry Manager for Contact Form 7 plugin. */
				__( 'Entry <strong>Manager for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>PHP</strong> and has been paused.', 'cf7-entry-manager' ),
				CF7EM__MINIMUM_PHP_VERSION
			);
			// phpcs:enable

			echo '</p></div>';
		}
	);

	return;
}

/**
 * Check if the version of WordPress in use on the site is supported by Entry Manager for Contact Form 7.
 */
if ( version_compare( $GLOBALS['wp_version'], CF7EM__MINIMUM_WP_VERSION, '<' ) ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-error is-dismissible"><p>';

			// phpcs:disable WordPress.Security.EscapeOutput
			printf(
				/* translators: %s: version of WordPress required by Entry Manager for Contact Form 7 plugin. */
				__( 'Entry <strong>Manager for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>WordPress</strong> and has been paused.', 'cf7-entry-manager' ),
				CF7EM__MINIMUM_WP_VERSION
			);
			// phpcs:enable

			echo '</p></div>';
		}
	);

	return;
}

register_activation_hook(
	__FILE__,
	static function () {
		// Doing nothing on activation.
	}
);

register_deactivation_hook(
	__FILE__,
	static function () {
		// Doing nothing on deactivation.
	}
);

add_action(
	'admin_enqueue_scripts',
	static function ( string $suffix ) {
		if ( ! in_array( $suffix, array( 'toplevel_page_wpcf7', 'contact_page_cf7-entry-manager' ), true ) ) {
			return;
		}

		wp_enqueue_style( 'cf7-entry-manager-style', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), CF7EM_VERSION );
	},
	10,
	1
);

add_action(
	'wpcf7_init',
	static function () {
		/**
		 * Check if the version of Contact Form 7 in use on the site is supported by Entry Manager for Contact Form 7.
		 */
		if ( version_compare( WPCF7_VERSION, CF7EM__MINIMUM_WPCF7_VERSION, '<' ) ) {
			add_action(
				'admin_notices',
				static function () {
					echo '<div class="notice notice-error is-dismissible"><p>';

					// phpcs:disable WordPress.Security.EscapeOutput
					printf(
						/* translators: %s: version of Contact Form 7 required by Entry Manager for Contact Form 7 plugin. */
						__( 'Entry <strong>Manager for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>Contact Form 7</strong> and has been paused.', 'cf7-entry-manager' ),
						CF7EM__MINIMUM_WPCF7_VERSION
					);
					// phpcs:enable

					echo '</p></div>';
				}
			);

			return;
		}

		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';
		}

		require_once __DIR__ . '/includes/class-item.php';
		require_once __DIR__ . '/includes/class-page-element.php';
		require_once __DIR__ . '/includes/class-list-table.php';
		require_once __DIR__ . '/includes/class-option.php';
		require_once __DIR__ . '/includes/class-submission.php';

		Submission::register();

		/**
		 * Override user contact meta properties.
		 */
		add_filter(
			'user_contactmethods',
			static fn ( array $methods ) => array_merge(
				array(
					'user_phone' => __( 'Phone Number', 'cf7-entry-manager' ),
				),
				$methods
			),
			10,
			1
		);

		/**
		 * Register the submissions admin menu.
		 */
		\add_action( 'admin_menu', array( Submission::class, 'admin_menu' ), 9, 0 );
	}
);

/**
 * Capture the contact form submission and store it to database before sending it.
 */
\add_action(
	'wpcf7_before_send_mail',
	static function ( WPCF7_ContactForm $contact_form ): void {
		$option = Option::get( $contact_form );

		if ( ! $option ) {
			return;
		}

		$form_data = $option->form_data();

		\do_action( 'cf7em_before_save', $form_data );

		$returned_id = Item::store( $contact_form, $option );

		\do_action( 'cf7em_after_save', $form_data, $returned_id );
	},
	10,
	1
);

/**
 * Prepare to store option properties values.
 */
\add_action(
	'wpcf7_save_contact_form',
	static function ( WPCF7_ContactForm $contact_form, array $data ): void {
		$submissions = \wp_parse_args( $data[ Submission::MENU_SLUG ], array() );

		$contact_form->set_properties( array( 'submissions' => $submissions ) );
	},
	10,
	2
);

/**
 * Register new contact form option properties.
 */
\add_filter(
	'wpcf7_pre_construct_contact_form_properties',
	static fn ( array $properties ) => array_merge(
		$properties,
		array( 'submissions' => array() )
	),
	10,
	1
);

/**
 * Add a submissions panel to the contact form editor.
 */
\add_filter(
	'wpcf7_editor_panels',
	static function ( array $panels ): array {
		$post_type_object = Submission::get_post_type_object();

		$panels['submissions'] = array(
			'title'    => $post_type_object->label,
			'callback' => array( Submission::class, 'admin_editor_panel' ),
		);

		return $panels;
	},
	10,
	1
);
