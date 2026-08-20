<?php
/**
 * Generic archive (category, tag, date, author).
 *
 * The property archive has its own template, archive-property.php.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php the_archive_title(); ?></h1>
		<?php
		$growmodo_description = get_the_archive_description();

		if ( '' !== $growmodo_description ) :
			?>
			<div class="lede"><?php echo wp_kses_post( $growmodo_description ); ?></div>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/loop-posts',
			null,
			array(
				'empty'       => __( 'Nothing found in this archive.', 'growmodo' ),
				'pager_label' => __( 'Archive pagination', 'growmodo' ),
			)
		);
		?>
	</div>
</section>

<?php
get_footer();
