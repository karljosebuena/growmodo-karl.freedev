<?php
/**
 * Dismissible announcement bar.
 *
 * Rendered visible so it still works without JavaScript. A blocking snippet in
 * inc/assets.php adds a class to <html> before first paint when the visitor has
 * already dismissed it, which hides the bar with no flash of content.
 *
 * @package Growmodo
 */

?>
<div class="announcement" id="announcement" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'growmodo' ); ?>">
	<div class="container announcement__inner">
		<?php echo growmodo_icon( 'sparkle', 'announcement__sparkle' ); ?>
		<span><?php esc_html_e( 'Discover Your Dream Property with Estatein', 'growmodo' ); ?></span>
		<a class="announcement__link" href="<?php echo esc_url( home_url( '/properties/' ) ); ?>">
			<?php esc_html_e( 'Learn More', 'growmodo' ); ?>
		</a>
	</div>
	<button class="announcement__dismiss" type="button" data-dismiss="announcement">
		<span class="screen-reader-text"><?php esc_html_e( 'Dismiss announcement', 'growmodo' ); ?></span>
		<?php echo growmodo_icon( 'close' ); ?>
	</button>
</div>
