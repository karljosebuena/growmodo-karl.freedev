<?php
/**
 * The four value propositions below the hero, on the home and services pages.
 *
 * A full-bleed strip rather than a container-width row: the design runs it to
 * within 10px of the viewport edge (20px at the desktop frame), which is why it
 * uses its own wrapper instead of .container.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_features = array(
	array(
		'icon'  => 'home',
		'title' => __( 'Find Your Dream Home', 'growmodo' ),
		'url'   => get_post_type_archive_link( 'property' ),
	),
	array(
		'icon'  => 'value',
		'title' => __( 'Unlock Property Value', 'growmodo' ),
		'url'   => home_url( '/services/#valuation' ),
	),
	array(
		'icon'  => 'manage',
		'title' => __( 'Effortless Property Management', 'growmodo' ),
		'url'   => home_url( '/services/#management' ),
	),
	array(
		'icon'  => 'insight',
		'title' => __( 'Smart Investments, Informed Decisions', 'growmodo' ),
		'url'   => home_url( '/services/#marketing' ),
	),
);
?>
<section class="section is-revealable" id="features" aria-label="<?php esc_attr_e( 'What we do', 'growmodo' ); ?>">
	<div class="feature-strip">
		<ul class="feature-strip__list">
			<?php foreach ( $growmodo_features as $growmodo_feature ) : ?>
				<li>
					<a class="feature" href="<?php echo esc_url( $growmodo_feature['url'] ); ?>">
						<span class="feature__icon"><?php echo growmodo_icon( $growmodo_feature['icon'] ); ?></span>
						<span class="feature__title"><?php echo esc_html( $growmodo_feature['title'] ); ?></span>
						<?php echo growmodo_icon( 'arrow-up-right', 'feature__arrow' ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
