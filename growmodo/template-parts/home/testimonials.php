<?php
/**
 * Home "What Our Clients Say" section.
 *
 * @package Growmodo
 */

$growmodo_testimonials = new WP_Query(
	array(
		'post_type'      => 'testimonial',
		'posts_per_page' => 3,
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
		?>

		<div class="grid grid--3">
			<?php growmodo_render_cards( $growmodo_testimonials, 'card-testimonial' ); ?>
		</div>
	</div>
</section>
