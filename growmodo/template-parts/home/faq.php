<?php
/**
 * Home "Frequently Asked Questions" section.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_faqs = new WP_Query(
	array(
		'post_type'      => 'faq',
		'posts_per_page' => growmodo_carousel_count(),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		),
	)
);

if ( ! $growmodo_faqs->have_posts() ) {
	return;
}
?>
<section class="section section--bordered is-revealable" id="faq">
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

		get_template_part(
			'template-parts/carousel',
			null,
			array(
				'query' => $growmodo_faqs,
				'card'  => 'card-faq',
				'label' => __( 'Frequently asked questions', 'growmodo' ),
			)
		);
		?>
	</div>
</section>
