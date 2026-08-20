<?php
/**
 * Home feature cards — the four value propositions below the hero.
 *
 * @package Growmodo
 */

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
<section class="section" id="features" aria-label="<?php esc_attr_e( 'What we do', 'growmodo' ); ?>">
	<div class="container">
		<ul class="grid grid--2 grid--4">
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
