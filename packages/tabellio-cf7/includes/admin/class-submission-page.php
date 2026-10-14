<?php
/**
 * Admin class.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

declare( strict_types = 1 );

namespace Tabellio_CF7\Admin;

use Override;
use Tabellio_CF7\Admin_Page;
use Tabellio_CF7\Html_Element;
use Tabellio_CF7\Item;
use Tabellio_CF7\List_Table;
use Tabellio_CF7\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin.
 *
 * @internal
 */
final class Submission_Page extends Admin_Page {
	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function menu(): void {
		$post_type_object = Submission::get_post_type_object();

		$page                = \add_submenu_page(
			'wpcf7',
			$post_type_object->labels->items_list,
			$post_type_object->labels->menu_name,
			$post_type_object->cap->read_private_posts,
			$this->menu_slug = Submission::MENU_SLUG,
			array( $this, 'render' ),
			2,
		);

		if ( ! $page ) {
			return;
		}

		\add_action( 'load-' . $page, array( $this, 'load' ) );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( ! in_array( $hook, array( 'toplevel_page_wpcf7', 'contact_page_tabellio-cf7' ), true ) ) {
			return;
		}

		$css    = $this->plugin->get_asset_url( 'style.css' );
		$domain = $this->plugin->get( 'text_domain' );

		\wp_enqueue_style( $domain, $css['url'], array(), $css['version'] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(): void {
		parent::load();

		$action = \wpcf7_superglobal_request( 'action', null );

		\do_action(
			'tabellio_admin_page_load',
			\wpcf7_superglobal_get( 'page' ),
			$action
		);

		if ( 'read' === $action ) {
			$id = (int) \wpcf7_superglobal_get( 'post' );

			\check_admin_referer( 'tabellio-entry_' . $id );

			$query = array();

			if ( Item::set_read_status( $id, true ) ) {
				$query['post']    = $id;
				$query['message'] = 'marked-read';
			}

			\wp_safe_redirect( $this->get_url( $query ) );

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
	 * {@inheritdoc}
	 */
	public function render(): void {
		$action  = \wpcf7_superglobal_request( 'action', null );
		$page_id = $action ?: 'list';

		Html_Element::div(
			class: 'wrap',
			id: "tabellio-submission-{$page_id}",
			child: static fn ( $elm ) => $elm
				->h1( class: 'wp-heading-inline', child: \get_admin_page_title() )
				->hr( class: 'wp-header-end' )
				->div(
					class: 'inner',
					child: static fn ( $elm ) => match ( $action ) {
						'view' => $elm->call(
							function ( $elm ) {
								$id   = \wpcf7_superglobal_request( 'post', null );
								$item = ! empty( $id ) ? new Item( absint( $id ) ) : null;

								$item?->mark_read();

								require_once TABELLIO_PLUGIN_DIR . '/views/view-entry.php';

								return $elm;
							}
						),

						default => $elm->call(
							function ( $elm ) {
								$post_type_object = \get_post_type_object( Submission::POST_TYPE );

								$elm->allow_tag( 'table' );

								require_once TABELLIO_PLUGIN_DIR . '/views/list-entries.php';

								return $elm;
							}
						)
					}
				)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function help_tabs(): array {
		return array(
			// 'blank-option-help' => array(
			// 'title'    => \__( 'Blank Help', 'blank-option' ),
			// 'content'  => $this->to_paragraphs(
			// \__( 'This is where I would provide tabbed help to the user on how everything in my admin panel works. Formatted HTML works fine in here too', 'blank-option' )
			// ),
			// 'callback' => false,
			// 'priority' => 10,
			// ),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function help_sidebar(): ?string {
		$supports = $this->plugin->get( 'supports' ) ?: array();
		$links    = array(
			sprintf( '<strong>%s</strong>', \__( 'For more information:', 'blank-option' ) ),
		);

		foreach ( $supports as $type => $url ) {
			if ( 'email' === $type ) {
				continue;
			}

			$links[] = sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				\esc_url( $url ),
				ucfirst( $type )
			);
		}

		return $this->to_paragraphs( ...$links );
	}
}
