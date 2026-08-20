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
 * Number of FAQ entries the front page shows.
 *
 * Shared by the rendered section and the FAQPage structured data, so the
 * markup can never advertise answers that are not on the page — which
 * Google's structured-data policy treats as invalid.
 *
 * @since 1.0.0
 *
 * @return int
 */
function growmodo_faq_count() {
	return 3;
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

/**
 * Render the "01 of 12" counter and pager arrows below a card section.
 *
 * The arrows link to the relevant archive rather than paginating in place —
 * the front page shows a fixed, curated set.
 *
 * @since 1.0.0
 *
 * @param int    $shown Number of posts rendered in the section.
 * @param int    $total Total posts available.
 * @param string $url   Destination for the forward arrow.
 * @return void
 */
function growmodo_render_section_foot( $shown, $total, $url ) {
	if ( $total <= $shown ) {
		return;
	}
	?>
	<div class="section-foot">
		<p class="section-foot__count">
			<strong><?php echo esc_html( str_pad( (string) $shown, 2, '0', STR_PAD_LEFT ) ); ?></strong>
			<?php
			printf(
				/* translators: %s: total number of items. */
				esc_html__( 'of %s', 'growmodo' ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</p>
		<div class="section-foot__pager">
			<?php /* Decorative: the section shows a fixed first page, so back is inert. */ ?>
			<span class="icon-btn is-inert" aria-hidden="true"><?php echo growmodo_icon( 'arrow-left' ); ?></span>
			<a class="icon-btn" href="<?php echo esc_url( $url ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'See more', 'growmodo' ); ?></span>
				<?php echo growmodo_icon( 'arrow-right' ); ?>
			</a>
		</div>
	</div>
	<?php
}
