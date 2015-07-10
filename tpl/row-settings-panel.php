<?php
/**
 * Created by shramee
 * At: 7:59 PM 8/7/15
 */

global $pootlepb_row_settings_tabs;

/**
 * Add your own tabs to Content block editor panel
 *	'tabName' => array(
 *		'label' => 'Tab Name',
 *		'priority' => 10,
 *	),
 */
$pootlepb_row_settings_tabs = apply_filters( 'pootlepb_row_settings_tabs', $pootlepb_row_settings_tabs );
$panel_tabs = array();

foreach ( $pootlepb_row_settings_tabs as $k => $tab ) {
	if ( empty( $tab['priority'] ) ) {
		$tab['priority'] = 10;
	}
	$panel_tabs[ $tab['priority'] ][ $k ] = $tab;
}

ksort( $panel_tabs );
?>
<div class="ppb-cool-panel-wrap">
	<ul class="ppb-acp-sidebar">
		<?php
		//Output the tabs
		foreach ( $panel_tabs as $tabs ) {
			foreach ( $tabs as $k => $tab ) {
				//Separator
				if ( empty( $tab['label'] ) ) {
					echo '<li class="ppb-separator"></li>';
					continue;
				}
				?>
				<li>
					<a href="#pootle-<?php echo $k; ?>-tab">
						<?php echo $tab['label']; ?>
					</a>
				</li>
			<?php
			}
		}
		?>
	</ul>

	<?php
	//Output the tab panels
	foreach ( $panel_tabs as $tabs ) {
		foreach ( $tabs as $k => $tab ) {
			if ( empty( $tab['label'] ) ) { continue; }
			if ( empty( $tab['class'] ) ) { $tab['class'] = ''; }
			?>
			<div id="pootle-<?php echo $k; ?>-tab" class="ppb-style-section <?php echo $tab['class']; ?>">

				<?php
				do_action( 'pootlepb_row_settings_' . $k . '_tab' );
				pootlepb_row_dialog_fields_output( $k );
				?>

			</div>
		<?php
		}
	}
	?>

</div>
