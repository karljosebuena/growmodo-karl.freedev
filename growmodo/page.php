<?php
/**
 * Generic page template, used by pages without a dedicated template.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<section class="page-hero">
			<div class="container">
				<h1 class="page-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>
			</div>
		</section>

		<section class="section">
			<div class="container">
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</section>
	</article>
	<?php
endwhile;

get_footer();
