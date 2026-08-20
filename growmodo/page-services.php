<?php
/**
 * Services page: hero, the four capabilities, then three service groups.
 *
 * Every group shares one shape — heading, service cards, side CTA — so the
 * content lives in a single array and renders through one loop.
 *
 * Applied automatically to the page with the slug `services`.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

$growmodo_groups = array(
	array(
		'id'        => 'valuation',
		'layout'    => 'wide',
		'title'     => __( 'Unlock Property Value', 'growmodo' ),
		'text'      => __( 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is.', 'growmodo' ),
		'cta_title' => __( 'Unlock the Value of Your Property Today', 'growmodo' ),
		'cta_text'  => __( 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.', 'growmodo' ),
		'services'  => array(
			array( 'value', __( 'Valuation Mastery', 'growmodo' ), __( 'Discover the true worth of your property with our expert valuation services.', 'growmodo' ), 'valuation-mastery' ),
			array( 'insight', __( 'Strategic Marketing', 'growmodo' ), __( 'Selling a property requires more than just a listing; it demands a strategic marketing approach.', 'growmodo' ), 'strategic-marketing' ),
			array( 'home', __( 'Negotiation Wizardry', 'growmodo' ), __( 'Negotiating the best deal is an art, and our negotiation experts are masters of it.', 'growmodo' ), 'negotiation-wizardry' ),
			array( 'manage', __( 'Closing Success', 'growmodo' ), __( 'A successful sale is not complete until the closing. We guide you through the intricate closing process.', 'growmodo' ), 'closing-success' ),
		),
	),
	array(
		'id'        => 'management',
		'layout'    => 'wide',
		'title'     => __( 'Effortless Property Management', 'growmodo' ),
		'text'      => __( 'Owning a property should be a pleasure, not a hassle. Estatein\'s Property Management Service takes the stress out of property ownership.', 'growmodo' ),
		'cta_title' => __( 'Experience Effortless Property Management', 'growmodo' ),
		'cta_text'  => __( 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.', 'growmodo' ),
		'services'  => array(
			array( 'home', __( 'Tenant Harmony', 'growmodo' ), __( 'Our Tenant Management services ensure that your tenants have a smooth and reducing vacancies.', 'growmodo' ), 'tenant-harmony' ),
			array( 'manage', __( 'Maintenance Ease', 'growmodo' ), __( 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.', 'growmodo' ), 'maintenance-ease' ),
			array( 'value', __( 'Financial Peace of Mind', 'growmodo' ), __( 'Managing property finances can be complex. Our financial experts take care of rent collection.', 'growmodo' ), 'financial-peace' ),
			array( 'insight', __( 'Legal Guardian', 'growmodo' ), __( 'Stay compliant with property laws and regulations effortlessly.', 'growmodo' ), 'legal-guardian' ),
		),
	),
	array(
		'id'        => 'marketing',
		'layout'    => 'rail',
		'title'     => __( 'Smart Investments, Informed Decisions', 'growmodo' ),
		'text'      => __( 'Building a real estate portfolio requires a strategic approach.', 'growmodo' ),
		'cta_title' => __( 'Unlock Your Investment Potential', 'growmodo' ),
		'cta_text'  => __( 'Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.', 'growmodo' ),
		'services'  => array(
			array( 'insight', __( 'Market Insight', 'growmodo' ), __( 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions.', 'growmodo' ), 'market-insight' ),
			array( 'value', __( 'ROI Assessment', 'growmodo' ), __( 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments.', 'growmodo' ), 'roi-assessment' ),
			array( 'manage', __( 'Customized Strategies', 'growmodo' ), __( 'Every investor is unique, and so are their goals. We develop customized Investment Strategies tailored to your specific needs.', 'growmodo' ), 'customized-strategies' ),
			array( 'home', __( 'Diversification Mastery', 'growmodo' ), __( 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations.', 'growmodo' ), 'diversification-mastery' ),
		),
	),
);
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'Elevate Your Real Estate Experience', 'growmodo' ); ?></h1>
		<p class="lede">
			<?php esc_html_e( 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<?php get_template_part( 'template-parts/home/features' ); ?>

<?php foreach ( $growmodo_groups as $growmodo_group ) : ?>
	<?php $growmodo_is_rail = 'rail' === $growmodo_group['layout']; ?>
	<section class="section section--bordered" id="<?php echo esc_attr( $growmodo_group['id'] ); ?>">
		<div class="container <?php echo $growmodo_is_rail ? 'service-rail' : ''; ?>">
			<div<?php echo $growmodo_is_rail ? ' class="service-rail__aside"' : ''; ?>>
				<?php
				get_template_part(
					'template-parts/section-head',
					null,
					array(
						'title' => $growmodo_group['title'],
						'text'  => $growmodo_group['text'],
					)
				);

				/*
				 * In the rail layout the CTA sits under the heading rather than in
				 * the card grid, which is the one thing that changes between the
				 * design's two arrangements of this section.
				 */
				if ( $growmodo_is_rail ) {
					get_template_part( 'template-parts/service-cta', null, array( 'group' => $growmodo_group ) );
				}
				?>
			</div>

			<ul class="service-grid <?php echo $growmodo_is_rail ? 'service-grid--pair' : ''; ?>">
				<?php foreach ( $growmodo_group['services'] as $growmodo_service ) : ?>
					<li class="card service" id="<?php echo esc_attr( $growmodo_service[3] ); ?>">
						<h3 class="service__head">
							<span class="info__icon"><?php echo growmodo_icon( $growmodo_service[0] ); ?></span>
							<span class="card__title"><?php echo esc_html( $growmodo_service[1] ); ?></span>
						</h3>
						<p class="card__text"><?php echo esc_html( $growmodo_service[2] ); ?></p>
					</li>
				<?php endforeach; ?>

				<?php
				/*
				 * The CTA is the last cell of the same grid, spanning the columns
				 * the services left free — the design lays it out beside them, not
				 * as a rail alongside. In the rail layout it moves out of the grid
				 * entirely; see below.
				 */
				?>
				<?php if ( 'rail' !== $growmodo_group['layout'] ) : ?>
					<li class="service-cta">
						<?php get_template_part( 'template-parts/service-cta', null, array( 'group' => $growmodo_group ) ); ?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</section>
<?php endforeach; ?>

<?php
get_footer();
