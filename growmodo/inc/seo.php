<?php
/**
 * SEO meta tags: description, Open Graph, and Twitter card.
 *
 * WordPress core already handles the document title (`title-tag`) and the
 * canonical link (`rel_canonical`), so this only adds what core leaves out.
 * Structured data lives separately in inc/schema.php.
 *
 * @package Growmodo
 */

/**
 * Build a plain-text description for the current request.
 *
 * @return string Trimmed description, or an empty string when none applies.
 */
function growmodo_meta_description() {
	if ( is_front_page() ) {
		$description = get_bloginfo( 'description' );

		if ( '' === $description ) {
			$description = __( 'Discover your dream property with Estatein. Browse curated homes and investments, and speak to advisers who know the market.', 'growmodo' );
		}

		return $description;
	}

	if ( is_singular() ) {
		$post = get_post();

		$description = has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_strip_all_tags( strip_shortcodes( $post->post_content ) );

		return wp_trim_words( $description, 30, '' );
	}

	if ( is_post_type_archive( 'property' ) ) {
		return __( 'Browse every Estatein listing. Filter by property type, bedrooms, bathrooms, and budget to find the home that matches your plans.', 'growmodo' );
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
 * @return void
 */
function growmodo_print_meta_tags() {
	$description = trim( growmodo_meta_description() );
	$title       = wp_get_document_title();
	$url         = home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	$image       = '';

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
