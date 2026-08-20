<?php
/**
 * The call to action closing a services group.
 *
 * A template part because it appears in two places depending on the group's
 * layout — as the last cell of the card grid, or under the heading in the rail
 * arrangement — and rendering it twice in page-services.php would mean keeping
 * two copies in step.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type array $group Group definition, needing `cta_title` and `cta_text`.
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['group']['cta_title'] ) ) {
	return;
}

$growmodo_group = $args['group'];
?>
<div class="service-cta__panel">
	<div class="service-cta__copy">
		<h3 class="card__title"><?php echo esc_html( $growmodo_group['cta_title'] ); ?></h3>
		<p class="card__text"><?php echo esc_html( $growmodo_group['cta_text'] ); ?></p>
	</div>

	<a class="btn service-cta__action" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
		<?php esc_html_e( 'Learn More', 'growmodo' ); ?>
		<span class="screen-reader-text">
			<?php
			printf(
				/* translators: %s: name of the service group. */
				esc_html__( 'about %s', 'growmodo' ),
				esc_html( $growmodo_group['title'] )
			);
			?>
		</span>
	</a>
</div>
