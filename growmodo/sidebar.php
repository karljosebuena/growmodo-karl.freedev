<?php
/**
 * Blog sidebar.
 *
 * Returns early when the widget area is empty so blog templates collapse to a
 * single column rather than reserving space for nothing.
 *
 * @package Growmodo
 */

if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
	return;
}
?>
<aside class="sidebar" aria-label="<?php esc_attr_e( 'Blog sidebar', 'growmodo' ); ?>">
	<?php dynamic_sidebar( 'blog-sidebar' ); ?>
</aside>
