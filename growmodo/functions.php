<?php
/**
 * Theme bootstrap: constants and module loading.
 *
 * All functionality lives in focused files under inc/ — this file only
 * wires them up.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme version, used to cache-bust enqueued assets.
 *
 * @since 1.0.0
 *
 * @var string
 */
define( 'GROWMODO_VERSION', '1.0.0' );

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/assets.php';
require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/property-query.php';
require get_template_directory() . '/inc/pricing.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/form-handler.php';
require get_template_directory() . '/inc/schema.php';
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/admin-columns.php';
