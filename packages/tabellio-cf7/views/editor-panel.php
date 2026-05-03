<?php
/**
 * View entry template.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Variables for the view.
 *
 * @var Page_Element       $elm              The page element instance.
 * @var \WP_Post_Type      $post_type_object The custom post type object.
 * @var \WPCF7_ContactForm $contact_form     The contact form object.
 */

$elm->h2( array(), \esc_html( $post_type_object->label ) );

$elm->fieldset(
	array( 'class' => 'tabellio-option' ),
	static fn ( $elm ) => $elm
	->legend( child: \__( 'You can edit the way you treat each submissions here.', 'tabellio-cf7' ) )

	->table(
		array( 'class' => 'form-table' ),
		static fn ( $elm ) => $elm
		->tbody(
			child: static function ( $elm ) use ( $contact_form ) {
				$option = new Option( $contact_form );

				/**
				 * Get the form fields for the submission option form.
				 *
				 * @var array<string, array{label: string, hint: string, type: string, atts: array, options: array}> $fields
				 */
				$fields = apply_filters( 'tabellio_editor_panel_options', array(), $contact_form );

				foreach ( $fields as $id => $field ) {
					$field = \wp_parse_args(
						$field,
						array(
							'label'   => '',
							'hint'    => '',
							'type'    => 'input',
							'atts'    => array(),
							'options' => array(),
						)
					);

					if ( 'separator' === $field['type'] ) {
						$elm->tr(
							child: static fn ( $elm ) => $elm
							->td(
								array(
									'colspan' => '2',
									'style'   => 'padding: 0;',
								),
								static fn ( $elm ) => $elm->hr()
							)
						);

						continue;
					}

					$field_id = sprintf( '%s-%s', Submission::MENU_SLUG, $id );

					$elm->tr(
						child: static fn ( $elm ) => $elm
						->th(
							array( 'scope' => 'row' ),
							static fn ( $elm ) => $elm
								->label( array( 'for' => $field_id ), esc_html( $field['label'] ) )
						)

						->td(
							child: static function ( $elm ) use ( $option, $id, $field, $field_id ) {
								$field_atts = \wp_parse_args(
									$field['atts'],
									array(
										'id'    => $field_id,
										'name'  => sprintf( '%s[%s]', Submission::MENU_SLUG, $id ),
										'value' => $option[ $id ],
									)
								);

								$is_select   = 'select' === $field['type'];
								$is_checkbox = 'input' === $field['type'] && 'checkbox' === $field_atts['type'];

								$selected = null;

								if ( $is_select ) {
									$selected = $field_atts['value'];
									unset( $field_atts['value'] );
								}

								if ( $is_checkbox ) {
									$field_atts['value']   = 'on';
									$field_atts['checked'] = $option[ $id ];
								}

								match ( $field['type'] ) {
									'select' => $elm->select(
										$field_atts,
										static function ( $elm ) use ( $field, $selected ) {
											$elm->option(
												array(
													'selected' => empty( $selected ),
													'value'    => '',
												),
												\__( 'None selected', 'tabellio-cf7' )
											);

											foreach ( $field['options'] as $value => $label ) {
												$value = is_int( $value ) ? $label : $value;

												$elm->option(
													array(
														'value' => \esc_attr( $value ),
														'selected' => $selected === $value,
													), \esc_html( $label )
												);
											}
										}
									),

									default => $elm->input( $field_atts ),
								};

								if ( empty( $field['hint'] ) ) {
									return;
								}

								if ( $is_checkbox ) {
									$elm->span( array(), \esc_html( $field['hint'] ) );
								} else {
									$elm->p( array( 'class' => 'description' ), \esc_html( $field['hint'] ) );
								}
							}
						)
					);
				}
			}
		)
	)
);
