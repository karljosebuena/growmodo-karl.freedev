<?php
/**
 * Site footer: brand + newsletter, sitemap columns, socials, legal bar.
 *
 * The sitemap columns mirror the Figma exactly and are defined here rather
 * than as five menu locations: they are structural to the design, not
 * content an editor is expected to re-order (see docs/roadmap.md triage).
 *
 * @package Growmodo
 */

$growmodo_footer_columns = array(
	__( 'Home', 'growmodo' )       => array(
		__( 'Hero Section', 'growmodo' ) => home_url( '/#main' ),
		__( 'Features', 'growmodo' )     => home_url( '/#features' ),
		__( 'Properties', 'growmodo' )   => home_url( '/#properties' ),
		__( 'Testimonials', 'growmodo' ) => home_url( '/#testimonials' ),
		__( 'FAQ\'s', 'growmodo' )       => home_url( '/#faq' ),
	),
	__( 'About Us', 'growmodo' )   => array(
		__( 'Our Story', 'growmodo' )    => home_url( '/about-us/#story' ),
		__( 'Our Works', 'growmodo' )    => home_url( '/about-us/#works' ),
		__( 'How It Works', 'growmodo' ) => home_url( '/about-us/#process' ),
		__( 'Our Team', 'growmodo' )     => home_url( '/about-us/#team' ),
		__( 'Our Clients', 'growmodo' )  => home_url( '/about-us/#clients' ),

		/*
		 * The Figma has no blog screen, so the header nav is left exactly as
		 * designed and the posts page is reachable from here instead.
		 */
		__( 'Insights', 'growmodo' )     => get_permalink( (int) get_option( 'page_for_posts' ) ),
	),
	__( 'Properties', 'growmodo' ) => array(
		__( 'Portfolio', 'growmodo' )  => get_post_type_archive_link( 'property' ),
		__( 'Categories', 'growmodo' ) => get_post_type_archive_link( 'property' ),
	),
	__( 'Services', 'growmodo' )   => array(
		__( 'Valuation Mastery', 'growmodo' )    => home_url( '/services/#valuation' ),
		__( 'Strategic Marketing', 'growmodo' )  => home_url( '/services/#marketing' ),
		__( 'Negotiation Wizardry', 'growmodo' ) => home_url( '/services/#negotiation' ),
		__( 'Closing Success', 'growmodo' )      => home_url( '/services/#closing' ),
		__( 'Property Management', 'growmodo' )  => home_url( '/services/#management' ),
	),
	__( 'Contact Us', 'growmodo' ) => array(
		__( 'Contact Form', 'growmodo' ) => home_url( '/contact/#contact-form' ),
		__( 'Our Offices', 'growmodo' )  => home_url( '/contact/#offices' ),
	),
);

$growmodo_socials = array(
	'facebook' => 'https://facebook.com/',
	'linkedin' => 'https://linkedin.com/',
	'twitter'  => 'https://x.com/',
	'youtube'  => 'https://youtube.com/',
);
?>

<?php get_template_part( 'template-parts/cta-banner' ); ?>
</main>

<footer class="site-footer">
	<div class="container site-footer__top">
		<div class="site-footer__brand-col">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php echo growmodo_icon( 'logo', 'brand__mark' ); ?>
				<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			</a>

			<form class="newsletter" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="growmodo_form" />
				<input type="hidden" name="growmodo_form_type" value="newsletter" />
				<?php wp_nonce_field( 'growmodo_form', 'growmodo_nonce' ); ?>

				<p class="form__honeypot" aria-hidden="true">
					<label for="newsletter-website"><?php esc_html_e( 'Website', 'growmodo' ); ?></label>
					<input type="text" id="newsletter-website" name="growmodo_website" tabindex="-1" autocomplete="off" />
				</p>

				<label class="screen-reader-text" for="newsletter-email"><?php esc_html_e( 'Your email address', 'growmodo' ); ?></label>
				<div class="newsletter__field">
					<?php echo growmodo_icon( 'mail', 'newsletter__icon' ); ?>
					<input
						class="newsletter__input"
						type="email"
						id="newsletter-email"
						name="growmodo_email"
						placeholder="<?php esc_attr_e( 'Enter Your Email', 'growmodo' ); ?>"
						required
					/>
					<button class="newsletter__submit" type="submit">
						<span class="screen-reader-text"><?php esc_html_e( 'Subscribe', 'growmodo' ); ?></span>
						<?php echo growmodo_icon( 'send' ); ?>
					</button>
				</div>
			</form>
		</div>

		<div class="site-footer__cols">
			<?php foreach ( $growmodo_footer_columns as $growmodo_heading => $growmodo_links ) : ?>
				<nav class="footer-col" aria-label="<?php echo esc_attr( $growmodo_heading ); ?>">
					<h2 class="footer-col__title"><?php echo esc_html( $growmodo_heading ); ?></h2>
					<ul class="footer-col__list">
						<?php foreach ( $growmodo_links as $growmodo_label => $growmodo_url ) : ?>
							<li>
								<a href="<?php echo esc_url( $growmodo_url ); ?>"><?php echo esc_html( $growmodo_label ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="site-footer__bottom">
		<div class="container site-footer__bottom-inner">
			<div class="site-footer__legal">
				<p>
					<?php
					printf(
						/* translators: %1$s: current year, %2$s: site name. */
						esc_html__( '@%1$s %2$s. All Rights Reserved.', 'growmodo' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_class'     => 'site-footer__legal-menu',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>

			<ul class="socials">
				<?php foreach ( $growmodo_socials as $growmodo_network => $growmodo_url ) : ?>
					<li>
						<a href="<?php echo esc_url( $growmodo_url ); ?>" rel="noopener noreferrer" target="_blank">
							<span class="screen-reader-text"><?php echo esc_html( ucfirst( $growmodo_network ) ); ?></span>
							<?php echo growmodo_icon( $growmodo_network ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
