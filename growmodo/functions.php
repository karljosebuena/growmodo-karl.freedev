<?php
/**
 * Theme bootstrap: constants and module loading.
 *
 * All functionality lives in focused files under inc/ — this file only
 * wires them up.
 *
 * @package Growmodo
 */

define( 'GROWMODO_VERSION', '1.0.0' );

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/assets.php';
require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/form-handler.php';
