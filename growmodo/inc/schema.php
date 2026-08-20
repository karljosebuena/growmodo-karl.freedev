<?php
/**
 * JSON-LD structured data.
 *
 * Emits schema.org markup so search engines can read the site as a real
 * estate agent with listings and an FAQ, rather than inferring it from prose:
 *
 * - RealEstateAgent on the front page
 * - SingleFamilyResidence + Offer on a property
 * - FAQPage wherever FAQ entries are published
 *
 * @package Growmodo
 */

/**
 * Print the JSON-LD block for the current request.
 *
 * @return void
 */
function growmodo_print_schema() {
	$graph = array();

	if ( is_front_page() ) {
		$graph[] = growmodo_schema_organization();

		$faq = growmodo_schema_faq();
		if ( ! empty( $faq ) ) {
			$graph[] = $faq;
		}
	}

	if ( is_singular( 'property' ) ) {
		$graph[] = growmodo_schema_property( get_post() );
	}

	if ( empty( $graph ) ) {
		return;
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'growmodo_print_schema' );

/**
 * Build the RealEstateAgent node for the site itself.
 *
 * @return array
 */
function growmodo_schema_organization() {
	return array(
		'@type'       => 'RealEstateAgent',
		'@id'         => home_url( '/#organization' ),
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
	);
}

/**
 * Build the residence node for a single property, including its offer.
 *
 * @param WP_Post $post Property post.
 * @return array
 */
function growmodo_schema_property( $post ) {
	$price    = (int) get_post_meta( $post->ID, 'growmodo_price', true );
	$beds     = (int) get_post_meta( $post->ID, 'growmodo_beds', true );
	$baths    = (int) get_post_meta( $post->ID, 'growmodo_baths', true );
	$location = get_post_meta( $post->ID, 'growmodo_location', true );

	$node = array(
		'@type'       => 'SingleFamilyResidence',
		'@id'         => get_permalink( $post ) . '#property',
		'name'        => get_the_title( $post ),
		'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
		'url'         => get_permalink( $post ),
	);

	if ( has_post_thumbnail( $post ) ) {
		$node['image'] = get_the_post_thumbnail_url( $post, 'large' );
	}

	if ( $beds > 0 ) {
		$node['numberOfBedrooms'] = $beds;
	}

	if ( $baths > 0 ) {
		$node['numberOfBathroomsTotal'] = $baths;
	}

	if ( '' !== $location ) {
		$node['address'] = array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $location,
		);
	}

	if ( $price > 0 ) {
		$node['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $price,
			'priceCurrency' => 'USD',
			'availability'  => 'https://schema.org/InStock',
			'url'           => get_permalink( $post ),
		);
	}

	return $node;
}

/**
 * Build the FAQPage node from published FAQ entries.
 *
 * @return array Empty array when no FAQs exist.
 */
function growmodo_schema_faq() {
	$faqs = get_posts(
		array(
			'post_type'      => 'faq',
			'posts_per_page' => 10,
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	if ( empty( $faqs ) ) {
		return array();
	}

	$entities = array();

	foreach ( $faqs as $faq ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => get_the_title( $faq ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $faq->post_content ),
			),
		);
	}

	return array(
		'@type'      => 'FAQPage',
		'@id'        => home_url( '/#faq' ),
		'mainEntity' => $entities,
	);
}
