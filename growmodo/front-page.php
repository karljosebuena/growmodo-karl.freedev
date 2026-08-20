<?php
/**
 * Front page: the Estatein home sections in design order.
 *
 * Each section part returns early when it has no content, so an empty
 * install degrades to the hero and features rather than empty headings.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/features' );
get_template_part( 'template-parts/home/properties' );
get_template_part( 'template-parts/home/testimonials' );
get_template_part( 'template-parts/home/faq' );

get_footer();
