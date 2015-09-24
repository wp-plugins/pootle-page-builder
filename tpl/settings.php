<?php
/**
 * Settings page template
 * @author pootlepress
 * @since 0.1.0
 */
?>

<div class="wrap">
	<h2>Pootle Page Builder</h2>
	<?php settings_errors(); ?>

	<?php
	/** @var string $active_tab Current tab */
	$active_tab = 'general';
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	} // end if
	?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=page_builder_settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
		<a href="?page=page_builder_settings&tab=addons" class="nav-tab <?php echo $active_tab == 'addons' ? 'nav-tab-active' : ''; ?>">Add-on Licence Keys</a>
	</h2>

	<?php
		if ( $active_tab == 'addons' ) {
			require POOTLEPB_DIR . "tpl/addon-keys.php";
		} else {
			?>
			<form action='options.php' method="POST">
				<?php
				do_settings_sections( 'pootlepage-display' );
				settings_fields( 'pootlepage-display' );
				submit_button();
				?>
			</form>
			<?php
		}
	?>
</div>