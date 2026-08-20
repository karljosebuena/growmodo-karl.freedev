<?php
/**
 * Home "Featured Properties" section — the newest three property listings.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_properties = new WP_Query(
	array(
		'post_type'      => 'property',
		'posts_per_page' => 3,
	)
);

if ( ! $growmodo_properties->have_posts() ) {
	return;
}

$growmodo_archive = get_post_type_archive_link( 'property' );
?>
<section class="section section--bordered is-revealable" id="properties">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title'       => __( 'Featured Properties', 'growmodo' ),
				'text'        => __( 'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein.', 'growmodo' ),
				'action_url'  => $growmodo_archive,
				'action_text' => __( 'View All Properties', 'growmodo' ),
			)
		);
		?>

		<div class="grid grid--3">
			<?php growmodo_render_cards( $growmodo_properties, 'card-property' ); ?>
		</div>

		<?php
		growmodo_render_section_foot(
			$growmodo_properties->post_count,
			$growmodo_properties->found_posts,
			$growmodo_archive
		);
		?>
	</div>
</section>
