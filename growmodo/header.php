<?php
/**
 * Site header: skip link, brand, primary nav with accessible mobile toggle,
 * contact CTA.
 *
 * @package Growmodo
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'growmodo' ); ?></a>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'growmodo' ); ?>">
			<button class="site-nav__toggle" aria-expanded="false" aria-controls="primary-menu">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'growmodo' ); ?></span>
				<span class="site-nav__toggle-bar" aria-hidden="true"></span>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<a class="btn btn--primary site-header__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
			<?php esc_html_e( 'Contact Us', 'growmodo' ); ?>
		</a>
	</div>
</header>

<main id="main" class="site-main">
