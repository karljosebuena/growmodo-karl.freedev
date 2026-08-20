<?php
/**
 * Property taxonomy of values and archive filtering.
 *
 * The property-type list lives here as the single source of truth: the meta
 * box renders it as a select, the archive filter renders it as a dropdown,
 * and both validate submitted values against it.
 *
 * @package Growmodo
 */

/**
 * Selectable property types.
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
 * Read and validate the archive filter values from the query string.
 *
 * Every value is validated, not merely sanitized: types must exist in the
 * allowlist and numbers are clamped to a sane range.
 *
 * @return array{type:string,beds:int,baths:int,max_price:int}
 */
function growmodo_get_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only archive filtering, no state change.
	$type = isset( $_GET['ptype'] ) ? sanitize_text_field( wp_unslash( $_GET['ptype'] ) ) : '';

	return array(
		'type'      => array_key_exists( $type, growmodo_property_types() ) ? $type : '',
		'beds'      => isset( $_GET['beds'] ) ? min( 10, absint( wp_unslash( $_GET['beds'] ) ) ) : 0,
		'baths'     => isset( $_GET['baths'] ) ? min( 10, absint( wp_unslash( $_GET['baths'] ) ) ) : 0,
		'max_price' => isset( $_GET['maxprice'] ) ? absint( wp_unslash( $_GET['maxprice'] ) ) : 0,
	);
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/**
 * Apply the archive filters to the main property archive query.
 *
 * @param WP_Query $query Query being prepared.
 * @return void
 */
function growmodo_filter_property_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'property' ) ) {
		return;
	}

	$filters    = growmodo_get_filters();
	$meta_query = array();

	if ( '' !== $filters['type'] ) {
		$meta_query[] = array(
			'key'   => 'growmodo_type',
			'value' => $filters['type'],
		);
	}

	if ( $filters['beds'] > 0 ) {
		$meta_query[] = array(
			'key'     => 'growmodo_beds',
			'value'   => $filters['beds'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( $filters['baths'] > 0 ) {
		$meta_query[] = array(
			'key'     => 'growmodo_baths',
			'value'   => $filters['baths'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( $filters['max_price'] > 0 ) {
		$meta_query[] = array(
			'key'     => 'growmodo_price',
			'value'   => $filters['max_price'],
			'type'    => 'NUMERIC',
			'compare' => '<=',
		);
	}

	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}

	if ( ! empty( $meta_query ) ) {
		$query->set( 'meta_query', $meta_query );
	}

	$query->set( 'posts_per_page', 6 );
}
add_action( 'pre_get_posts', 'growmodo_filter_property_archive' );
