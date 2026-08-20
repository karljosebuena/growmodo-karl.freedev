<?php
/**
 * Single blog post: title, meta, featured image, content, sidebar.
 *
 * @package Growmodo
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<section class="page-hero">
			<div class="container">
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<p class="lede">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
					<?php
					$growmodo_categories = get_the_category_list( ', ' );
					if ( '' !== $growmodo_categories ) {
						echo ' &middot; ' . wp_kses_post( $growmodo_categories );
					}
					?>
				</p>
			</div>
		</section>

		<section class="section">
			<div class="container">
				<div class="with-sidebar">
					<div>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="property-single__media">
								<?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="entry-content"><?php the_content(); ?></div>

						<?php
						the_post_navigation(
							array(
								'class'              => 'post-nav',
								'prev_text'          => '&larr; %title',
								'next_text'          => '%title &rarr;',
								'screen_reader_text' => esc_html__( 'Post navigation', 'growmodo' ),
							)
						);

						if ( comments_open() || get_comments_number() ) {
							comments_template();
						}
						?>
					</div>

					<?php get_sidebar(); ?>
				</div>
			</div>
		</section>
	</article>
	<?php
endwhile;

get_footer();
