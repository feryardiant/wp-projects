<?php
/**
 * View entry template.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Variables for the view.
 *
 * @var Page_Element  $elm              The page element instance.
 * @var \WP_Post_Type $post_type_object The custom post type object.
 */

$elm->div(
	array( 'class' => 'wrap' ),
	static fn ( $elm ) => $elm
	->h1(
		array( 'class' => 'wp-heading-inline' ),
		\esc_html( $post_type_object->labels->items_list )
	)

	->hr( array( 'class' => 'wp-header-end' ) )

	->form(
		array( 'method' => 'get' ),
		static fn ( $elm ) => $elm
		->input(
			array(
				'type'  => 'hidden',
				'name'  => 'page',
				'value' => Submission::MENU_SLUG,
			)
		)

		->call(
			static function () use ( $post_type_object ) {
				$list_table = new List_Table();

				$list_table->prepare_items();

				$list_table->search_box(
					$post_type_object->labels->search_items,
					Submission::MENU_SLUG
				);

				$list_table->display();
			}
		)
	)
);
