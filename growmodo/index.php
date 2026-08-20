<?php
/**
 * Blog index — also the fallback for any template not otherwise defined.
 *
 * Runs the standard WordPress Loop with the blog sidebar beside it.
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
			$growmodo_posts_page = (int) get_option( 'page_for_posts' );

			if ( is_home() && ! is_front_page() && $growmodo_posts_page ) {
				echo esc_html( get_the_title( $growmodo_posts_page ) );
			} else {
				esc_html_e( 'Latest Insights', 'growmodo' );
			}
			?>
		</h1>
		<p class="lede">
			<?php esc_html_e( 'Market analysis, buying guides, and news from the Estatein team.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/loop-posts',
			null,
			array(
				'empty'       => __( 'No posts published yet.', 'growmodo' ),
				'pager_label' => __( 'Post pagination', 'growmodo' ),
			)
		);
		?>
	</div>
</section>

<?php
get_footer();
