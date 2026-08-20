<?php
/**
 * Small presentation helpers shared across templates.
 *
 * @package Growmodo
 */

/**
 * Format a whole-dollar price for display.
 *
 * Prices are stored as integers, so no decimals are rendered.
 *
 * @param int $amount Price in whole dollars.
 * @return string Formatted price, e.g. "$550,000".
 */
function growmodo_format_price( $amount ) {
	return '$' . number_format_i18n( absint( $amount ) );
}

/**
 * Render one section of posts from a query, using a card template part.
 *
 * Keeps the five card sections on the front page to a single line each
 * instead of five near-identical loops.
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
