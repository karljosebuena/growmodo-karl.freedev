<?php
/**
 * Property archive: hero, search, browser-side filters, results carousel.
 *
 * The search box reloads the page with a new query; the filter row below it
 * only narrows what is already rendered. See inc/property-query.php for why
 * they are split that way, and why the whole result set loads at once.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

$growmodo_term   = growmodo_property_search_term();
$growmodo_facets = growmodo_property_facets( $wp_query->posts );
$growmodo_loaded = (int) $wp_query->post_count;
$growmodo_found  = (int) $wp_query->found_posts;

/* translators: 1: number of properties shown, 2: number loaded. */
$growmodo_count_text = __( 'Showing %1$s of %2$s properties', 'growmodo' );
?>

<section class="page-hero page-hero--finder">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'Find Your Dream Property', 'growmodo' ); ?></h1>
		<p class="lede">
			<?php esc_html_e( 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey starts here.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<section class="finder">
	<div class="container finder__inner">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Search and filter properties', 'growmodo' ); ?></h2>

		<?php
		get_template_part(
			'template-parts/property-search',
			null,
			array( 'term' => $growmodo_term )
		);
		?>

		<?php
		/*
		 * Everything below needs results to act on. The pills prune themselves
		 * when a facet is empty, but the price bands are fixed and would
		 * otherwise survive a fruitless search as a control over nothing.
		 */
		?>
		<?php if ( $growmodo_loaded > 0 ) : ?>
			<?php
			get_template_part(
				'template-parts/property-filters',
				null,
				array(
					'facets' => $growmodo_facets,
					'target' => '#property-results',
				)
			);

			/*
			 * Hiding cards is a content change with no focus change, so the new
			 * count has to be announced (WCAG 4.1.3). The design shows no such
			 * line, so it is announced without being drawn: screen-reader-only,
			 * not removed. The script rewrites it from data-template, which is
			 * why the phrasing has to read correctly for any number rather than
			 * relying on _n().
			 */
			?>
			<p
				class="screen-reader-text"
				role="status"
				data-filter-count
				data-template="<?php echo esc_attr( $growmodo_count_text ); ?>"
			>
				<?php
				printf(
					esc_html( $growmodo_count_text ),
					esc_html( number_format_i18n( $growmodo_loaded ) ),
					esc_html( number_format_i18n( $growmodo_loaded ) )
				);
				?>
			</p>

			<p class="notice" data-filter-empty hidden>
				<?php esc_html_e( 'No properties match those filters. Widen one of them to see more.', 'growmodo' ); ?>
			</p>

			<?php if ( $growmodo_loaded < $growmodo_found ) : ?>
				<p class="finder__count">
					<?php
					printf(
						/* translators: %s: total number of matching properties. */
						esc_html__( 'Your search matched %s properties in total — narrow it to reach the rest.', 'growmodo' ),
						esc_html( number_format_i18n( $growmodo_found ) )
					);
					?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Discover a World of Possibilities', 'growmodo' ),
				'text'  => __( 'Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home.', 'growmodo' ),
			)
		);

		if ( $growmodo_loaded > 0 ) {
			get_template_part(
				'template-parts/carousel',
				null,
				array(
					'query'    => $wp_query,
					'card'     => 'card-property',
					'label'    => __( 'Properties', 'growmodo' ),
					'track_id' => 'property-results',
				)
			);
		} elseif ( '' !== $growmodo_term ) {
			?>
			<p class="notice">
				<?php
				printf(
					/* translators: %s: search term. */
					esc_html__( 'Nothing matched “%s”. Try a shorter or different search.', 'growmodo' ),
					esc_html( $growmodo_term )
				);
				?>
			</p>
			<?php
		} else {
			?>
			<p class="notice"><?php esc_html_e( 'No properties are listed yet.', 'growmodo' ); ?></p>
			<?php
		}
		?>
	</div>
</section>

<section class="section section--bordered" id="enquire">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'title' => __( 'Let\'s Make it Happen', 'growmodo' ),
				'text'  => __( 'Ready to take the first step toward your dream property? Fill out the form below and our team of experts will get back to you with tailored recommendations.', 'growmodo' ),
			)
		);

		get_template_part(
			'template-parts/form-inquiry',
			null,
			array(
				'id'   => 'archive-inquiry',
				'type' => 'inquiry',
			)
		);
		?>
	</div>
</section>

<?php
get_footer();
