<?php
/**
 * Home hero: headline, dual CTAs, trust stats, imagery with rotating seal.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="hero">
	<div class="hero__content">
		<h1 class="hero__title"><?php esc_html_e( 'Discover Your Dream Property with Estatein', 'growmodo' ); ?></h1>

		<p class="hero__text">
			<?php esc_html_e( 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.', 'growmodo' ); ?>
		</p>

		<div class="hero__actions">
			<a class="btn" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">
				<?php esc_html_e( 'Learn More', 'growmodo' ); ?>
			</a>
			<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
				<?php esc_html_e( 'Browse Properties', 'growmodo' ); ?>
			</a>
		</div>

		<?php growmodo_render_stats(); ?>
	</div>

	<div class="hero__media">
		<img
			src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/hero-building.webp' ); ?>"
			alt="<?php esc_attr_e( 'Modern glass high-rise apartment towers at dusk', 'growmodo' ); ?>"
			width="840"
			height="811"
			fetchpriority="high"
			decoding="async"
		/>

		<div class="hero__seal">
			<svg class="hero__seal-text" viewBox="0 0 140 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<?php /* Radius 47 keeps the ring text inside the 140-unit viewBox. */ ?>
					<path id="growmodo-seal-path" d="M70,70 m-47,0 a47,47 0 1,1 94,0 a47,47 0 1,1 -94,0" />
				</defs>
				<text>
					<textPath href="#growmodo-seal-path" startOffset="4%">
						<?php esc_html_e( 'Discover Your Dream Property', 'growmodo' ); ?>
					</textPath>
				</text>
			</svg>
			<?php echo growmodo_icon( 'arrow-up-right', 'hero__seal-arrow' ); ?>
		</div>
	</div>
</section>
