<?php
/**
 * Home hero: headline, dual CTAs, trust stats, imagery with rotating seal.
 *
 * @package Growmodo
 */

$growmodo_stats = array(
	array( '200+', __( 'Happy Customers', 'growmodo' ) ),
	array( '10k+', __( 'Properties For Clients', 'growmodo' ) ),
	array( '16+', __( 'Years of Experience', 'growmodo' ) ),
);
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

		<div class="hero__stats">
			<?php foreach ( $growmodo_stats as $growmodo_stat ) : ?>
				<div class="stat">
					<div class="stat__value"><?php echo esc_html( $growmodo_stat[0] ); ?></div>
					<div class="stat__label"><?php echo esc_html( $growmodo_stat[1] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
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
					<path id="growmodo-seal-path" d="M70,70 m-54,0 a54,54 0 1,1 108,0 a54,54 0 1,1 -108,0" />
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
