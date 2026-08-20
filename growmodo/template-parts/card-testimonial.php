<?php
/**
 * Testimonial card: star rating, headline, quote, author identity.
 *
 * Expects the current post in the loop to be a `testimonial`.
 *
 * @package Growmodo
 */

$growmodo_rating   = min( 5, max( 0, (int) get_post_meta( get_the_ID(), 'growmodo_rating', true ) ) );
$growmodo_name     = get_post_meta( get_the_ID(), 'growmodo_client_name', true );
$growmodo_location = get_post_meta( get_the_ID(), 'growmodo_client_location', true );
?>
<article <?php post_class( 'card testimonial' ); ?>>
	<?php if ( $growmodo_rating > 0 ) : ?>
		<div class="stars">
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %d: star rating out of five. */
					esc_html__( 'Rated %d out of 5', 'growmodo' ),
					absint( $growmodo_rating )
				);
				?>
			</span>
			<?php for ( $growmodo_i = 0; $growmodo_i < $growmodo_rating; $growmodo_i++ ) : ?>
				<?php echo growmodo_icon( 'star' ); ?>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<h3 class="card__title"><?php the_title(); ?></h3>

	<div class="card__text"><?php the_content(); ?></div>

	<?php if ( '' !== $growmodo_name ) : ?>
		<div class="testimonial__author">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php
				the_post_thumbnail(
					'growmodo-avatar',
					array(
						'class'   => 'testimonial__avatar',
						'loading' => 'lazy',
						'alt'     => '',
					)
				);
				?>
			<?php endif; ?>
			<div>
				<div class="testimonial__name"><?php echo esc_html( $growmodo_name ); ?></div>
				<?php if ( '' !== $growmodo_location ) : ?>
					<div class="testimonial__location"><?php echo esc_html( $growmodo_location ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</article>
