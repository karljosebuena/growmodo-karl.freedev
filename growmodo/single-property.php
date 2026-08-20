<?php
/**
 * Single property: header, imagery, description, spec panel, enquiry form.
 *
 * The Figma's pricing-breakdown tables (transfer tax, legal fees, monthly
 * costs) are deliberately omitted: there is no data model behind them, and
 * inventing figures would ship fabricated content. Noted in the write-up
 * as a scoped decision.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$growmodo_beds     = (int) get_post_meta( get_the_ID(), 'growmodo_beds', true );
	$growmodo_baths    = (int) get_post_meta( get_the_ID(), 'growmodo_baths', true );
	$growmodo_type     = get_post_meta( get_the_ID(), 'growmodo_type', true );
	$growmodo_price    = (int) get_post_meta( get_the_ID(), 'growmodo_price', true );
	$growmodo_location = get_post_meta( get_the_ID(), 'growmodo_location', true );
	?>

	<article <?php post_class(); ?>>
		<section class="page-hero">
			<div class="container">
				<h1 class="page-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>

				<ul class="property__tags">
					<?php if ( '' !== $growmodo_location ) : ?>
						<li class="tag">
							<?php echo growmodo_icon( 'pin' ); ?>
							<?php echo esc_html( $growmodo_location ); ?>
						</li>
					<?php endif; ?>
					<?php if ( $growmodo_price > 0 ) : ?>
						<li class="tag">
							<?php echo esc_html( growmodo_format_price( $growmodo_price ) ); ?>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</section>

		<section class="section">
			<div class="container">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="property-single__media">
						<?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="property-single__layout">
					<div>
						<?php
						// Fall back to the excerpt so the heading is never orphaned.
						$growmodo_body = trim( get_the_content() );

						if ( '' === $growmodo_body ) {
							$growmodo_body = trim( get_the_excerpt() );
						}

						if ( '' !== $growmodo_body ) :
							?>
							<h2><?php esc_html_e( 'Description', 'growmodo' ); ?></h2>
							<div class="property-single__body entry-content">
								<?php
								if ( '' !== trim( get_the_content() ) ) {
									the_content();
								} else {
									echo '<p>' . esc_html( $growmodo_body ) . '</p>';
								}
								?>
							</div>
						<?php endif; ?>
					</div>

					<aside class="property-single__aside" aria-label="<?php esc_attr_e( 'Property details', 'growmodo' ); ?>">
						<h2 class="card__title"><?php esc_html_e( 'At a glance', 'growmodo' ); ?></h2>

						<div class="spec-list">
							<?php if ( $growmodo_beds > 0 ) : ?>
								<div class="spec-list__row">
									<span class="spec-list__key"><?php esc_html_e( 'Bedrooms', 'growmodo' ); ?></span>
									<span><?php echo esc_html( number_format_i18n( $growmodo_beds ) ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $growmodo_baths > 0 ) : ?>
								<div class="spec-list__row">
									<span class="spec-list__key"><?php esc_html_e( 'Bathrooms', 'growmodo' ); ?></span>
									<span><?php echo esc_html( number_format_i18n( $growmodo_baths ) ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $growmodo_type ) : ?>
								<div class="spec-list__row">
									<span class="spec-list__key"><?php esc_html_e( 'Type', 'growmodo' ); ?></span>
									<span><?php echo esc_html( $growmodo_type ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $growmodo_location ) : ?>
								<div class="spec-list__row">
									<span class="spec-list__key"><?php esc_html_e( 'Location', 'growmodo' ); ?></span>
									<span><?php echo esc_html( $growmodo_location ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $growmodo_price > 0 ) : ?>
								<div class="spec-list__row">
									<span class="spec-list__key"><?php esc_html_e( 'Price', 'growmodo' ); ?></span>
									<span><?php echo esc_html( growmodo_format_price( $growmodo_price ) ); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<a class="btn btn--primary btn--block" href="#property-inquiry">
							<?php esc_html_e( 'Enquire About This Property', 'growmodo' ); ?>
						</a>
					</aside>
				</div>
			</div>
		</section>

		<section class="section section--bordered">
			<div class="container">
				<?php
				get_template_part(
					'template-parts/section-head',
					null,
					array(
						'title' => sprintf(
							/* translators: %s: property title. */
							__( 'Inquire About %s', 'growmodo' ),
							get_the_title()
						),
						'text'  => __( 'Interested in this property? Fill out the form below and our team will reach out with full details, including scheduling a viewing and answering any questions you may have.', 'growmodo' ),
					)
				);

				get_template_part(
					'template-parts/form-inquiry',
					null,
					array(
						'id'       => 'property-inquiry',
						'type'     => 'inquiry',
						'property' => get_the_title(),
					)
				);
				?>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
