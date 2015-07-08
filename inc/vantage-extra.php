<?php
/**
 * Add row styles.
 *
 * @param $styles
 *
 * @return mixed
 * @since 0.1.0
 */
function pootlepb_panels_row_styles( $styles ) {
	$styles['wide-grey'] = __( 'Wide Grey', 'vantage' );

	return $styles;
}
add_filter( 'pootlepb_row_styles', 'pootlepb_panels_row_styles' );

/**
 * Returns content block styling fields
 * @return array Style fields
 * @since 0.1.0
 */
function pootlepb_block_styling_fields() {

	global $pootlepb_content_block_styling_fields;
	return $pootlepb_content_block_styling_fields;
}
add_filter( 'pootlepb_row_style_fields', 'pootlepb_row_style_fields' );

function pootlepb_panels_panels_row_attributes( $attr, $row ) {
	if ( ! empty( $row['style']['no_margin'] ) ) {
		if ( empty( $attr['style'] ) ) {
			$attr['style'] = '';
		}

		$attr['style'] .= 'margin-bottom: 0px;';

	} else {
		if ( empty( $attr['style'] ) ) {
			$attr['style'] = '';
		}

		$marginBottom = pootlepb_settings( 'margin-bottom' );
		if ( ! empty( $row['style']['margin_bottom'] ) || '0' === ( $row['style']['margin_bottom'] ) ) {
			$attr['style'] .= "margin-bottom: {$row['style']['margin_bottom']}px;";
		} elseif ( $marginBottom ) {
			$attr['style'] .= "margin-bottom: {$marginBottom}px;";
		} else {
			$attr['style'] .= 'margin-bottom: 0;';
		}

	}

	if ( isset( $row['style']['id'] ) && ! empty( $row['style']['id'] ) ) {
		$attr['id'] = $row['style']['id'];
	}

	return $attr;
}
add_filter( 'pootlepb_row_attributes', 'pootlepb_panels_panels_row_attributes', 10, 2 );
