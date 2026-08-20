<?php
/**
 * Search form, themed to match the site's form controls.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_search_id = wp_unique_id( 'search-field-' );
?>
<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $growmodo_search_id ); ?>">
		<?php esc_html_e( 'Search for:', 'growmodo' ); ?>
	</label>
	<div class="search-form__field">
		<input
			class="form__input"
			type="search"
			id="<?php echo esc_attr( $growmodo_search_id ); ?>"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Search…', 'growmodo' ); ?>"
		/>
		<button class="btn btn--primary" type="submit">
			<?php esc_html_e( 'Search', 'growmodo' ); ?>
		</button>
	</div>
</form>
