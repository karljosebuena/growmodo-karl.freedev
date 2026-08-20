<?php
/**
 * Property archive: hero, working filter bar, results grid, pagination.
 *
 * The filter bar submits by GET to this same archive; the query is modified in
 * inc/property-query.php so pagination and the theme's URLs stay canonical.
 *
 * @package Growmodo
 */

get_header();

$growmodo_filters = growmodo_get_filters();
$growmodo_action  = get_post_type_archive_link( 'property' );
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'Find Your Dream Property', 'growmodo' ); ?></h1>
		<p class="lede">
			<?php esc_html_e( 'Welcome to Estatein, where your dream property awaits. Browse our curated selection of homes and investments, each offering a unique story and a chance to redefine your life.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="screen-reader-text"><?php esc_html_e( 'Filter properties', 'growmodo' ); ?></h2>

		<form class="filters" action="<?php echo esc_url( $growmodo_action ); ?>" method="get" role="search">
			<div class="filters__row">
				<p class="form__field">
					<label class="form__label" for="filter-type"><?php esc_html_e( 'Property Type', 'growmodo' ); ?></label>
					<select class="form__input" id="filter-type" name="ptype">
						<option value=""><?php esc_html_e( 'Any type', 'growmodo' ); ?></option>
						<?php foreach ( growmodo_property_types() as $growmodo_value => $growmodo_label ) : ?>
							<option value="<?php echo esc_attr( $growmodo_value ); ?>" <?php selected( $growmodo_value, $growmodo_filters['type'] ); ?>>
								<?php echo esc_html( $growmodo_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="form__field">
					<label class="form__label" for="filter-beds"><?php esc_html_e( 'Bedrooms (min)', 'growmodo' ); ?></label>
					<select class="form__input" id="filter-beds" name="beds">
						<option value="0"><?php esc_html_e( 'Any', 'growmodo' ); ?></option>
						<?php for ( $growmodo_i = 1; $growmodo_i <= 5; $growmodo_i++ ) : ?>
							<option value="<?php echo esc_attr( $growmodo_i ); ?>" <?php selected( $growmodo_i, $growmodo_filters['beds'] ); ?>>
								<?php echo esc_html( sprintf( '%d+', $growmodo_i ) ); ?>
							</option>
						<?php endfor; ?>
					</select>
				</p>

				<p class="form__field">
					<label class="form__label" for="filter-baths"><?php esc_html_e( 'Bathrooms (min)', 'growmodo' ); ?></label>
					<select class="form__input" id="filter-baths" name="baths">
						<option value="0"><?php esc_html_e( 'Any', 'growmodo' ); ?></option>
						<?php for ( $growmodo_i = 1; $growmodo_i <= 5; $growmodo_i++ ) : ?>
							<option value="<?php echo esc_attr( $growmodo_i ); ?>" <?php selected( $growmodo_i, $growmodo_filters['baths'] ); ?>>
								<?php echo esc_html( sprintf( '%d+', $growmodo_i ) ); ?>
							</option>
						<?php endfor; ?>
					</select>
				</p>

				<p class="form__field">
					<label class="form__label" for="filter-price"><?php esc_html_e( 'Max price (USD)', 'growmodo' ); ?></label>
					<input
						class="form__input"
						type="number"
						id="filter-price"
						name="maxprice"
						min="0"
						step="10000"
						inputmode="numeric"
						placeholder="<?php esc_attr_e( 'No maximum', 'growmodo' ); ?>"
						value="<?php echo $growmodo_filters['max_price'] > 0 ? esc_attr( $growmodo_filters['max_price'] ) : ''; ?>"
					/>
				</p>
			</div>

			<div class="filters__actions">
				<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Search Properties', 'growmodo' ); ?></button>
				<a class="btn" href="<?php echo esc_url( $growmodo_action ); ?>"><?php esc_html_e( 'Reset', 'growmodo' ); ?></a>
			</div>
		</form>

		<?php if ( have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Matching properties', 'growmodo' ); ?></h2>

			<p class="lede filters__summary" role="status">
				<?php
				printf(
					/* translators: %s: number of matching properties. */
					esc_html( _n( '%s property matches your search.', '%s properties match your search.', (int) $wp_query->found_posts, 'growmodo' ) ),
					esc_html( number_format_i18n( $wp_query->found_posts ) )
				);
				?>
			</p>

			<div class="grid grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card-property' );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'class'              => 'pagination',
					'mid_size'           => 1,
					'prev_text'          => esc_html__( 'Previous', 'growmodo' ),
					'next_text'          => esc_html__( 'Next', 'growmodo' ),
					'screen_reader_text' => esc_html__( 'Property pagination', 'growmodo' ),
				)
			);
			?>
		<?php else : ?>
			<p class="notice">
				<?php esc_html_e( 'No properties match those filters yet. Try widening your search.', 'growmodo' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
