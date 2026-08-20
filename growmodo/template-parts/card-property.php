<?php
/**
 * Property card: thumbnail, title, excerpt, spec tags, price, detail link.
 *
 * Expects the current post in the loop to be a `property`.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_beds     = (int) get_post_meta( get_the_ID(), 'growmodo_beds', true );
$growmodo_baths    = (int) get_post_meta( get_the_ID(), 'growmodo_baths', true );
$growmodo_type     = get_post_meta( get_the_ID(), 'growmodo_type', true );
$growmodo_price    = (int) get_post_meta( get_the_ID(), 'growmodo_price', true );
$growmodo_location = get_post_meta( get_the_ID(), 'growmodo_location', true );
?>
<?php
/*
 * The data attributes are what the archive's filters read. They ship on every
 * property card rather than only on the archive: a handful of bytes, against a
 * conditional that would make the card's output depend on where it is used.
 */
?>
<article
	<?php post_class( 'card property' ); ?>
	data-location="<?php echo esc_attr( $growmodo_location ); ?>"
	data-type="<?php echo esc_attr( $growmodo_type ); ?>"
	data-beds="<?php echo esc_attr( $growmodo_beds ); ?>"
	data-baths="<?php echo esc_attr( $growmodo_baths ); ?>"
	data-price="<?php echo esc_attr( $growmodo_price ); ?>"
>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'growmodo-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<h3 class="card__title">
		<a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
	</h3>

	<?php if ( has_excerpt() ) : ?>
		<p class="card__text">
			<?php echo esc_html( get_the_excerpt() ); ?>
			<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'growmodo' ); ?></a>
		</p>
	<?php endif; ?>

	<ul class="property__tags">
		<?php if ( $growmodo_beds > 0 ) : ?>
			<li class="tag">
				<?php echo growmodo_icon( 'bed' ); ?>
				<?php
				printf(
					/* translators: %d: number of bedrooms. */
					esc_html( _n( '%d-Bedroom', '%d-Bedroom', $growmodo_beds, 'growmodo' ) ),
					absint( $growmodo_beds )
				);
				?>
			</li>
		<?php endif; ?>

		<?php if ( $growmodo_baths > 0 ) : ?>
			<li class="tag">
				<?php echo growmodo_icon( 'bath' ); ?>
				<?php
				printf(
					/* translators: %d: number of bathrooms. */
					esc_html( _n( '%d-Bathroom', '%d-Bathroom', $growmodo_baths, 'growmodo' ) ),
					absint( $growmodo_baths )
				);
				?>
			</li>
		<?php endif; ?>

		<?php if ( '' !== $growmodo_type ) : ?>
			<li class="tag">
				<?php echo growmodo_icon( 'building' ); ?>
				<?php echo esc_html( $growmodo_type ); ?>
			</li>
		<?php endif; ?>
	</ul>

	<div class="property__footer">
		<?php if ( $growmodo_price > 0 ) : ?>
			<div>
				<div class="property__price-label"><?php esc_html_e( 'Price', 'growmodo' ); ?></div>
				<div class="property__price"><?php echo esc_html( growmodo_format_price( $growmodo_price ) ); ?></div>
			</div>
		<?php endif; ?>

		<a class="btn btn--primary" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'View Property Details', 'growmodo' ); ?>
		</a>
	</div>
</article>
