<?php
/**
 * Home "Featured Properties" section.
 *
 * Ordered by the editor's Order field, then oldest first — a curated sequence
 * rather than whatever happened to be published last.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_properties = new WP_Query(
	array(
		'post_type'      => 'property',
		'posts_per_page' => growmodo_carousel_count(),
		'orderby'        => growmodo_curated_order(),
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

		get_template_part(
			'template-parts/carousel',
			null,
			array(
				'query' => $growmodo_properties,
				'card'  => 'card-property',
				'label' => __( 'Featured properties', 'growmodo' ),
			)
		);
		?>
	</div>
</section>
