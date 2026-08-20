<?php
/**
 * Property filters: a view over the results the search already returned.
 *
 * These never reach the server. assets/js/main.js applies each change to the
 * rendered cards immediately, which is why there is no submit button to press
 * and no reset button to press either — every control's first option *is* its
 * cleared state.
 *
 * A <fieldset>, not a <form>: the controls belong together, but nothing is ever
 * submitted, and the selects carry no `name` so a stray submit cannot pick them
 * up. Each select declares the card attribute it reads (`data-filter`) and how
 * to compare it (`data-match`); the script owns no field list of its own.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type array  $facets Option lists from growmodo_property_facets().
 *     @type string $target CSS selector for the element whose children are filtered.
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['facets'] ) || empty( $args['target'] ) ) {
	return;
}

$growmodo_facets = $args['facets'];

$growmodo_pills = array(
	array(
		'key'     => 'location',
		'icon'    => 'pin',
		'label'   => __( 'Location', 'growmodo' ),
		'match'   => 'exact',
		'options' => array_combine( $growmodo_facets['locations'], $growmodo_facets['locations'] ),
	),
	array(
		'key'     => 'type',
		'icon'    => 'house',
		'label'   => __( 'Property Type', 'growmodo' ),
		'match'   => 'exact',
		'options' => array_combine( $growmodo_facets['types'], $growmodo_facets['types'] ),
	),
	array(
		'key'     => 'price',
		'icon'    => 'banknote',
		'label'   => __( 'Pricing Range', 'growmodo' ),
		'match'   => 'range',
		'options' => growmodo_price_bands(),
	),
	array(
		'key'     => 'size',
		'icon'    => 'cube',
		'label'   => __( 'Property Size', 'growmodo' ),
		'match'   => 'range',
		'options' => growmodo_size_bands(),
	),
	array(
		'key'     => 'year',
		'icon'    => 'calendar',
		'label'   => __( 'Build Year', 'growmodo' ),
		'match'   => 'exact',
		'options' => array_combine( $growmodo_facets['years'], $growmodo_facets['years'] ),
	),
);

// A control with nothing to offer is a dead control; drop it rather than ship it.
$growmodo_pills = array_filter(
	$growmodo_pills,
	static function ( $pill ) {
		return ! empty( $pill['options'] );
	}
);

if ( empty( $growmodo_pills ) ) {
	return;
}
?>
<fieldset class="filters" data-filter-target="<?php echo esc_attr( $args['target'] ); ?>">
	<legend class="screen-reader-text"><?php esc_html_e( 'Narrow these results', 'growmodo' ); ?></legend>

	<?php foreach ( $growmodo_pills as $growmodo_pill ) : ?>
		<p class="pill">
			<label class="screen-reader-text" for="filter-<?php echo esc_attr( $growmodo_pill['key'] ); ?>">
				<?php echo esc_html( $growmodo_pill['label'] ); ?>
			</label>
			<span class="pill__icon"><?php echo growmodo_icon( $growmodo_pill['icon'] ); ?></span>
			<select
				class="pill__select"
				id="filter-<?php echo esc_attr( $growmodo_pill['key'] ); ?>"
				data-filter="<?php echo esc_attr( $growmodo_pill['key'] ); ?>"
				data-match="<?php echo esc_attr( $growmodo_pill['match'] ); ?>"
			>
				<option value=""><?php echo esc_html( $growmodo_pill['label'] ); ?></option>
				<?php foreach ( $growmodo_pill['options'] as $growmodo_value => $growmodo_option ) : ?>
					<option value="<?php echo esc_attr( $growmodo_value ); ?>">
						<?php echo esc_html( $growmodo_option ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="pill__chevron"><?php echo growmodo_icon( 'chevron-down' ); ?></span>
		</p>
	<?php endforeach; ?>
</fieldset>
