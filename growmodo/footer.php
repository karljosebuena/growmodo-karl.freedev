<?php
/**
 * Site footer: brand, footer nav, legal bar.
 *
 * The newsletter form and link columns land with the shared-shell phase.
 *
 * @package Growmodo
 */

?>
</main>

<footer class="site-footer">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
		</div>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'growmodo' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>

	<div class="site-footer__legal">
		<div class="container">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All Rights Reserved.', 'growmodo' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
