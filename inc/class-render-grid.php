<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 1/7/15
 * Time: 3:51 PM
 */

/**
 * Renders all the rows on the page
 * Class Pootle_Page_Builder_Render_Grid
 */
class Pootle_Page_Builder_Render_Grid extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Render_Grid Instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Outputs the pootle page builder grids
	 * @param array $grids
	 * @param array $panels_data
	 * @param int $post_id
	 */
	protected function output_rows( $grids, $panels_data, $post_id ) {

		foreach ( $grids as $gi => $cells ) {

			echo apply_filters( 'pootlepb_before_row', '', $panels_data['grids'][ $gi ] );

			$rowID = 'pg-' . $post_id . '-' . $gi;

			$grid_classes    = apply_filters( 'pootlepb_row_classes', array( 'panel-grid' ), $panels_data['grids'][ $gi ] );
			$grid_attributes = apply_filters( 'pootlepb_row_attributes', array(
				'class' => implode( ' ', $grid_classes ),
				'id'    => $rowID
			), $panels_data['grids'][ $gi ] );

			echo '<div ';
			foreach ( $grid_attributes as $name => $value ) {
				echo $name . '="' . esc_attr( $value ) . '" ';
			}
			echo '>';

			//ROW STYLE WRAPPER
			$this->row_style_wrapper( $rowID, $gi, $cells, $panels_data );

			echo "<div class='panel-grid-cell-container'>";

			$this->output_cells( $cells, $gi, $post_id, $panels_data );

			echo "</div><!--.panel-grid-cell-container-->";
			echo '</div><!--.panel-row-style-->';
			echo '</div><!--.panel-grid-->';

			// This allows other themes and plugins to add html after the row
			echo apply_filters( 'pootlepb_after_row', '', $panels_data['grids'][ $gi ] );
		}
	}

	/**
	 * Outputs the rows style wrapper and calls pootlepb_before_cells hook
	 * @param string $rowID
	 * @param int $gi
	 * @param array $cells
	 * @param array $panels_data
	 */
	private function row_style_wrapper( $rowID, $gi, $cells, $panels_data ) {

		$style_attributes = array();
		$style_attributes['class'] = array(
			'panel-row-style',
			'panel-row-style-' . $panels_data['grids'][ $gi ]['style']['class'],
			$panels_data['grids'][ $gi ]['style']['class'],
		);

		$styleArray = ! empty( $panels_data['grids'][ $gi ]['style'] ) ? $panels_data['grids'][ $gi ]['style'] : array();
		$style_attributes = apply_filters( 'pootlepb_row_style_attributes', $style_attributes, $styleArray, $cells );

		echo '<div ';
		foreach ( $style_attributes as $name => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( " ", array_unique( $value ) );
			}
			echo $name . '="' . esc_attr( $value ) . '" ';
		}
		echo '>';

		/**
		 * Fires in pootle page builder row
		 * @hooked Pootle_Page_Builder_Render_Layout::row_bg_video
		 * @hooked Pootle_Page_Builder_Render_Layout::row_embed_css
		 */
		do_action( 'pootlepb_before_cells', $styleArray, $rowID );

	}

	private function output_cells( $cells, $gi, $post_id, $panels_data ) {

		foreach ( $cells as $ci => $widgets ) {
			// Themes can add their own styles to cells
			$cellId          = 'pgc-' . $post_id . '-' . $gi . '-' . $ci;
			$panels_data['grids'][ $gi ]['style']['col_class'] = empty( $panels_data['grids'][ $gi ]['style']['col_class'] ) ? '' : $panels_data['grids'][ $gi ]['style']['col_class'];
			$cell_classes    = apply_filters( 'pootlepb_row_cell_classes', array( 'panel-grid-cell', $panels_data['grids'][ $gi ]['style']['col_class'] ), $panels_data );
			$cell_attributes = array(
				'class' => implode( ' ', $cell_classes ),
				'id'    => $cellId
			);
			$cell_attributes = apply_filters( 'pootlepb_row_cell_attributes', $cell_attributes, $panels_data );

			echo '<div ';
			foreach ( $cell_attributes as $name => $value ) {
				echo $name . '="' . esc_attr( $value ) . '" ';
			}
			echo '>';

			foreach ( $widgets as $pi => $widget_info ) {

				/**
				 * Render the content block via this hook
				 *
				 * @param array $widget_info - Info for this block - backwards compatible with widgets
				 * @param int   $gi          - Grid Index
				 * @param int   $ci          - Cell Index
				 * @param int   $pi          - Panel/Content Block Index
				 * @param int   $blocks_num  - Total number of Blocks in cell
				 * @param int   $post_id     - The current post ID
				 * @since 0.1.0
				 */
				do_action( 'pootlepb_render_content_block', $widget_info, $gi, $ci, $pi, count( $widgets ), $post_id );
			}

			echo '</div>';
		}

	}

	/**
	 * Output row bg video
	 *
	 * @param array $style
	 * @param array $row_id
	 */
	public function row_embed_css( $style, $row_id ) {

		$row_id = '#' . $row_id;

		/** Fires in row to embed row styles */
		$embed_styles = trim( apply_filters( 'pootlepb_row_embed_style', '', $style, $row_id ) );

		if ( !empty( $embed_styles ) ) {
			echo "<style>{$embed_styles}</style>";
		}
	}

	/**
	 * Output row bg video
	 * @param array $style
	 */
	public function row_bg_video( $style ) {

		if ( ! empty( $style['bg_video'] ) && ! empty( $style['background_toggle'] ) && '.bg_video' == $style['background_toggle'] ) {

			$videoClasses = 'ppb-bg-video';

			if ( ! empty( $style['bg_mobile_image'] ) ) {
				$videoClasses .= ' hide-on-mobile';
			}
			?>
			<video class="<?php echo $videoClasses; ?>" preload="auto" autoplay="true" loop="loop" muted="muted"
			       volume="0">
				<?php
				echo "<source src='{$style['bg_video']}' type='video/mp4'>";
				echo "<source src='{$style['bg_video']}' type='video/webm'>";
				?>
				Sorry, your browser does not support HTML5 video.
			</video>
		<?php
		}
	}

	/**
	 * Adds css to cells for column gutters
	 * @param string $css
	 * @param array $style
	 * @param string $rowID
	 * @return string
	 * @since 0.1.0
	 */
	public function row_col_gutter( $css, $style, $rowID ) {

		if ( isset( $style['col_gutter'] ) && is_numeric( $style['col_gutter'] ) ) {
			$css .= $rowID . ' .panel-grid-cell { padding: 0 ' . ( $style['col_gutter']/2 ) . 'px 0; }';
		}

		return $css;
	}

	/**
	 * Sets the styles for column gutter
	 * @param string $css
	 * @param array $style
	 * @param string $rowID
	 * @return string
	 * @since 0.1.0
	 */
	public function row_overlay( $css, $style, $rowID ) {

		if ( isset( $style['background'] ) && ! empty( $style['bg_overlay_color'] ) ) {
			$overlay_color = $style['bg_overlay_color'];
			if ( ! empty( $style['bg_overlay_opacity'] ) ) {
				$overlay_color = 'rgba( ' . pootlepb_hex2rgb( $overlay_color ) . ", {$style['bg_overlay_opacity']} )";
			}
			$css .= "$rowID .panel-row-style:before { background-color: $overlay_color; }";
		}

		return $css;
	}
}

//Instantiating Pootle_Page_Builder_Render_Grid class
Pootle_Page_Builder_Render_Grid::instance();