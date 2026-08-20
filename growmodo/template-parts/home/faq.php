<?php
/**
 * Home "Frequently Asked Questions" section.
 *
 * @package Growmodo
 */

$growmodo_faqs = new WP_Query(
	array(
		'post_type'      => 'faq',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'ASC',
	)
);

if ( ! $growmodo_faqs->have_posts() ) {
	return;
}
?>
<section class="section section--bordered" id="faq">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Frequently Asked Questions', 'growmodo' ),
				'text'  => __( 'Find answers to common questions about Estatein\'s services, property listings, and the real estate process. We\'re here to provide clarity and assist you every step of the way.', 'growmodo' ),
			)
		);
		?>

		<div class="grid grid--3">
			<?php growmodo_render_cards( $growmodo_faqs, 'card-faq' ); ?>
		</div>
	</div>
</section>
