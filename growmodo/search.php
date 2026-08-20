<?php
/**
 * Search results.
 *
 * @package Growmodo
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title">
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search results for %s', 'growmodo' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<?php get_search_form(); ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="with-sidebar">
			<div>
				<?php if ( have_posts() ) : ?>
					<div class="grid grid--2">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/card-post' );
						endwhile;
						?>
					</div>

					<?php
					the_posts_pagination(
						array(
							'class'              => 'pagination',
							'mid_size'           => 1,
							'prev_text'          => esc_html__( 'Previous', 'growmodo' ),
							'next_text'          => esc_html__( 'Next', 'growmodo' ),
							'screen_reader_text' => esc_html__( 'Search pagination', 'growmodo' ),
						)
					);
					?>
				<?php else : ?>
					<p class="notice">
						<?php esc_html_e( 'Nothing matched that search. Try a different term, or browse our properties.', 'growmodo' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</section>

<?php
get_footer();
