<?php
/**
 * Property archive: the search query, and the option lists the filters offer.
 *
 * Search and filtering are two different things here, deliberately:
 *
 * - **Search** asks the server a question. It changes which properties exist on
 *   the page, so it owns the URL, costs a page load, and lives in this file.
 * - **Filters** are a view over whatever search returned. They are applied to
 *   the rendered cards in the browser (see assets/js/main.js), which is why
 *   there is no `meta_query` below: one predicate, one implementation.
 *
 * Filtering in the browser can only ever see what is on the page, so the
 * archive loads its whole result set in one request instead of paginating it,
 * and the template says so out loud if the cap below ever truncates.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Selectable property types.
 *
 * This is the authoring allowlist: the editor renders it as a select and
 * validates against it. The archive filter deliberately does *not* read it —
 * see growmodo_property_facets() for why.
 *
 * @since 1.0.0
 *
 * @return string[] Type labels, keyed by stored value.
 */
function growmodo_property_types() {
	return array(
		'Villa'     => __( 'Villa', 'growmodo' ),
		'Apartment' => __( 'Apartment', 'growmodo' ),
		'Cottage'   => __( 'Cottage', 'growmodo' ),
		'Townhouse' => __( 'Townhouse', 'growmodo' ),
		'Penthouse' => __( 'Penthouse', 'growmodo' ),
		'Bungalow'  => __( 'Bungalow', 'growmodo' ),
	);
}

/**
 * The order property listings are shown in, everywhere.
 *
 * The editor's Order field first, publication date second — a curated sequence
 * rather than whatever happened to be published last. It lives in one function
 * because the home carousel and the archive have to agree; when they did not,
 * the same six listings appeared in opposite orders on the two pages.
 *
 * @since 1.0.0
 *
 * @return array<string,string> WP_Query `orderby` clauses.
 */
function growmodo_property_order() {
	return array(
		'menu_order' => 'ASC',
		'date'       => 'ASC',
	);
}

/**
 * Distinct locations across the published properties.
 *
 * Used by the enquiry form's Preferred Location select, so it can only offer
 * places we actually have listings in. Memoised per request: the form appears
 * on three templates and none of them should pay for this twice.
 *
 * @since 1.0.0
 *
 * @return string[] Location labels, keyed by themselves.
 */
function growmodo_property_locations() {
	static $locations = null;

	if ( null !== $locations ) {
		return $locations;
	}

	$locations = array();

	/*
	 * Full post objects rather than `fields => ids`: WP_Query primes the meta
	 * cache for them in one query, where ids would cost one query per listing.
	 */
	$query = new WP_Query(
		array(
			'post_type'              => 'property',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'orderby'                => growmodo_property_order(),
		)
	);

	foreach ( $query->posts as $post ) {
		$location = get_post_meta( $post->ID, 'growmodo_location', true );

		if ( '' !== $location ) {
			$locations[ $location ] = $location;
		}
	}

	ksort( $locations );

	return $locations;
}

/**
 * Read the property search term from the query string.
 *
 * The parameter is `q`, not `s`: `s` would make WordPress treat the request as
 * a site-wide search and hand it to search.php, losing this archive entirely.
 *
 * @since 1.0.0
 *
 * @return string Sanitized search term, or an empty string.
 */
function growmodo_property_search_term() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only search, no state change.
	$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

	return trim( $term );
}

/**
 * Collect the filter option lists from the properties actually on the page.
 *
 * Derived from the result set rather than from a fixed list, so every option
 * offered is guaranteed to match at least one visible card. A filter that can
 * only ever return nothing is worse than no filter.
 *
 * @since 1.0.0
 *
 * @param WP_Post[] $posts Posts rendered by the archive.
 * @return array{locations:string[],types:string[],years:int[]}
 */
function growmodo_property_facets( $posts ) {
	$locations = array();
	$types     = array();
	$years     = array();

	foreach ( $posts as $post ) {
		$location = get_post_meta( $post->ID, 'growmodo_location', true );
		$type     = get_post_meta( $post->ID, 'growmodo_type', true );
		$year     = (int) get_post_meta( $post->ID, 'growmodo_year', true );

		if ( '' !== $location ) {
			$locations[] = $location;
		}

		if ( '' !== $type ) {
			$types[] = $type;
		}

		if ( $year > 0 ) {
			$years[] = $year;
		}
	}

	$locations = array_unique( $locations );
	$types     = array_unique( $types );
	$years     = array_unique( $years );

	sort( $locations );
	sort( $types );
	rsort( $years );

	return array(
		'locations' => $locations,
		'types'     => $types,
		'years'     => $years,
	);
}

/**
 * Price bands offered by the pricing filter.
 *
 * Bounds are `low-high`, parsed by the browser-side filter as half-open: low
 * included, high excluded, so the number two adjacent bands share lands in
 * exactly one of them. An empty high end means no upper limit. Labels are built
 * with growmodo_format_price() so the currency is written in exactly one place.
 *
 * @since 1.0.0
 *
 * @return string[] Band labels, keyed by bounds.
 */
function growmodo_price_bands() {
	return array(
		'0-500000'       => sprintf(
			/* translators: %s: formatted price. */
			__( 'Under %s', 'growmodo' ),
			growmodo_format_price( 500000 )
		),
		'500000-1000000' => sprintf(
			/* translators: 1: lower price, 2: upper price. */
			__( '%1$s to %2$s', 'growmodo' ),
			growmodo_format_price( 500000 ),
			growmodo_format_price( 1000000 )
		),
		'1000000-'       => sprintf(
			/* translators: %s: formatted price. */
			__( 'Over %s', 'growmodo' ),
			growmodo_format_price( 1000000 )
		),
	);
}

/**
 * Floor-area bands offered by the property-size filter.
 *
 * Bands rather than derived values: sizes are continuous, so one option per
 * listing would be a menu of six meaningless numbers. Same `low-high` format as
 * the price bands, with an open upper end.
 *
 * @since 1.0.0
 *
 * @return string[] Band labels, keyed by bounds in square feet.
 */
function growmodo_size_bands() {
	return array(
		'0-1500'    => sprintf(
			/* translators: %s: formatted floor area. */
			__( 'Under %s', 'growmodo' ),
			growmodo_format_size( 1500 )
		),
		'1500-3000' => sprintf(
			/* translators: 1: lower floor area, 2: upper floor area. */
			__( '%1$s to %2$s', 'growmodo' ),
			growmodo_format_size( 1500 ),
			growmodo_format_size( 3000 )
		),
		'3000-'     => sprintf(
			/* translators: %s: formatted floor area. */
			__( 'Over %s', 'growmodo' ),
			growmodo_format_size( 3000 )
		),
	);
}

/**
 * Apply the search term to the main property archive query.
 *
 * @since 1.0.0
 *
 * @param WP_Query $query Query being prepared.
 * @return void
 */
function growmodo_property_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'property' ) ) {
		return;
	}

	$term = growmodo_property_search_term();

	if ( '' !== $term ) {
		$query->set( 's', $term );
	}

	/*
	 * The whole result set, not a page of it: the filters below the search box
	 * work on rendered cards, so anything left on a second page would be
	 * invisible to them. The cap is a ceiling on that, not a page size — the
	 * template reports it when a search matches more than this.
	 */
	$query->set( 'posts_per_page', 24 );

	// The same curated order the home carousel uses, search or no search.
	$query->set( 'orderby', growmodo_property_order() );
}
add_action( 'pre_get_posts', 'growmodo_property_archive_query' );
