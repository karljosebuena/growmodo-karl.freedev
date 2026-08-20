<?php
/**
 * Comprehensive Pricing Details: listing price and four estimate groups.
 *
 * Every figure is derived from the listing price by inc/pricing.php, so this
 * template only lays them out. The note above the groups is not decoration —
 * these are estimates, and saying so is the reason the section can exist
 * without shipping invented numbers.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type int $price Listing price in whole dollars.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_price  = isset( $args['price'] ) ? absint( $args['price'] ) : 0;
$growmodo_groups = growmodo_pricing_groups( $growmodo_price );

if ( empty( $growmodo_groups ) ) {
	return;
}
?>
<section class="section section--bordered" id="pricing">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Comprehensive Pricing Details', 'growmodo' ),
				'text'  => __( 'At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing to help you make an informed decision.', 'growmodo' ),
			)
		);
		?>

		<p class="note-panel">
			<strong class="note-panel__label"><?php esc_html_e( 'Note', 'growmodo' ); ?></strong>
			<?php esc_html_e( 'The figures below are estimates and may vary depending on the property, location, and individual circumstances.', 'growmodo' ); ?>
		</p>

		<div class="pricing">
			<p class="pricing__listing">
				<span class="pricing__listing-label"><?php esc_html_e( 'Listing Price', 'growmodo' ); ?></span>
				<span class="pricing__listing-value"><?php echo esc_html( growmodo_format_price( $growmodo_price ) ); ?></span>
			</p>

			<div class="pricing__groups">
				<?php foreach ( $growmodo_groups as $growmodo_group ) : ?>
					<section class="card pricing__group">
						<div class="pricing__group-head">
							<h3 class="card__title"><?php echo esc_html( $growmodo_group['title'] ); ?></h3>
							<?php
							/*
							 * The design puts a Learn More button on every group.
							 * It goes to the enquiry form: these are estimates,
							 * and asking is the only way to learn more. The
							 * accessible name names its group, since four
							 * identical links on one page say nothing on their own.
							 */
							?>
							<a
								class="btn"
								href="#property-inquiry"
								aria-label="
								<?php
								printf(
									/* translators: %s: name of the cost group, e.g. "Additional Fees". */
									esc_attr__( 'Ask us about %s', 'growmodo' ),
									esc_attr( $growmodo_group['title'] )
								);
								?>
								"
							>
								<?php esc_html_e( 'Learn More', 'growmodo' ); ?>
							</a>
						</div>

						<ul class="pricing__rows">
							<?php foreach ( $growmodo_group['rows'] as $growmodo_row ) : ?>
								<li class="pricing__row">
									<span class="pricing__label"><?php echo esc_html( $growmodo_row[0] ); ?></span>
									<span class="pricing__figure">
										<strong class="pricing__value"><?php echo esc_html( $growmodo_row[1] ); ?></strong>
										<?php if ( '' !== $growmodo_row[2] ) : ?>
											<span class="tag pricing__note"><?php echo esc_html( $growmodo_row[2] ); ?></span>
										<?php endif; ?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
