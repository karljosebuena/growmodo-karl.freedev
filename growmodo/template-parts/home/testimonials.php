<?php
/**
 * Home "What Our Clients Say" section.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_testimonials = new WP_Query(
	array(
		'post_type'      => 'testimonial',
		'posts_per_page' => growmodo_carousel_count(),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		),
	)
);

if ( ! $growmodo_testimonials->have_posts() ) {
	return;
}
?>
<section class="section section--bordered is-revealable" id="testimonials">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'What Our Clients Say', 'growmodo' ),
				'text'  => __( 'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.', 'growmodo' ),
			)
		);

		get_template_part(
			'template-parts/carousel',
			null,
			array(
				'query' => $growmodo_testimonials,
				'card'  => 'card-testimonial',
				'label' => __( 'Client testimonials', 'growmodo' ),
			)
		);
		?>
	</div>
</section>
