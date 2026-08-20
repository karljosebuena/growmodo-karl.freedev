<?php
/**
 * Search results.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

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
		<?php
		get_template_part(
			'template-parts/loop-posts',
			null,
			array(
				'empty'       => __( 'Nothing matched that search. Try a different term, or browse our properties.', 'growmodo' ),
				'pager_label' => __( 'Search pagination', 'growmodo' ),
			)
		);
		?>
	</div>
</section>

<?php
get_footer();
