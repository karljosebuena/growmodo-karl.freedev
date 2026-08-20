<?php
/**
 * 404 template: keeps a dead end on-brand and offers a way onward.
 *
 * @package Growmodo
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'This page could not be found', 'growmodo' ); ?></h1>
		<p class="lede">
			<?php esc_html_e( 'The page you were looking for has moved or never existed. Browse our listings instead, or get in touch and we will point you the right way.', 'growmodo' ); ?>
		</p>
	</div>
</section>

<section class="section">
	<div class="container filters__actions">
		<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
			<?php esc_html_e( 'Browse Properties', 'growmodo' ); ?>
		</a>
		<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to Home', 'growmodo' ); ?>
		</a>
	</div>
</section>

<?php
get_footer();
