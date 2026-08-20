<?php
/**
 * Contact page: hero, contact detail cards, enquiry form, office locations.
 *
 * Applied automatically to the page with the slug `contact`.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

$growmodo_details = array(
	array(
		'icon'  => 'mail',
		'title' => __( 'Email', 'growmodo' ),
		'text'  => 'info@estatein.com',
		'url'   => 'mailto:info@estatein.com',
	),
	array(
		'icon'  => 'phone',
		'title' => __( 'Phone', 'growmodo' ),
		'text'  => '+1 (123) 456-7890',
		'url'   => 'tel:+11234567890',
	),
	array(
		'icon'  => 'pin',
		'title' => __( 'Main Headquarters', 'growmodo' ),
		'text'  => __( '123 Estatein Plaza, City Center, Metropolis', 'growmodo' ),
		'url'   => '#offices',
	),
	array(
		'icon'  => 'building',
		'title' => __( 'Regional Office', 'growmodo' ),
		'text'  => __( '456 Urban Avenue, Downtown District, Metropolis', 'growmodo' ),
		'url'   => '#offices',
	),
);

$growmodo_offices = array(
	array(
		'label' => __( 'Main Headquarters', 'growmodo' ),
		'title' => __( '123 Estatein Plaza, City Center, Metropolis', 'growmodo' ),
		'text'  => __( 'Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.', 'growmodo' ),
		'email' => 'info@estatein.com',
		'phone' => '+1 (123) 456-7890',
		'city'  => __( 'Metropolis', 'growmodo' ),
	),
	array(
		'label' => __( 'Regional Offices', 'growmodo' ),
		'title' => __( '456 Urban Avenue, Downtown District, Metropolis', 'growmodo' ),
		'text'  => __( 'Estatein\'s presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets.', 'growmodo' ),
		'email' => 'info@estatein.com',
		'phone' => '+1 (123) 628-7890',
		'city'  => __( 'Metropolis', 'growmodo' ),
	),
);
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'Get in Touch with Estatein', 'growmodo' ); ?></h1>
		<p class="lede">
			<?php esc_html_e( 'Welcome to Estatein\'s Contact Us page. We\'re here to assist you with any enquiries, requests, or feedback you may have.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<ul class="grid grid--2 grid--4">
			<?php foreach ( $growmodo_details as $growmodo_detail ) : ?>
				<li>
					<a class="info" href="<?php echo esc_url( $growmodo_detail['url'] ); ?>">
						<span class="info__icon"><?php echo growmodo_icon( $growmodo_detail['icon'] ); ?></span>
						<span class="info__title"><?php echo esc_html( $growmodo_detail['title'] ); ?></span>
						<span class="info__text"><?php echo esc_html( $growmodo_detail['text'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--bordered">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Let\'s Connect', 'growmodo' ),
				'text'  => __( 'We\'re excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein.', 'growmodo' ),
			)
		);

		get_template_part(
			'template-parts/form-inquiry',
			null,
			array(
				'id'   => 'contact-form',
				'type' => 'contact',
			)
		);
		?>
	</div>
</section>

<section class="section section--bordered" id="offices">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Discover Our Office Locations', 'growmodo' ),
				'text'  => __( 'Estatein is here to serve you across multiple locations. Whether you\'re looking to meet our team in person or reach out remotely, we have an office near you.', 'growmodo' ),
			)
		);
		?>

		<div class="grid grid--2">
			<?php foreach ( $growmodo_offices as $growmodo_office ) : ?>
				<article class="card">
					<p class="property__price-label"><?php echo esc_html( $growmodo_office['label'] ); ?></p>
					<h3 class="card__title"><?php echo esc_html( $growmodo_office['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $growmodo_office['text'] ); ?></p>

					<ul class="property__tags">
						<li class="tag">
							<?php echo growmodo_icon( 'mail' ); ?>
							<?php echo esc_html( $growmodo_office['email'] ); ?>
						</li>
						<li class="tag">
							<?php echo growmodo_icon( 'phone' ); ?>
							<?php echo esc_html( $growmodo_office['phone'] ); ?>
						</li>
						<li class="tag">
							<?php echo growmodo_icon( 'pin' ); ?>
							<?php echo esc_html( $growmodo_office['city'] ); ?>
						</li>
					</ul>

					<a class="btn btn--primary" href="#contact-form">
						<?php esc_html_e( 'Get in Touch', 'growmodo' ); ?>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
