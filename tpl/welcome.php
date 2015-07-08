<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 19/6/15
 * Time: 11:05 PM
 * @since 0.1.0
 */
?>
<div class="wrap ppb-welcome about-wrap">

	<h1>Welcome to pootle page builder</h1>

	<div class="about-text ppb-about-text">
		Thank you for using pootle page builder. The aim of pootle page builder is to help you create compelling WordPress pages more easily. We hope you like it.
	</div>

	<div class="ppb-badge"></div>

	<p class="ppb-actions">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=page_builder_settings' ) ); ?>" class="button pootle">Settings</a>
		<a href="http://docs.pootlepress.com/" class="button pootle">Docs</a>
		<b>Version <?php echo esc_attr( POOTLEPB_VERSION ); ?></b>
	</p>

	<hr>
	<h4>How to use page builder</h4>

	<p>Page Builder is easy to use but you can check out the video below to get started.</p>

	<div class="ppb-video-container">
		<iframe src="https://player.vimeo.com/video/131757773" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> <p><a href="https://vimeo.com/131757773">Pootle Page Builder Sneak Peek 2</a> from <a href="https://vimeo.com/user8529687">pootlepress</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
	</div>

	<div class="changelog">
		<div class="feature-section col three-col">
			<div>
				<h4>Easy to use</h4>
				<p>We've designed pootle page builder to look and work as much like WordPress as possible. We've worked hard to make the easy features intuitive and the complex features straight-forward to use. Look out for tooltips (these are small question mark icons in grey than you can hover over for more information)</p>
			</div>
			<div>
				<h4>All themes</h4>
				<p>page builder works on all themes, so you can change the theme of your website but still keep using page builder!</p>
			</div>
			<div class="last-feature">
				<h4>Amazing Rows</h4>
				<p>Rows in page builder allow you to add background colors, images or autoplay videos. Row background images can have a parallax effect. They can also be full width.</p>
			</div>
		</div>
	</div>

	<hr>

	<div class="return-to-dashboard">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=page_builder_settings' ) ); ?>">Go to Page Builder Settings</a>
	</div>
</div>