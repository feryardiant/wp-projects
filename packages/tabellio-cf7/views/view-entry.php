<?php
/**
 * View entry template.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variables for the view.
 *
 * @var \Tabellio_CF7\Item $item The submission item instance.
 * @var \Tabellio_CF7\Html_Element $elm The page element instance.
 */

$elm->form(
	method: 'post',
	action: $item->url(),
	id: 'wpcf7-admin-form-element',
	disabled: ! $item->current_user_can( 'wpcf7_edit_contact_form' ),
	child: static fn ( $elm ) => $elm
		->call(
			function () use ( $item ) {
				if ( $item->current_user_can( 'wpcf7_edit_contact_form' ) ) {
					wp_nonce_field( 'wpcf7-save-submission-entry_' . $item->id );
				}
			}
		)

		->input( type: 'hidden', id: 'post_ID', name: 'post_ID', value: $item->id, )
		->input( type: 'hidden', id: 'hiddenaction', name: 'action', value: 'save', )

		->div(
			id: 'poststuff',
			child: static fn ( $elm ) => $elm
			->div(
				id: 'post-body',
				class: 'metabox-holder columns-2 wp-clearfix',
				child: static fn ( $elm ) => $elm

				->div(
					id: 'postbox-container-1',
					class: 'postbox-container',
					child: static fn ( $elm ) => $elm
					->section(
						id: 'tabellio-info',
						class: 'tabellio-box postbox',
						child: static fn ( $elm ) => $elm
						->header(
							class: 'postbox-header',
							child: static fn ( $elm ) => $elm
							->h2( child: \__( 'Info', 'tabellio-cf7' ) )
							->div(
								class: 'handle-actions hide-if-no-js',
								child: static fn ( $elm ) => $elm
								// Nothing for now.
							), // .handle-actions
						) // .postbox-header

						->div(
							class: 'inside',
							child: static fn ( $elm ) => $elm
							->div(
								class: 'tabellio-row tabellio-info',
								child: static fn ( $elm ) => $elm
								->div(
									class: 'tabellio-col tabellio-info-field',
									child: static fn ( $elm ) => $elm
									->p( child: \__( 'Submitted', 'tabellio-cf7' ) )
								)
								->div(
									class: 'tabellio-col tabellio-info-value',
									child: static fn ( $elm ) => $elm
									->p( child: esc_html( $item->datetime?->format( 'Y-m-d H:i:s' ) ) )
								)
							) // .tabellio-row

							->div(
								class: 'tabellio-row tabellio-info',
								child: static fn ( $elm ) => $elm
								->div(
									class: 'tabellio-col tabellio-info-field',
									child: static fn ( $elm ) => $elm
									->p( child: \__( 'Form', 'tabellio-cf7' ) )
								)
								->div(
									class: 'tabellio-col tabellio-info-value ' . ( $item->form_id ? '' : 'tabellio-no-value' ),
									child: static fn ( $elm ) => $elm
									->p(
										child: ( $form = $item->form() ) ? esc_html( $form->title() ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no form', 'tabellio-cf7' )
										)
									)
								)
							) // .tabellio-row

							->div(
								class: 'tabellio-row tabellio-info',
								child: static fn ( $elm ) => $elm
								->div(
									class: 'tabellio-col tabellio-info-field',
									child: static fn ( $elm ) => $elm
									->img(
										class: 'avatar photo',
										src: get_avatar_url( $item->author_id ),
										loading: 'lazy',
									)
								) // .tabellio-info-field

								->div(
									class: 'tabellio-col tabellio-info-value ' . ( $item->author_id ? '' : 'tabellio-no-value' ),
									child: static fn ( $elm ) => $elm
									->p(
										class: $item->author_name ? '' : 'tabellio-no-value',
										child: $item->author_name ? esc_html( $item->author_name ) : sprintf(
											'<span aria-hidden="true">%s</span><span class="screen-reader-text">(%s)</span>',
											\__( 'Anonymous', 'tabellio-cf7' ),
											\__( 'no author info', 'tabellio-cf7' )
										)
									)
									->p(
										class: $item->author_email ? '' : 'tabellio-no-value',
										child: $item->author_email ? esc_html( $item->author_email ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no email info', 'tabellio-cf7' )
										)
									)
									->p(
										class: $item->author_phone ? '' : 'tabellio-no-value',
										child: $item->author_phone ? esc_html( $item->author_phone ) : sprintf(
											'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
											\__( 'no phone info', 'tabellio-cf7' )
										)
									)
								) // .tabellio-info-value
							) // .tabellio-row
						), // .inside
					) // #tabellio-info
				) // #postbox-container-1

				->div(
					id: 'postbox-container-2',
					class: 'postbox-container',
					child: static fn ( $elm ) => $elm
					->section(
						id: 'tabellio-entry',
						class: 'tabellio-box postbox',
						child: static fn ( $elm ) => $elm
						->header(
							class: 'postbox-header',
							child: static fn ( $elm ) => $elm
								->h2( child: \__( 'Submission Entry', 'tabellio-cf7' ) )
								->div(
									array( 'class' => 'handle-actions hide-if-no-js' ),
									child: static fn ( $elm ) => $elm
										// Nothing for now.
								), // .handle-actions
						) // .postbox-header

						->div(
							class: 'inside',
							child: static function ( $elm ) use ( $item ) {
								/**
								 * Form tag object.
								 *
								 * @var \WPCF7_FormTag $tag
								 */
								foreach ( $item->form()->scan_form_tags() as $tag ) {
									if ( 'submit' === $tag->basetype ) {
										continue;
									}

									$value     = $item->submission[ $tag->name ] ?? '';
									$has_value = '' !== $value && null !== $value;

									$elm->div(
										class: 'tabellio-row tabellio-submission ' . ( $has_value ? 'field-answered' : 'field-no-answer' ),
										child: static fn ( $elm ) => $elm
											->div(
												class: 'tabellio-col tabellio-submission-field',
												child: static fn ( $elm ) => $elm->p( child: esc_html( $tag->name ) )
											) // .tabellio-submission-field

											->div(
												class: "tabellio-col tabellio-submission-value tabellio-type-{$tag->basetype}",
												child: static fn ( $elm ) => match ( $tag->basetype ) {
													'tel' => $elm->p(
														child: static fn ( $elm ) => $elm
														->a( array( 'href' => 'tel:' . esc_attr( $value ) ), esc_html( $value ) )
													),

													'email' => $elm->p(
														child: static fn ( $elm ) => $elm
															->a( href: 'mailto:' . $value, child: esc_html( $value ) )
													),

													'select', 'checkbox', 'radio' => $elm->ol(
														child: static function ( $elm ) use ( $tag, $value ) {
															foreach ( $tag->values as $i => $option ) {
																$elm->li(
																	class: ( $value === $option ) ? 'selected' : '',
																	child: esc_html( $option )
																);
															}
														}
													),

													'file' => $elm->p(
														child: esc_html( $has_value ? $value : \__( 'No file uploaded', 'tabellio-cf7' ) )
													),

													'acceptance' => $elm->p(
														child: boolval( $value )
															? \__( 'Accepted', 'tabellio-cf7' )
															: \__( 'Not accepted', 'tabellio-cf7' )
													),

													default => $elm->p( child: $has_value ? $value : \__( 'No answer', 'tabellio-cf7' ) ),
												}
											) // .tabellio-submission-value

											->div(
												class: 'tabellio-col tabellio-submission-info',
												child: static function ( $elm ) use ( $tag ) {
													if ( ! empty( $tag->options ) ) {
														return $elm->span(
															class: 'tabellio-submission-option',
															child: static function ( $elm ) use ( $tag ) {
																$options = array_reduce(
																	$tag->options,
																	static function ( $carry, $option ) {
																		if ( ! str_contains( $option, ':' ) ) {
																			if ( 'optional' !== $option ) {
																				$carry[] = $option;
																			}

																			return $carry;
																		}

																		list( $key, $value ) = explode( ':', $option );

																		$carry[] = sprintf( '%s: %s', $key, $value );

																		return $carry;
																	},
																	array()
																);

																if ( ! str_contains( $tag->type, '*' ) ) {
																	array_unshift( $options, 'optional' );
																}

																$elm->p(
																	child: sprintf(
																		/* translators: %s: comma-separated list of form tag options */
																		esc_html__( 'Options: %s', 'tabellio-cf7' ),
																		implode( ', ', $options )
																	)
																);
															}
														);
													}

													if ( 'quiz' === $tag->basetype ) {
														return $elm
															->p( child: \__( 'Questions', 'tabellio-cf7' ) )
															->ol(
																child: static function ( $elm ) use ( $tag ) {
																	foreach ( $tag->raw_values as $i => $option ) {
																		list( $question, $answer ) = array_map( 'trim', explode( '|', $option ) );

																		$elm->li(
																			child: static fn ( $elm ) => $elm
																			->span( child: sprintf( '%s %s', $question, $answer ) )
																		);
																	}
																}
															);
													}

													if ( ! empty( $tag->content ) ) {
														return $elm
															->p( class: 'tabellio-submission-content', child: $tag->content );
													}
												}
											) // .tabellio-submission-info
									); // .tabellio-submission
								}
							}
						) // .inside
					) // #tabellio-entry
				) // #postbox-container-2
			) // #post-body

			->clear()
		) // #poststuff
); // #wpcf7-admin-form-element
