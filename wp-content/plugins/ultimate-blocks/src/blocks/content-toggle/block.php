<?php
/**
 * Content toggle main block file.
 *
 * @package Ultimate_Blocks
 */

/**
 * Enqueue frontend script for content toggle block
 *
 * @return void
 */
function ub_content_toggle_add_frontend_assets() {
	require_once dirname( dirname( __DIR__ ) ) . '/common.php';

	$presentBlocks = ub_getPresentBlocks();

	$firstInstanceDetected = false;

	foreach ( $presentBlocks as $block ) {
		if ( $block['blockName'] === 'ub/content-toggle' || $block['blockName'] === 'ub/content-toggle-panel'
			|| $block['blockName'] === 'ub/content-toggle-block' || $block['blockName'] === 'ub/content-toggle-panel-block' ) {
			if ( ! $firstInstanceDetected ) {
				wp_enqueue_script(
					'ultimate_blocks-content-toggle-front-script',
					plugins_url( 'content-toggle/front.build.js', __DIR__ ),
					array(),
					Ultimate_Blocks_Constants::plugin_version(),
					true
				);
				$firstInstanceDetected = true;
			}

			if ( is_singular() && isset( $block['attrs']['hasFAQSchema'] ) ) {
				add_action( 'wp_footer', 'ub_merge_faqpages', 80 );
				break;
			}
		}
	}
}

if ( ! class_exists( 'ub_simple_html_dom_node' ) ) {
	require dirname( dirname( __DIR__ ) ) . '/simple_html_dom.php';
}

function ub_render_content_toggle_block( $attributes, $content ) {
	extract( $attributes );

	return '<div class="wp-block-ub-content-toggle' . ( isset( $className ) ? ' ' .  $className  : '' )
			. (isset($align) ? " align". $align : "") . '" ' . ( $blockID === '' ? '' : 'id="ub-content-toggle-' . esc_attr($blockID) . '"' ) .
			( $preventCollapse ? ' data-preventcollapse="true"' : '' ) .
			( $showOnlyOne ? ' data-showonlyone="true"' : '' ) . ' data-mobilecollapse="' . json_encode( $collapsedOnMobile ) . '" data-desktopcollapse="' . json_encode( $collapsed ) . '">'
			. $content . '</div>';
}

function ub_render_content_toggle_panel_block( $attributes, $content, $block_object ) {
	$classNamePrefix = 'wp-block-ub-content-toggle';
	extract( $attributes );
	$border_class = $border ? '' : 'no-border ';
	$icons        = json_decode( file_get_contents( __DIR__ . '/icons/icons.json' ) );
	$icon_class   = $icons->$toggleIcon;

	$block_context = $block_object->context;

	if ( isset( $block_context['parentID'] ) ) {
		$parentID = $block_context['parentID'];
	}
	$should_collapsed = $collapsed && ! $defaultOpen;

	if (!in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'])) {
		$titleTag = 'p';
	}

	return '<div ' . ( $toggleID === '' ? '' : 'id="' . esc_attr($toggleID) . '" ' ) . 'class="' . $border_class . $classNamePrefix . '-accordion' . ( isset( $className ) ? ' ' .  $className  : '' ) . '"'
			. ( $parentID === '' ? ' style="border-color: ' . esc_attr($theme) . ';"' : '' ) . '>
                <div class="' . $classNamePrefix . '-accordion-title-wrap"'
			. ( $parentID === '' ? ' style="background-color: ' . esc_attr($theme) . ';"' : '' ) . ( $preventCollapse ? ' aria-disabled="true"' : '' )
			.  '" aria-controls="ub-content-toggle-panel-' . esc_attr($index) . '-' . esc_attr($parentID) . '" tabindex="0">
                    <' . esc_attr($titleTag) . ' class="' . $classNamePrefix . '-accordion-title ub-content-toggle-title-' . esc_attr($parentID) . '"'
			. ( $parentID === '' ? ' style="color:' . esc_attr($titleColor) . ';"' : '' ) . '>' . wp_kses_post($panelTitle) . '</' . esc_attr($titleTag) . '>' .
			( $toggleIcon === 'none' ? '' : '<div class="' . $classNamePrefix . '-accordion-toggle-wrap ' .  esc_attr($toggleLocation)  .
											'"><span class="' . $classNamePrefix . '-accordion-state-indicator ' . esc_attr($icon_class) .
											( $should_collapsed ? '' : ' open' ) . '"></span>
                    </div>' ) .
			'</div><div role="region" '. 'aria-expanded="'. (json_encode(! $should_collapsed)) .'" class="' . $classNamePrefix . '-accordion-content-wrap' .
			( $should_collapsed ? ' ub-hide' : '' ) . '" id="ub-content-toggle-panel-' . esc_attr($index) . '-' . esc_attr($parentID) . '">' . $content
			. '</div></div>';
}

function ub_register_content_toggle_panel_block() {
	if ( function_exists( 'register_block_type_from_metadata' ) ) {
		require dirname( dirname( __DIR__ ) ) . '/defaults.php';
		register_block_type_from_metadata( dirname(dirname(dirname(__DIR__))) . '/dist/blocks/content-toggle/components/panel/block.json', array(
			'attributes'      => $defaultValues['ub/content-toggle-panel-block']['attributes'],
			'render_callback' => 'ub_render_content_toggle_panel_block'
		) );
	}
}

function ub_register_content_toggle_block() {
	if ( function_exists( 'register_block_type_from_metadata' ) ) {
		require dirname( dirname( __DIR__ ) ) . '/defaults.php';
		register_block_type_from_metadata( dirname(dirname(dirname(__DIR__))) . '/dist/blocks/content-toggle/block.json',
			array(
				'attributes'      => $defaultValues['ub/content-toggle-block']['attributes'],
				'render_callback' => 'ub_render_content_toggle_block',
			)
		);
	}
}

add_action( 'init', 'ub_register_content_toggle_block' );

add_action( 'init', 'ub_register_content_toggle_panel_block' );

add_action( 'wp_enqueue_scripts', 'ub_content_toggle_add_frontend_assets' );

add_filter( 'render_block', 'ub_content_toggle_filter', 10, 3 );

function ub_faq_questions( $qna = array() ) {
	static $parsed_qna = array();

	if ( ! isset( $qna ) ) {
		$parsed_qna = array();
	}

	if ( empty( $qna ) ) {
		return $parsed_qna;
	} else {
		$current_qna = array();

		$current  = array_map(
			function ( $item ) {
				return json_encode( $item );
			},
			$parsed_qna
		);
		$newItems = array_map(
			function ( $item ) {
				return json_encode( $item );
			},
			$qna
		);

		foreach ( $newItems as $index => $item ) {
			if ( ! in_array( $item, $current ) ) {
				array_push( $current_qna, $qna[ $index ] );
			}
		}
		$parsed_qna = array_merge( $parsed_qna, $current_qna );

		return true;
	}
}

function ub_content_toggle_filter( $block_content, $block ) {

	if ( 'ub/content-toggle-block' !== $block['blockName'] ) {
		return $block_content;
	}

	$output = $block_content;

	if ( isset( $block['attrs']['hasFAQSchema'] ) ) {
		$parsedBlockContent = ub_str_get_html(
			preg_replace(
				'/^<div class="wp-block-ub-content-toggle(?: [^>]*)?" id="ub-content-toggle-.*?">/',
				'<div class="toggleroot">',
				$block_content
			)
		);
		$panel = array();

		if( !empty($parsedBlockContent) && gettype($parsedBlockContent) !== "boolean" ){
			$panel = $parsedBlockContent->find( '.toggleroot>.wp-block-ub-content-toggle-accordion>.wp-block-ub-content-toggle-accordion-content-wrap' );
		}
		foreach ( $panel as $elem ) {
			// look for possible nested content toggles and remove existing ones
			foreach ( $elem->find( '.wp-block-ub-content-toggle' ) as $nestedToggle ) {
				$nestedToggle->outertext = '';
			}
			foreach ( $elem->find( 'script[type="application/ld+json"]' ) as $nestedToggle ) {
				$nestedToggle->outertext = '';
			}
		}

		$panel = array_map(
			function ( $elem ) {
				return $elem->innertext;
			},
			$panel
		);

		$questions = array();

		foreach ( $block['innerBlocks'] as $key => $togglePanel ) {
			if ( isset( $panel[ $key ] ) ) {
				$answer = preg_replace_callback(
					'/<([a-z1-6]+)[^>]*?>[^<]*?<\/(\1)>/i',
					function ( $matches ) {
						return ( in_array(
							$matches[1],
							array(
								'script',
								'svg',
								'iframe',
								'applet',
								'map',
								'audio',
								'button',
								'table',
								'datalist',
								'form',
								'frameset',
								'select',
								'optgroup',
								'picture',
								'style',
								'video',
							)
						) ? '' : $matches[0] );
					},
					$panel[ $key ]
				);

				$answer = preg_replace_callback(
					'/<\/?([a-z1-6]+).*?\/?>/i',
					function ( $matches ) {
						if ( in_array(
							$matches[1],
							array(
								'h1',
								'h2',
								'h3',
								'h4',
								'h5',
								'h6',
								'a',
								'br',
								'ol',
								'ul',
								'li',
								'p',
								'div',
								'b',
								'strong',
								'i',
								'em',
								'u',
								'del',
							)
						) ) {
							return $matches[0];
						} else {
							$replacement = '';
							if ( $matches[1] === 'ins' ) {
								$replacement = 'u';
							} elseif ( $matches[1] === 'big' ) {
								$replacement = 'strong';
							} elseif ( $matches[1] === 'q' ) {
								$replacement = 'p';
							} elseif ( $matches[1] === 'dir' ) {
								$replacement = 'ul';
							} elseif ( $matches[1] === 'address' || $matches[1] === 'cite' ) {
								$replacement = 'em';
							} elseif ( in_array(
								$matches[1],
								array(
									'article',
									'aside',
									'blockquote',
									'details',
									'dialog',
									'figure',
									'figcaption',
									'footer',
									'header',
									'nav',
									'pre',
									'section',
									'textarea',
								)
							) ) {
								$replacement = 'div';
							}

							return ( $replacement === '' ? '' : str_replace( $matches[1], $replacement, $matches[0] ) );
						}
					},
					$answer
				);

				while ( preg_match_all(
					'/<([a-z1-6]+)[^>]*?><\/(\1)>/i',
					$answer
				) > 0 ) { // remove empty tags and tags that only contain empty tags
					$answer = preg_replace( '/<([a-z1-6]+)[^>]*?><\/(\1)>/i', '', $answer );
				}

				// check all attributes

				$answer = preg_replace_callback(
					'/<[a-z1-6]+( (?:(?:aria|data)-[^\t\n\f \/>"\'=]+|[a-z]+)=[\'"][\s\S]+?[\'"])>/i',
					function ( $matches ) {
						$attributeList = preg_replace_callback(
							'/ ([\S]+)=([\'"])([\s\S]*?)(\2)/',
							function ( $matches ) {
								return $matches[1] === 'href' ? ( " href='" . $matches[3] . "'" ) : '';
							},
							$matches[1]
						);

						return str_replace( $matches[1], $attributeList, $matches[0] );
					},
					$answer
				);

				if ( $answer !== '' && array_key_exists(
					'panelTitle',
					$togglePanel['attrs']
				) && $togglePanel['attrs']['panelTitle'] !== '' ) { // blank answers and questions are invalid

					array_push(
						$questions,
						array(
							'@type'          => 'Question',
							'name'           => wp_filter_nohtml_kses( $togglePanel['attrs']['panelTitle'] ),
							'acceptedAnswer' => array(
								'@type' => 'Answer',
								'text'  => trim( str_replace( '"', '\"', $answer ) ),
							),
						)
					);
				}
			}
		}
		ub_faq_questions( $questions );
	}

	return $output;
}

function ub_merge_faqpages() {
	?>
	<?php
	echo '<script type="application/ld+json">{
            "@context":"http://schema.org/",
            "@type":"FAQPage",
            "mainEntity": ' . json_encode( ub_faq_questions(), JSON_UNESCAPED_SLASHES ) . '}</script>';
	?>
	<?php
}

/**
 * Add extra context information for content toggle panel.
 *
 * @param array    $current_context current context of target block.
 * @param array    $current_block block info array.
 * @param WP_Block $parent_block parent block.
 *
 * @return array block context
 */
function content_toggle_panel_context( $current_context, $current_block, $parent_block = null ) {
	$target_content_toggle_parts = array( 'ub/content-toggle-panel-block', 'ub/content-toggle-panel' );

	if ( in_array( $current_block['blockName'], $target_content_toggle_parts, true ) ) {
		if ( ! isset( $current_context['parentID'] ) && ! is_null( $parent_block ) ) {
			$current_context['parentID'] = $parent_block->parsed_block['attrs']['blockID'];
		}
	}

	return $current_context;
}

add_filter( 'render_block_context', 'content_toggle_panel_context', 10, 3 );
