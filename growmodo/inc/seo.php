<?php
/**
 * SEO meta tags: description, Open Graph, and Twitter card.
 *
 * WordPress core already handles the document title (`title-tag`) and the
 * canonical link (`rel_canonical`), so this only adds what core leaves out.
 * Structured data lives separately in inc/schema.php.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * The site's own description: its tagline, or the theme's default sentence.
 *
 * @since 1.0.0
 *
 * @return string
 */
function growmodo_site_description() {
	$description = get_bloginfo( 'description' );

	if ( '' !== $description ) {
		return $description;
	}

	return __( 'Discover your dream property with Estatein. Browse curated homes and investments, and speak to advisers who know the market.', 'growmodo' );
}

/**
 * Descriptions for the pages whose copy lives in a template, not the editor.
 *
 * About Us, Services and Contact render fixed structural content, so there is
 * no excerpt or post content for a description to come from and they would
 * otherwise ship with none at all. Each line below is that page's own opening
 * copy, trimmed — keyed by slug, because the slug is what selects the template.
 *
 * @since 1.0.0
 *
 * @return array<string, string> Slug => description.
 */
function growmodo_page_descriptions() {
	return apply_filters(
		'growmodo_page_descriptions',
		array(
			'about-us' => __( 'Our story is one of continuous growth: a small team with big dreams that became a platform trusted by countless clients. Meet the people behind Estatein.', 'growmodo' ),
			'services' => __( 'Property valuation, marketing and negotiation, hands-off property management, and investment analysis — each service designed around your plans.', 'growmodo' ),
			'contact'  => __( 'Talk to Estatein about buying, selling, renting or managing a property. Send us a message, or visit one of our offices.', 'growmodo' ),
		)
	);
}

/**
 * Build a plain-text description for the current request.
 *
 * @since 1.0.0
 *
 * @return string Trimmed description, or an empty string when none applies.
 */
function growmodo_meta_description() {
	if ( is_front_page() ) {
		return growmodo_site_description();
	}

	if ( is_home() ) {
		return __( 'Guides, market notes and buying advice from the Estatein team.', 'growmodo' );
	}

	if ( is_singular() ) {
		$post = get_post();

		if ( has_excerpt( $post ) ) {
			return wp_trim_words( get_the_excerpt( $post ), 30, '' );
		}

		$content = trim( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );

		if ( '' !== $content ) {
			return wp_trim_words( $content, 30, '' );
		}

		$descriptions = growmodo_page_descriptions();

		return isset( $descriptions[ $post->post_name ] )
			? $descriptions[ $post->post_name ]
			: growmodo_site_description();
	}

	if ( is_post_type_archive( 'property' ) ) {
		return __( 'Browse every Estatein listing. Filter by location, property type, price, floor area and build year to find the home that matches your plans.', 'growmodo' );
	}

	if ( is_search() ) {
		/* translators: %s: search query. */
		return sprintf( __( 'Search results for %s on Estatein.', 'growmodo' ), get_search_query() );
	}

	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_description() );
	}

	return '';
}

/**
 * Print the meta description, Open Graph, and Twitter card tags.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_print_meta_tags() {
	$description = trim( growmodo_meta_description() );
	$title       = wp_get_document_title();
	$image       = '';

	// Prefer the canonical URL core already computed for singular views.
	$url = is_singular() ? wp_get_canonical_url() : home_url( add_query_arg( array() ) );

	if ( ! $url ) {
		$url = home_url( '/' );
	}

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_post(), 'large' );
	} elseif ( is_front_page() ) {
		$image = get_template_directory_uri() . '/assets/img/hero-building.webp';
	}

	if ( '' !== $description ) {
		printf(
			'<meta name="description" content="%s" />' . "\n",
			esc_attr( $description )
		);
		printf(
			'<meta property="og:description" content="%s" />' . "\n",
			esc_attr( $description )
		);
		printf(
			'<meta name="twitter:description" content="%s" />' . "\n",
			esc_attr( $description )
		);
	}

	printf(
		'<meta property="og:title" content="%s" />' . "\n",
		esc_attr( $title )
	);
	printf(
		'<meta property="og:type" content="%s" />' . "\n",
		esc_attr( is_singular() ? 'article' : 'website' )
	);
	printf(
		'<meta property="og:url" content="%s" />' . "\n",
		esc_url( $url )
	);
	printf(
		'<meta property="og:site_name" content="%s" />' . "\n",
		esc_attr( get_bloginfo( 'name' ) )
	);
	printf(
		'<meta name="twitter:title" content="%s" />' . "\n",
		esc_attr( $title )
	);

	if ( '' !== $image ) {
		printf(
			'<meta property="og:image" content="%s" />' . "\n",
			esc_url( $image )
		);
		printf(
			'<meta name="twitter:card" content="%s" />' . "\n",
			'summary_large_image'
		);
		printf(
			'<meta name="twitter:image" content="%s" />' . "\n",
			esc_url( $image )
		);
	} else {
		printf(
			'<meta name="twitter:card" content="%s" />' . "\n",
			'summary'
		);
	}
}
add_action( 'wp_head', 'growmodo_print_meta_tags', 2 );
