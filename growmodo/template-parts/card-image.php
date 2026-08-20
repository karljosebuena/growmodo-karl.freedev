<?php
/**
 * Gallery image, as one slide of a carousel.
 *
 * A <figure> rather than a list item: the track it sits in is a <div>, so an
 * <li> there would be invalid.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type int $item Attachment ID.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_image = isset( $args['item'] ) ? absint( $args['item'] ) : 0;

if ( $growmodo_image < 1 ) {
	return;
}
?>
<figure class="gallery__item">
	<?php echo wp_get_attachment_image( $growmodo_image, 'large', false, array( 'loading' => 'lazy' ) ); ?>
</figure>
