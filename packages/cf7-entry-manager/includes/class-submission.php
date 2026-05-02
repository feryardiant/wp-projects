<?php
/**
 * Item class.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

use WPCF7_ContactForm;

/**
 * Class Submission.
 */
final class Submission {
	/**
	 * Custom post type slug for submissions.
	 *
	 * @var string
	 */
	public const POST_TYPE = 'cf7em-submission';

	/**
	 * Menu slug for the submissions admin page.
	 *
	 * @var string
	 */
	public const MENU_SLUG = 'cf7-entry-manager';

	/**
	 * Meta key for the user phone field.
	 *
	 * @var string
	 */
	public const USER_PHONE_META_KEY = '_cf7em_user_phone';

	/**
	 * Register the submissions custom post type.
	 *
	 * @return void
	 */
	public static function register() {
		$labels = array(
			'name'                  => __( 'Submissions', 'cf7-entry-manager' ),
			'singular_name'         => __( 'Submission', 'cf7-entry-manager' ),
			'view_item'             => __( 'View Submission', 'cf7-entry-manager' ),
			'search_items'          => __( 'Search Submissions', 'cf7-entry-manager' ),
			'not_found'             => __( 'No submissions found.', 'cf7-entry-manager' ),
			'not_found_in_trash'    => __( 'No submissions found in Trash.', 'cf7-entry-manager' ),
			'filter_items_list'     => _x(
				'Filter submissions list',
				'Screen reader text for the filter links heading on the post type listing screen.',
				'cf7-entry-manager'
			),
			'items_list_navigation' => _x(
				'Submissions list navigation',
				'Screen reader text for the pagination heading on the post type listing screen.',
				'cf7-entry-manager'
			),
			'items_list'            => _x(
				'Submissions list',
				'Screen reader text for the items list heading on the post type listing screen.',
				'cf7-entry-manager'
			),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'            => $labels,
				'description'       => __( 'List of form submissions.', 'cf7-entry-manager' ),
				'public'            => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,
				'capability_type'   => 'post',
				// Map everything to CF7 capabilities.
				'capabilities'      => array(
					'edit_post'          => 'wpcf7_edit_contact_form',   // Meta cap.
					'read_post'          => 'wpcf7_read_contact_form',   // Meta cap.
					'delete_post'        => 'wpcf7_delete_contact_form', // Meta cap.
					'edit_posts'         => 'wpcf7_edit_contact_forms',
					'edit_others_posts'  => 'wpcf7_edit_contact_forms',
					'publish_posts'      => 'wpcf7_edit_contact_forms',
					'read_private_posts' => 'wpcf7_read_contact_forms',
					'create_posts'       => 'do_not_allow', // Prevents manual creation in admin.
				),
				'map_meta_cap'      => true, // Essential for the mapping above to work.
				'hierarchical'      => false,
				'supports'          => array( 'title', 'excerpt', 'author', 'custom-fields' ),
				'rewrite'           => array( 'slug' => 'submission' ),
				'query_var'         => true,
				'menu_icon'         => 'dashicons-email-alt',
				// 'register_meta_box_cb' => static function( WP_Post $post ) {
				// Doing nothing for now.
				// },
			)
		);
	}

	/**
	 * Load the submissions admin page.
	 *
	 * @internal
	 * @return void
	 */
	public static function admin_load_page(): void {
		$action = \wpcf7_superglobal_request( 'action', null );

		\do_action(
			'cf7em_admin_page_load',
			\wpcf7_superglobal_get( 'page' ),
			$action
		);

		if ( 'read' === $action ) {
			$id = (int) \wpcf7_superglobal_get( 'post' );

			\check_admin_referer( 'cf7em-entry_' . $id );

			$query = array();

			if ( Item::set_read_status( $id, true ) ) {
				$query['post']    = $id;
				$query['message'] = 'marked-read';
			}

			\wp_safe_redirect( self::admin_menu_url( $query ) );

			exit();
		}

		$screen = \get_current_screen();

		\add_filter(
			'manage_' . $screen->id . '_columns',
			array( List_Table::class, 'define_column' ),
			10,
			1
		);
	}

	/**
	 * Render the submissions panel for the contact form editor.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form.
	 * @internal
	 * @return void
	 */
	public static function admin_editor_panel( WPCF7_ContactForm $contact_form ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$post_type_object = self::get_post_type_object();

		$elm = new Page_Element(
			array(
				'allowed_html' => array(
					'form' => array( 'method' => true ),
				),
			)
		);

		require_once CF7EM_PLUGIN_DIR . '/views/editor-panel.php';

		$elm->render();
	}

	/**
	 * Render the submissions admin management page.
	 *
	 * @internal
	 * @return void
	 */
	public static function admin_management_page(): void {
		$action = \wpcf7_superglobal_request( 'action', null );
		$item   = \wpcf7_superglobal_request( 'post', null );

		if ( 'view' === $action && $item ) {
			$item = new Item( absint( $item ) );
			$elm  = new Page_Element(
				array(
					'allowed_html' => array(
						'form' => array(
							'method'   => true,
							'action'   => true,
							'id'       => true,
							'class'    => true,
							'disabled' => true,
						),
					),
				)
			);

			$item->mark_read();

			require_once CF7EM_PLUGIN_DIR . '/views/view-entry.php';

			$elm->render();

			return;
		}

		$post_type_object = self::get_post_type_object();

		$elm = new Page_Element(
			array(
				'allowed_html' => array(
					'form' => array( 'method' => true ),
				),
			)
		);

		require_once CF7EM_PLUGIN_DIR . '/views/list-entries.php';

		$elm->render();
	}

	/**
	 * Generate the admin URL for the submissions page.
	 *
	 * @param array $query The query arguments to add to the URL.
	 * @return string The generated URL.
	 */
	public static function admin_menu_url( array $query ): string {
		return \add_query_arg(
			$query,
			\menu_page_url( self::MENU_SLUG, false )
		);
	}

	/**
	 * Retrieve custom post type object for form submission.
	 *
	 * @return \WP_Post_Type|null The post type object or null if not registered.
	 */
	public static function get_post_type_object() {
		return \get_post_type_object( self::POST_TYPE );
	}

	/**
	 * Constructor.
	 */
	private function __construct() {}
}
