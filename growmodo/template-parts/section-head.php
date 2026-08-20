<?php
/**
 * Section heading: sparkle motif, title, lede, optional action button.
 *
 * @package Growmodo
 *
 * @since 1.0.0
 *
 * @var array $args {
 *     @type string $title       Heading text. Required.
 *     @type string $text        Supporting copy. Optional.
 *     @type string $action_url  Button URL. Optional.
 *     @type string $action_text Button label. Optional.
 *     @type string $level       Heading tag, 'h1' or 'h2'. Default 'h2'.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_level      = isset( $args['level'] ) && 'h1' === $args['level'] ? 'h1' : 'h2';
$growmodo_has_action = ! empty( $args['action_url'] ) && ! empty( $args['action_text'] );
?>
<div class="section-head">
	<div class="section-head__sparkles" aria-hidden="true">
		<?php
		echo growmodo_icon( 'sparkle' );
		echo growmodo_icon( 'sparkle' );
		echo growmodo_icon( 'sparkle' );
		?>
	</div>

	<div class="section-head__row">
		<div>
			<<?php echo esc_html( $growmodo_level ); ?> class="section-head__title">
				<?php echo esc_html( $args['title'] ); ?>
			</<?php echo esc_html( $growmodo_level ); ?>>
			<?php if ( ! empty( $args['text'] ) ) : ?>
				<p class="lede"><?php echo esc_html( $args['text'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $growmodo_has_action ) : ?>
			<div class="section-head__action">
				<a class="btn" href="<?php echo esc_url( $args['action_url'] ); ?>">
					<?php echo esc_html( $args['action_text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
