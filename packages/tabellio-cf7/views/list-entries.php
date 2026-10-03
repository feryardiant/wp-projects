<?php
/**
 * View entry template.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

use Tabellio_CF7\List_Table;
use Tabellio_CF7\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Variables for the view.
 *
 * @var \Tabellio_CF7\Html_Element $elm The page element instance.
 * @var \WP_Post_Type $post_type_object The custom post type object.
 */

$elm->form(
	method: 'get',
	child: static fn ( $elm ) => $elm
		->input( type: 'hidden', name: 'page', value: Submission::MENU_SLUG )

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
);
