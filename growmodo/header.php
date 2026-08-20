<?php
/**
 * Site header: announcement bar, brand, primary navigation, contact CTA.
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

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'growmodo' ); ?></a>

<?php get_template_part( 'template-parts/announcement-bar' ); ?>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo growmodo_icon( 'logo', 'brand__mark' ); ?>
			<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		</a>

		<nav class="nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'growmodo' ); ?>">
			<button class="nav__toggle" aria-expanded="false" aria-controls="primary-menu">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle menu', 'growmodo' ); ?></span>
				<?php
				echo growmodo_icon( 'menu', 'nav__toggle-open' );
				echo growmodo_icon( 'close', 'nav__toggle-close' );
				?>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'nav__list',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<a class="btn site-header__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
			<?php esc_html_e( 'Contact Us', 'growmodo' ); ?>
		</a>
	</div>
</header>

<main id="main" class="site-main">
