<?php
/**
 * Small presentation helpers shared across templates.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Format a whole-dollar price for display.
 *
 * Prices are stored as integers, so no decimals are rendered.
 *
 * @since 1.0.0
 *
 * @param int $amount Price in whole dollars.
 * @return string Formatted price, e.g. "$550,000".
 */
function growmodo_format_price( $amount ) {
	return '$' . number_format_i18n( absint( $amount ) );
}

/**
 * Format a floor area for display.
 *
 * The unit lives here rather than in each label, so it is written once.
 *
 * @since 1.0.0
 *
 * @param int $sqft Floor area in square feet.
 * @return string Formatted area, e.g. "1,500 sq ft".
 */
function growmodo_format_size( $sqft ) {
	return sprintf(
		/* translators: %s: floor area in square feet. */
		__( '%s sq ft', 'growmodo' ),
		number_format_i18n( absint( $sqft ) )
	);
}

/**
 * Every image belonging to a property, featured one first.
 *
 * WordPress's own model — images attached to the post — rather than a list of
 * IDs in a meta field: an editor adds gallery images by uploading them to the
 * listing, with no extra UI to learn and nothing to keep in sync.
 *
 * @since 1.0.0
 *
 * @param int $post_id Property ID.
 * @return int[] Attachment IDs, in display order.
 */
function growmodo_property_images( $post_id ) {
	$featured = (int) get_post_thumbnail_id( $post_id );

	$attached = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_parent'    => $post_id,
			'post_status'    => 'inherit',
			'posts_per_page' => 12,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	$images = $featured > 0 ? array( $featured ) : array();

	return array_values( array_unique( array_merge( $images, $attached ) ) );
}

/**
 * Split a newline-separated meta value into trimmed lines.
 *
 * Used for the key-features list, which is authored as one item per line —
 * simpler for an editor than a repeater, and it needs no extra UI.
 *
 * @since 1.0.0
 *
 * @param string $value Raw meta value.
 * @return string[] Non-empty lines.
 */
function growmodo_lines( $value ) {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return array();
	}

	$lines = array_map( 'trim', preg_split( '/\R/', $value ) );

	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Number of items a home carousel loads.
 *
 * The FAQ section and the FAQPage structured data both read this, so the markup
 * can never advertise answers that are not on the page — which Google's
 * structured-data policy treats as invalid.
 *
 * @since 1.0.0
 *
 * @return int
 */
function growmodo_carousel_count() {
	return 9;
}

/**
 * Render the shared marketing stat cards (200+ / 10k+ / 16+).
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_render_stats() {
	$stats = array(
		array( '200+', __( 'Happy Customers', 'growmodo' ) ),
		array( '10k+', __( 'Properties For Clients', 'growmodo' ) ),
		array( '16+', __( 'Years of Experience', 'growmodo' ) ),
	);
	?>
	<div class="hero__stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="stat">
				<div class="stat__value"><?php echo esc_html( $stat[0] ); ?></div>
				<div class="stat__label"><?php echo esc_html( $stat[1] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render pagination for the main query.
 *
 * Wraps the one argument that actually varies between templates so the same
 * six-line array is not repeated in four of them.
 *
 * @since 1.0.0
 *
 * @param string $label Screen-reader label describing what is being paged.
 * @return void
 */
function growmodo_pagination( $label ) {
	the_posts_pagination(
		array(
			'class'              => 'pagination',
			'mid_size'           => 1,
			'prev_text'          => esc_html__( 'Previous', 'growmodo' ),
			'next_text'          => esc_html__( 'Next', 'growmodo' ),
			'screen_reader_text' => $label,
		)
	);
}

/**
 * Render one section of posts from a query, using a card template part.
 *
 * Keeps the five card sections on the front page to a single line each
 * instead of five near-identical loops.
 *
 * @since 1.0.0
 *
 * @param WP_Query $query Query to loop over.
 * @param string   $card  Card slug under template-parts/, e.g. 'card-property'.
 * @return void
 */
function growmodo_render_cards( $query, $card ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/' . $card );
	}
	wp_reset_postdata();
}

