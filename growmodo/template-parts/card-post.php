<?php
/**
 * Blog post card, used by the blog index, archives, and search results.
 *
 * @package Growmodo
 */

?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'growmodo-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<p class="property__price-label">
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
			<?php echo esc_html( get_the_date() ); ?>
		</time>
	</p>

	<h2 class="card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>

	<p class="card__text">
		<?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?>
	</p>

	<a class="btn" href="<?php the_permalink(); ?>">
		<?php esc_html_e( 'Read More', 'growmodo' ); ?>
	</a>
</article>
