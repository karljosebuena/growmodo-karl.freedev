<?php
/**
 * Property search: the one control on this page that asks the server anything.
 *
 * A plain GET form, so a search is a real URL — shareable, bookmarkable, and
 * working with JavaScript switched off. Its neighbour,
 * template-parts/property-filters.php, is the exact opposite by design.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type string $term Current search term, already validated.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_term = isset( $args['term'] ) ? $args['term'] : '';
?>
<form
	class="search-card"
	role="search"
	method="get"
	action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>"
>
	<label class="screen-reader-text" for="property-search">
		<?php esc_html_e( 'Search for a property', 'growmodo' ); ?>
	</label>
	<input
		class="search-card__input"
		type="search"
		id="property-search"
		name="q"
		value="<?php echo esc_attr( $growmodo_term ); ?>"
		placeholder="<?php esc_attr_e( 'Search For A Property', 'growmodo' ); ?>"
	/>
	<?php
	/*
	 * The label is text at desktop and hidden at narrow widths, where the
	 * design shows an icon-only square. aria-label carries the name across both
	 * — it repeats the visible text, so WCAG 2.5.3 (Label in Name) still holds.
	 */
	?>
	<button
		class="btn btn--primary search-card__submit"
		type="submit"
		aria-label="<?php esc_attr_e( 'Find Property', 'growmodo' ); ?>"
	>
		<?php echo growmodo_icon( 'search' ); ?>
		<span class="search-card__submit-text"><?php esc_html_e( 'Find Property', 'growmodo' ); ?></span>
	</button>
</form>
