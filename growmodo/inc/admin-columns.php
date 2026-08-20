<?php
/**
 * Admin list-table columns for the enquiry post type.
 *
 * Submissions are only useful if the important fields are visible without
 * opening each one, so the list table shows the contact details and the
 * property the enquiry relates to.
 *
 * @package Growmodo
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define the columns shown on the Inquiries list table.
 *
 * @since 1.0.0
 *
 * @param string[] $columns Existing column headings, keyed by column slug.
 * @return string[] Modified columns.
 */
function growmodo_inquiry_columns( $columns ) {
	return array(
		'cb'                => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'             => __( 'Enquiry', 'growmodo' ),
		'growmodo_email'    => __( 'Email', 'growmodo' ),
		'growmodo_phone'    => __( 'Phone', 'growmodo' ),
		'growmodo_property' => __( 'Property', 'growmodo' ),
		'date'              => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'growmodo' ),
	);
}
add_filter( 'manage_inquiry_posts_columns', 'growmodo_inquiry_columns' );

/**
 * Render the custom column values.
 *
 * @since 1.0.0
 *
 * @param string $column  Column slug being rendered.
 * @param int    $post_id Current post ID.
 * @return void
 */
function growmodo_inquiry_column_content( $column, $post_id ) {
	if ( 'growmodo_email' === $column ) {
		$email = get_post_meta( $post_id, 'growmodo_email', true );

		if ( '' !== $email ) {
			printf(
				'<a href="%1$s">%2$s</a>',
				esc_url( 'mailto:' . $email ),
				esc_html( $email )
			);
		}

		return;
	}

	if ( 'growmodo_phone' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'growmodo_phone', true ) );

		return;
	}

	if ( 'growmodo_property' === $column ) {
		$property_id = (int) get_post_meta( $post_id, 'growmodo_property_id', true );

		if ( $property_id > 0 && 'property' === get_post_type( $property_id ) ) {
			printf(
				'<a href="%1$s">%2$s</a>',
				esc_url( (string) get_edit_post_link( $property_id ) ),
				esc_html( get_the_title( $property_id ) )
			);
		} else {
			echo '&mdash;';
		}
	}
}
add_action( 'manage_inquiry_posts_custom_column', 'growmodo_inquiry_column_content', 10, 2 );
