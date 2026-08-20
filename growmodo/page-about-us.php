<?php
/**
 * About Us page: journey, values, achievements, process, team.
 *
 * Applied automatically to the page with the slug `about-us`.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

$growmodo_values = array(
	array( 'star', __( 'Trust', 'growmodo' ), __( 'Trust is the cornerstone of every successful real estate transaction.', 'growmodo' ) ),
	array( 'graduation', __( 'Excellence', 'growmodo' ), __( 'We set the bar high for ourselves. From the properties we list to the services we provide.', 'growmodo' ) ),
	array( 'people', __( 'Client-Centric', 'growmodo' ), __( 'Your dreams and needs are at the center of our universe. We listen, we understand.', 'growmodo' ) ),
	array( 'star', __( 'Our Commitment', 'growmodo' ), __( 'We are dedicated to providing you with the highest level of service, professionalism, and support.', 'growmodo' ) ),
);

$growmodo_achievements = array(
	array( __( '3+ Years of Excellence', 'growmodo' ), __( 'With over 3 years in the industry, we\'ve amassed a wealth of knowledge and experience, becoming a go-to resource for all things real estate.', 'growmodo' ) ),
	array( __( 'Happy Clients', 'growmodo' ), __( 'Our greatest achievement is the satisfaction of our clients. Their success stories fuel our passion for what we do.', 'growmodo' ) ),
	array( __( 'Industry Recognition', 'growmodo' ), __( 'We\'ve earned the respect of our peers and industry leaders, with accolades and awards that reflect our commitment to excellence.', 'growmodo' ) ),
);

$growmodo_steps = array(
	array( __( 'Discover a World of Possibilities', 'growmodo' ), __( 'Your journey begins with exploring our carefully curated property listings. Use our intuitive search tools to filter properties based on your preferences, including location, type, size, and budget.', 'growmodo' ) ),
	array( __( 'Narrowing Down Your Choices', 'growmodo' ), __( 'Once you\'ve found properties that catch your eye, save them to your account or make a shortlist. This allows you to compare and revisit your favourites as you make your decision.', 'growmodo' ) ),
	array( __( 'Personalized Guidance', 'growmodo' ), __( 'Have questions about a property or need more information? Our dedicated team of real estate experts is just a call or message away.', 'growmodo' ) ),
	array( __( 'See It for Yourself', 'growmodo' ), __( 'Arrange viewings of the properties you\'re interested in. We\'ll coordinate with the property owners and accompany you to ensure you get a firsthand look at your potential new home.', 'growmodo' ) ),
	array( __( 'Making Informed Decisions', 'growmodo' ), __( 'Before making an offer, our team will assist you with due diligence, including property inspections, legal checks, and market analysis. We want you to be fully informed and confident in your choice.', 'growmodo' ) ),
	array( __( 'Getting the Best Deal', 'growmodo' ), __( 'We\'ll help you negotiate the best terms and prepare your offer. Our goal is to secure the property at the right price and on favourable terms.', 'growmodo' ) ),
);

$growmodo_clients = array(
	array(
		'since'    => __( 'Since 2019', 'growmodo' ),
		'name'     => __( 'ABC Corporation', 'growmodo' ),
		'url'      => 'https://abccorporation.org/',
		'domain'   => __( 'Commercial Real Estate', 'growmodo' ),
		'category' => __( 'Luxury Home Development', 'growmodo' ),
		'quote'    => __( 'Estatein\'s expertise in finding the perfect office space for our expanding operations was invaluable. They truly understand our business needs.', 'growmodo' ),
	),
	array(
		'since'    => __( 'Since 2018', 'growmodo' ),
		'name'     => __( 'GreenTech Enterprises', 'growmodo' ),
		'url'      => 'https://www.greentechenterprises.com/',
		'domain'   => __( 'Commercial Real Estate', 'growmodo' ),
		'category' => __( 'Retail Space', 'growmodo' ),
		'quote'    => __( 'Estatein\'s ability to identify prime retail locations helped us expand our brand presence. They are a trusted partner in our growth.', 'growmodo' ),
	),
	array(
		'since'    => __( 'Since 2020', 'growmodo' ),
		'name'     => __( 'Harbour & Vine', 'growmodo' ),
		'url'      => 'https://harborandvine.com/',
		'domain'   => __( 'Hospitality', 'growmodo' ),
		'category' => __( 'Boutique Hotels', 'growmodo' ),
		'quote'    => __( 'They found us three sites in under a year, each one a better fit than the last. The market knowledge is genuinely deep.', 'growmodo' ),
	),
	array(
		'since'    => __( 'Since 2021', 'growmodo' ),
		'name'     => __( 'Northwind Logistics', 'growmodo' ),
		'url'      => 'https://nwl.one/en/',
		'domain'   => __( 'Industrial', 'growmodo' ),
		'category' => __( 'Warehousing', 'growmodo' ),
		'quote'    => __( 'Estatein understood our access and clearance requirements immediately, which saved us months of viewing unsuitable units.', 'growmodo' ),
	),
	array(
		'since'    => __( 'Since 2022', 'growmodo' ),
		'name'     => __( 'Meridian Health', 'growmodo' ),
		'url'      => 'https://meridianhealth.com.ph/',
		'domain'   => __( 'Healthcare', 'growmodo' ),
		'category' => __( 'Clinical Premises', 'growmodo' ),
		'quote'    => __( 'Fitting out a clinic has constraints most agents have never met. Ours had, and negotiated the lease terms around them.', 'growmodo' ),
	),
	array(
		'since'    => __( 'Since 2023', 'growmodo' ),
		'name'     => __( 'Atlas Studios', 'growmodo' ),
		'url'      => 'https://atlasstudios.com/',
		'domain'   => __( 'Creative', 'growmodo' ),
		'category' => __( 'Studio Space', 'growmodo' ),
		'quote'    => __( 'We needed height, power and quiet neighbours. They shortlisted four places that had all three and let us walk away from two.', 'growmodo' ),
	),
);

$growmodo_team = array(
	array( 'team-1', __( 'Max Mitchell', 'growmodo' ), __( 'Founder', 'growmodo' ) ),
	array( 'team-2', __( 'Sarah Johnson', 'growmodo' ), __( 'Chief Real Estate Officer', 'growmodo' ) ),
	array( 'team-3', __( 'David Brown', 'growmodo' ), __( 'Head of Property Management', 'growmodo' ) ),
	array( 'team-4', __( 'Michael Turner', 'growmodo' ), __( 'Legal Counsel', 'growmodo' ) ),
);
?>

<section class="section" id="story">
	<div class="container">
		<div class="split">
			<div>
				<?php
				get_template_part(
					'template-parts/section-head',
					null,
					array(
						'level' => 'h1',
						'title' => __( 'Our Journey', 'growmodo' ),
						'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we\'ve expanded our reach, forged valuable partnerships, and gained the trust of countless clients.', 'growmodo' ),
					)
				);
				?>

				<?php growmodo_render_stats(); ?>
			</div>

			<img
				class="split__media"
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/journey.webp' ); ?>"
				alt="<?php esc_attr_e( 'An adviser holding a scale model of a modern house', 'growmodo' ); ?>"
				width="759"
				height="547"
				decoding="async"
			/>
		</div>
	</div>
</section>

<section class="section section--bordered" id="values">
	<div class="container">
		<div class="split split--reverse">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'title' => __( 'Our Values', 'growmodo' ),
					'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.', 'growmodo' ),
				)
			);
			?>

			<div class="value-panel-frame">
				<ul class="value-panel">
				<?php foreach ( $growmodo_values as $growmodo_value ) : ?>
					<li class="value-panel__item">
						<div class="value-panel__head">
							<span class="info__icon"><?php echo growmodo_icon( $growmodo_value[0] ); ?></span>
							<h3 class="info__title"><?php echo esc_html( $growmodo_value[1] ); ?></h3>
						</div>
						<p class="card__text"><?php echo esc_html( $growmodo_value[2] ); ?></p>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="section section--bordered" id="achievements">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Our Achievements', 'growmodo' ),
				'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.', 'growmodo' ),
			)
		);
		?>

		<ul class="grid grid--3">
			<?php foreach ( $growmodo_achievements as $growmodo_achievement ) : ?>
				<li class="card">
					<h3 class="card__title"><?php echo esc_html( $growmodo_achievement[0] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $growmodo_achievement[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--bordered" id="process">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Navigating the Estatein Experience', 'growmodo' ),
				'text'  => __( 'At Estatein, we\'ve designed a straightforward process to help you find and purchase your dream property with ease. Here\'s a step-by-step guide to how it all works.', 'growmodo' ),
			)
		);
		?>

		<ol class="grid grid--3 steps">
			<?php foreach ( $growmodo_steps as $growmodo_index => $growmodo_step ) : ?>
				<li class="step">
					<p class="step__number">
						<?php
						printf(
							/* translators: %s: zero-padded step number. */
							esc_html__( 'Step %s', 'growmodo' ),
							esc_html( str_pad( (string) ( $growmodo_index + 1 ), 2, '0', STR_PAD_LEFT ) )
						);
						?>
					</p>
					<div class="step__body">
						<h3 class="card__title"><?php echo esc_html( $growmodo_step[0] ); ?></h3>
						<p class="card__text"><?php echo esc_html( $growmodo_step[1] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section section--bordered" id="team">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Meet the Estatein Team', 'growmodo' ),
				'text'  => __( 'At Estatein, our success is driven by the dedication and expertise of our team. Get to know the people behind our mission to make your real estate dreams a reality.', 'growmodo' ),
			)
		);
		?>

		<ul class="grid grid--2 grid--4">
			<?php foreach ( $growmodo_team as $growmodo_member ) : ?>
				<li class="card member">
					<div class="member__media">
						<img
							src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/' . $growmodo_member[0] . '.webp' ); ?>"
							<?php /* Decorative: the name follows as text, so alt would repeat it. */ ?>
							alt=""
							width="318"
							height="214"
							loading="lazy"
							decoding="async"
						/>
						<?php
						/*
						 * The design overlays a social badge on the portrait. It is
						 * markup rather than part of the image so it stays crisp and
						 * can carry an accessible name.
						 */
						?>
						<a class="member__social" href="https://x.com/" rel="noopener noreferrer" target="_blank">
							<span class="screen-reader-text">
								<?php
								printf(
									/* translators: %s: team member name. */
									esc_html__( '%s on X', 'growmodo' ),
									esc_html( $growmodo_member[1] )
								);
								?>
							</span>
							<?php echo growmodo_icon( 'twitter' ); ?>
						</a>
					</div>
					<h3 class="member__name"><?php echo esc_html( $growmodo_member[1] ); ?></h3>
					<p class="member__role"><?php echo esc_html( $growmodo_member[2] ); ?></p>
					<a class="member__hello" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<span><?php esc_html_e( 'Say Hello', 'growmodo' ); ?> <span aria-hidden="true">&#128075;</span></span>
						<span class="member__hello-icon"><?php echo growmodo_icon( 'send' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--bordered" id="clients">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Our Valued Clients', 'growmodo' ),
				'text'  => __( 'At Estatein, we have had the privilege of working with a diverse range of clients across various industries. Here are some of the clients we\'ve had the pleasure of serving.', 'growmodo' ),
			)
		);
		?>

		<?php
		get_template_part(
			'template-parts/carousel',
			null,
			array(
				'items'    => $growmodo_clients,
				'card'     => 'card-client',
				'label'    => __( 'Our valued clients', 'growmodo' ),
				'per_view' => 2,
			)
		);
		?>
	</div>
</section>

<?php
get_footer();
