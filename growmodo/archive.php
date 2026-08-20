<?php
/**
 * Generic archive (category, tag, date, author).
 *
 * The property archive has its own template, archive-property.php.
 *
 * @package Growmodo
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php the_archive_title(); ?></h1>
		<?php
		$growmodo_description = get_the_archive_description();

		if ( '' !== $growmodo_description ) :
			?>
			<div class="lede"><?php echo wp_kses_post( $growmodo_description ); ?></div>
		<?php endif; ?>
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
							'screen_reader_text' => esc_html__( 'Archive pagination', 'growmodo' ),
						)
					);
					?>
				<?php else : ?>
					<p class="notice"><?php esc_html_e( 'Nothing found in this archive.', 'growmodo' ); ?></p>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</section>

<?php
get_footer();
