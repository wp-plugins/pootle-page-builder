<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 1/7/15
 * Time: 11:49 AM
 */

/**
 * Class Pootle_Page_Builder_Custom_Styles
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Custom_Styles {
	/**
	 * @var Pootle_Page_Builder_Custom_Styles
	 * @since 0.1.0
	 */
	protected static $instance;

	/** @var string Current bg type video */
	protected $row_bg_type;

	/**
	 * Magic __construct
	 * $since 1.0.0
	 * @since 0.1.0
	 */
	public function __construct() {

		/* Add style attributes */
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_style_vars' ), 5, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_border' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_bg_color' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_bg_image' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_full_width' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_bg_parallax' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_height' ), 10, 3 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_hide_row' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_bg_vid_css' ), 10, 2 );
		add_filter( 'pootlepb_row_style_attributes', array( $this, 'row_inline_css' ), 10, 2 );
	}

	/**
	 * Initiates vars and properties for row styling
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_style_vars( $attr, $style ) {

		//Setting row bg type property
		$this->row_bg_type = '.bg_image';
		if ( isset( $style['background_toggle'] ) ) {
			$this->row_bg_type = $style['background_toggle'];
		}

		//Init style
		$attr['style'] = '';

		return $attr;
	}

	/**
	 * Set's row background color
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_bg_color( $attr, $style ) {

		if ( ! empty( $style['background'] ) ) {
			$attr['style'] .= 'background-color: ' . $style['background'] . ';';
		}

		return $attr;
	}

	/**
	 * Set's row border
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_border( $attr, $style ) {

		if ( ! empty( $attr['top_border_height'] ) ) {
			$attr['style'] .= 'border-top: ' . $style['top_border_height'] . 'px solid ' . $style['top_border'] . '; ';
		}
		if ( ! empty( $style['bottom_border_height'] ) ) {
			$attr['style'] .= 'border-bottom: ' . $style['bottom_border_height'] . 'px solid ' . $style['bottom_border'] . '; ';
		}

		return $attr;
	}

	/**
	 * Set's row background image
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_bg_image( $attr, $style ) {

		if ( '.bg_image' != $this->row_bg_type ) {
			return $attr;
		}

		if ( ! empty( $style['background_image'] ) ) {
			$attr['style'] .= 'background-image: url( ' . esc_url( $style['background_image'] ) . ' ); ';
			$attr = $this->row_bg_img_size( $attr, $style );
			$attr = $this->row_bg_img_repeat( $attr, $style );
		}

		return $attr;
	}

	/**
	 * Set's row bg image repeat
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_bg_img_repeat( $attr, $style ) {

		$repeat = 'no-repeat';

		if ( ! empty( $style['background_image_repeat'] ) ) {
			$repeat = 'repeat';
		}

		$attr['style'] .= "background-repeat: $repeat; ";

		return $attr;
	}

	/**
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_bg_img_size( $attr, $style ) {

		if ( ! empty( $style['background_image_size'] ) ) {
			$attr['style'] .= 'background-size: ' . $style['background_image_size'] . '; ';
		}

		return $attr;
	}

	/**
	 * Row full width class
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function row_full_width( $attr, $style ) {

		if ( ! empty( $style['full_width'] ) ) {
			$attr['class'][] = 'ppb-full-width-row';
			$attr['class'][] = 'ppb-full-width-no-bg';
		}

		return $attr;
	}

	/**
	 * Row bg parallax class
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function row_bg_parallax( $attr, $style ) {

		if ( '.bg_image' != $this->row_bg_type ) {
			return $attr;
		}

		if ( ! empty( $style['background_parallax'] ) ) {
			$attr['class'][] = 'ppb-parallax';
		}

		return $attr;
	}

	/**
	 * Row bg video class and video mobile image
	 *
	 * @param array $attr
	 * @param array $style
	 * @param array $cells
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function row_height( $attr, $style, $cells = array() ) {

		$row_empty = ! $this->row_has_content( $cells );

		if ( $row_empty ) {
			if ( ! empty( $style['row_height'] ) ) {
				$attr['style'] .= 'height:' . $style['row_height'] . 'px;';
			}
		}

		return $attr;
	}

	/**
	 * Return true if row contains content blocks in any cell
	 * @param array $cells Cells of the row to search for content blocks in
	 * @return bool
	 */
	protected function row_has_content( $cells ) {

		//Loop through the cells
		foreach ( $cells as $cell ) {

			//If cell contains content blocks
			if ( ! empty( $cell ) ) {
				return true;
			}
		}

		//No content blocks found in the cells of the row
		return false;
	}

	/**
	 * Row bg video class and video mobile image
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function row_hide_row( $attr, $style ) {

		if ( ! empty( $style['hide_row'] ) ) {
			$attr['style'] .= 'display:none;';
		}

		return $attr;
	}

	/**
	 * Row bg video class and video mobile image
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_bg_vid_css( $attr, $style ) {

		if ( '.bg_video' == $this->row_bg_type ) {

			$attr['class'][] = 'video-bg';

			if ( ! empty( $style['bg_mobile_image'] ) ) {
				$attr['style'] .= 'background: url( ' . esc_url( $style['bg_mobile_image'] ) . ' ) center/cover; ';
			}
		}

		return $attr;
	}

	/**
	 * Row bg video class and video mobile image
	 *
	 * @param array $attr
	 * @param array $style
	 *
	 * @return array
	 */
	public function row_inline_css( $attr, $style ) {

		if ( ! empty( $style['style'] ) ) {
			$attr['style'] .= preg_replace( "/\r|\n/", ';', $style['style'] );;
		}

		return $attr;
	}
}

/** @var Pootle_Page_Builder_Custom_Styles Instance */
$GLOBALS['Pootle_Page_Builder_Custom_Styles'] = new Pootle_Page_Builder_Custom_Styles();