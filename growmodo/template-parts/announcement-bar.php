<?php
/**
 * Dismissible announcement bar.
 *
 * Rendered server-side and hidden by main.js when the visitor has already
 * dismissed it, so no layout shift occurs for first-time visitors.
 *
 * @package Growmodo
 */

?>
<div class="announcement" id="announcement" hidden>
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
