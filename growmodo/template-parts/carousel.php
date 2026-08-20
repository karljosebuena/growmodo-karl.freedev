<?php
/**
 * Card carousel with a page counter and previous/next controls.
 *
 * The design shows each home section as a pageable row ("01 of 10" plus
 * arrows), so this renders one: a scroll-snap track that is a plain scrollable
 * row without JavaScript, upgraded by main.js into a paged carousel with a live
 * counter and disabled end states.
 *
 * @package Growmodo
 *
 * @since 1.0.0
 *
 * @var array $args {
 *     @type WP_Query $query Query whose posts fill the track. Required.
 *     @type string   $card  Card slug under template-parts/, e.g. 'card-property'.
 *     @type string   $label Accessible name for the carousel region.
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['query'] ) || empty( $args['card'] ) ) {
	return;
}

$growmodo_query = $args['query'];
$growmodo_label = isset( $args['label'] ) ? $args['label'] : __( 'Items', 'growmodo' );
?>
<div class="carousel" data-carousel aria-roledescription="carousel" aria-label="<?php echo esc_attr( $growmodo_label ); ?>">
	<div class="carousel__track" data-carousel-track tabindex="0">
		<?php growmodo_render_cards( $growmodo_query, $args['card'] ); ?>
	</div>

	<div class="section-foot">
		<p class="section-foot__count" data-carousel-count aria-live="polite">
			<strong data-carousel-current>01</strong>
			<span data-carousel-total></span>
		</p>

		<div class="section-foot__pager">
			<button class="icon-btn" type="button" data-carousel-prev>
				<span class="screen-reader-text"><?php esc_html_e( 'Previous', 'growmodo' ); ?></span>
				<?php echo growmodo_icon( 'arrow-left' ); ?>
			</button>
			<button class="icon-btn" type="button" data-carousel-next>
				<span class="screen-reader-text"><?php esc_html_e( 'Next', 'growmodo' ); ?></span>
				<?php echo growmodo_icon( 'arrow-right' ); ?>
			</button>
		</div>
	</div>
</div>
