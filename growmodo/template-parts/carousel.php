<?php
/**
 * Card carousel with a page counter and previous/next controls.
 *
 * The design shows each card section as a pageable row ("01 of 10" plus
 * arrows), so this renders one: a scroll-snap track that is a plain scrollable
 * row without JavaScript, upgraded by main.js into a paged carousel with a live
 * counter and disabled end states.
 *
 * Fills the track either from a query (`query`) or from template data
 * (`items`), so post-driven and hard-coded sections share one implementation.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type WP_Query $query    Query whose posts fill the track. Use with `card`.
 *     @type array    $items    Plain data rows, passed to the card as `item`.
 *     @type string   $card     Card slug under template-parts/, e.g. 'card-property'.
 *     @type string   $label    Accessible name for the carousel region.
 *     @type int      $per_view Cards visible at the widest breakpoint: 2 or 3. Default 3.
 *     @type string   $track_id Optional id for the track, so other behaviour —
 *                              the property filters, for one — can address the
 *                              cards without reaching in through a class name.
 *     @type int[]    $thumbs   Optional attachment IDs rendered as a jump-to
 *                              strip above the track. Another control on the
 *                              same track, so it lives inside the carousel
 *                              where the script already looks.
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['card'] ) || ( empty( $args['query'] ) && empty( $args['items'] ) ) ) {
	return;
}

$growmodo_label    = isset( $args['label'] ) ? $args['label'] : __( 'Items', 'growmodo' );
$growmodo_per_view = isset( $args['per_view'] ) && 2 === (int) $args['per_view'] ? 2 : 3;
$growmodo_track_id = isset( $args['track_id'] ) ? $args['track_id'] : '';
$growmodo_thumbs   = isset( $args['thumbs'] ) ? (array) $args['thumbs'] : array();
?>
<div class="carousel" data-carousel aria-roledescription="carousel" aria-label="<?php echo esc_attr( $growmodo_label ); ?>">
	<?php if ( count( $growmodo_thumbs ) > 1 ) : ?>
		<ul class="carousel__thumbs">
			<?php foreach ( $growmodo_thumbs as $growmodo_index => $growmodo_thumb ) : ?>
				<li>
					<button class="carousel__thumb" type="button" data-carousel-goto="<?php echo absint( $growmodo_index ); ?>">
						<?php
						/*
						 * alt="" on purpose: the thumbnail duplicates an image
						 * already on the page, so describing it twice is noise.
						 * The button's own name says what it does.
						 */
						echo wp_get_attachment_image(
							$growmodo_thumb,
							'growmodo-card',
							false,
							array(
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
						<span class="screen-reader-text">
							<?php
							printf(
								/* translators: %s: image number within the gallery. */
								esc_html__( 'Show image %s', 'growmodo' ),
								esc_html( number_format_i18n( $growmodo_index + 1 ) )
							);
							?>
						</span>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div
		class="carousel__track <?php echo 2 === $growmodo_per_view ? 'carousel__track--two' : ''; ?>"
		<?php echo '' === $growmodo_track_id ? '' : 'id="' . esc_attr( $growmodo_track_id ) . '"'; ?>
		data-carousel-track
		tabindex="0"
	>
		<?php
		if ( ! empty( $args['query'] ) ) {
			growmodo_render_cards( $args['query'], $args['card'] );
		} else {
			foreach ( $args['items'] as $growmodo_item ) {
				get_template_part(
					'template-parts/' . $args['card'],
					null,
					array( 'item' => $growmodo_item )
				);
			}
		}
		?>
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
