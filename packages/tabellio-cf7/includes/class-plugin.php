<?php
/**
 * Plugin class.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace Tabellio_CF7;

use WPCF7_ContactForm;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @internal
 */
final class Plugin {
	/**
	 * Minimum required PHP version.
	 *
	 * @var string
	 */
	public const MINIMUM_PHP_VERSION = '8.1';

	/**
	 * Minimum required WordPress version.
	 *
	 * @var string
	 */
	public const MINIMUM_WP_VERSION = '6.0';

	/**
	 * Minimum required Contact Form 7 version.
	 *
	 * @var string
	 */
	public const MINIMUM_WPCF7_VERSION = '6.1';

	/**
	 * Perform actions on plugin activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		// Doing nothing on activation, for now.
	}

	/**
	 * Perform actions on plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Doing nothing on deactivation, for now.
	}

	/**
	 * Initialize the plugin when Contact Form 7 is ready.
	 *
	 * @return void
	 */
	public static function wpcf7_init(): void {

		/**
		 * Check if the version of Contact Form 7 in use on the site is supported by Tabellio for Contact Form 7.
		 */
		if ( self::is_unmet_cf7_requirements() ) {
			/**
			 * Display an admin notice if the Contact Form 7 version is too low.
			 */
			add_action( 'admin_notices', array( Admin_Notices::class, 'unmet_cf7_requirements' ) );

			return;
		}

		Submission::register();

		/**
		 * Override user contact meta properties.
		 *
		 * @param array $methods The user contact methods.
		 * @return array
		 */
		add_filter(
			'user_contactmethods',
			static fn ( array $methods ) => array_merge(
				array( Submission::USER_PHONE_META_KEY => __( 'Phone Number', 'tabellio-cf7' ) ),
				$methods
			),
			10,
			1
		);

		/**
		 * Enqueue admin scripts and styles.
		 */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		/**
		 * Register the submissions admin menu.
		 */
		\add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 9, 0 );

		/**
		 * Register new contact form option properties.
		 */
		\add_filter(
			'wpcf7_pre_construct_contact_form_properties',
			static fn ( array $properties ): array => array_merge(
				$properties,
				array( Option::FORM_PROP_KEY => array() )
			),
			10,
			1
		);

		/**
		 * Add a submissions panel to the contact form editor.
		 */
		\add_filter( 'wpcf7_editor_panels', array( __CLASS__, 'wpcf7_editor_panels' ) );

		/**
		 * Capture the contact form submission and store it to database before sending it.
		 */
		\add_action( 'wpcf7_before_send_mail', array( __CLASS__, 'wpcf7_before_send_mail' ) );

		/**
		 * Prepare to store option properties values.
		 */
		\add_action( 'wpcf7_save_contact_form', array( __CLASS__, 'wpcf7_save_contact_form' ), 10, 2 );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $suffix The current admin page suffix.
	 * @return void
	 */
	public static function admin_enqueue_scripts( string $suffix ): void {
		if ( ! in_array( $suffix, array( 'toplevel_page_wpcf7', 'contact_page_tabellio-cf7' ), true ) ) {
			return;
		}

		\wp_enqueue_style( 'tabellio-style', self::url( 'assets/style.css' ), array(), TABELLIO_VERSION );
	}

	/**
	 * Register the submissions admin menu.
	 *
	 * @return void
	 */
	public static function admin_menu(): void {
		$post_type_object = Submission::get_post_type_object();

		$submissions = \add_submenu_page(
			'wpcf7',
			$post_type_object->labels->items_list,
			$post_type_object->labels->menu_name,
			$post_type_object->cap->read_private_posts,
			Submission::MENU_SLUG,
			array( Submission::class, 'admin_management_page' ),
			2,
		);

		\add_action(
			'load-' . $submissions,
			array( Submission::class, 'admin_load_page' ),
			10,
			0
		);
	}

	/**
	 * Add a submissions panel to the contact form editor.
	 *
	 * @param array $panels The existing editor panels.
	 * @return array
	 */
	public static function wpcf7_editor_panels( array $panels ): array {
		$post_type_object = Submission::get_post_type_object();

		$panels[ Option::FORM_PROP_KEY ] = array(
			'title'    => $post_type_object->label,
			'callback' => array( Submission::class, 'admin_editor_panel' ),
		);

		return $panels;
	}

	/**
	 * Capture the contact form submission and store it to database before sending it.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form object.
	 * @return void
	 */
	public static function wpcf7_before_send_mail( WPCF7_ContactForm $contact_form ): void {
		$option = Option::get( $contact_form );

		if ( ! $option ) {
			return;
		}

		$form_data = $option->form_data();

		/**
		 * Action hook before saving the submission.
		 *
		 * @param array $form_data The form submission data.
		 */
		\do_action( 'tabellio_before_save', $form_data );

		$returned_id = Item::store( $contact_form, $option );

		/**
		 * Action hook after saving the submission.
		 *
		 * @param array         $form_data   The form submission data.
		 * @param int|\WP_Error $returned_id The ID of the saved submission or error.
		 */
		\do_action( 'tabellio_after_save', $form_data, $returned_id );
	}

	/**
	 * Prepare to store option properties values.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form object.
	 * @param array             $data         The form data being saved.
	 * @return void
	 */
	public static function wpcf7_save_contact_form( WPCF7_ContactForm $contact_form, array $data ): void {
		$submissions = \wp_parse_args( $data[ Submission::MENU_SLUG ] ?? array(), array() );

		$contact_form->set_properties( array( Option::FORM_PROP_KEY => $submissions ) );
	}

	/**
	 * Get the plugin directory path.
	 *
	 * @param string ...$paths Path segments to append.
	 * @return string
	 */
	public static function dir( string ...$paths ): string {
		$paths = array_merge( array( TABELLIO_PLUGIN_DIR ), $paths );

		return implode( DIRECTORY_SEPARATOR, array_filter( $paths ) );
	}

	/**
	 * Get the plugin URL.
	 *
	 * @param string ...$paths Path segments to append.
	 * @return string
	 */
	public static function url( string ...$paths ): string {
		$paths = array_merge( array( \plugin_dir_url( TABELLIO_PLUGIN_FILE ) ), $paths );

		return implode( '/', array_filter( $paths ) );
	}

	/**
	 * Check if the version of PHP in use on the site is supported.
	 *
	 * @return bool
	 */
	public static function is_unmet_php_requirements(): bool {
		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' );
	}

	/**
	 * Check if the version of WordPress in use on the site is supported.
	 *
	 * @return bool
	 */
	public static function is_unmet_wp_requirements(): bool {
		return version_compare( $GLOBALS['wp_version'], self::MINIMUM_WP_VERSION, '<' );
	}

	/**
	 * Check if the version of Contact Form 7 in use on the site is supported by Tabellio for Contact Form 7.
	 *
	 * @return bool
	 */
	public static function is_unmet_cf7_requirements(): bool {
		if ( ! defined( 'WPCF7_VERSION' ) ) {
			return false;
		}

		return version_compare( WPCF7_VERSION, self::MINIMUM_WPCF7_VERSION, '<' );
	}

	/**
	 * Check the current screen.
	 *
	 * @param array $desired_screens The desired screen IDs to check against.
	 * @return bool
	 */
	public static function is_within_screens( array $desired_screens ): bool {
		if ( ! $screen = \get_current_screen() ) {
			return false;
		}

		return in_array( $screen->id, $desired_screens, true )
			|| false !== strpos( $screen->id, 'wpcf7' );
	}

	/**
	 * Check if the version of WordPress in under debug mode.
	 *
	 * @return bool
	 */
	public static function is_debug(): bool {
		return defined( 'WP_DEBUG' ) && boolval( WP_DEBUG );
	}
}
