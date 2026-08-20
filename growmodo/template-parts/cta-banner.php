<?php
/**
 * Pre-footer call-to-action banner, shared by every page.
 *
 * @package Growmodo
 */

?>
<section class="cta">
	<div class="container cta__inner">
		<div>
			<h2 class="cta__title"><?php esc_html_e( 'Start Your Real Estate Journey Today', 'growmodo' ); ?></h2>
			<p class="cta__text">
				<?php esc_html_e( 'Your dream property is just a click away. Whether you\'re looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.', 'growmodo' ); ?>
			</p>
		</div>
		<div class="cta__action">
			<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
				<?php esc_html_e( 'Explore Properties', 'growmodo' ); ?>
			</a>
		</div>
	</div>
</section>
