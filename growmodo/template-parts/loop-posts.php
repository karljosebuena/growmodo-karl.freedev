<?php
/**
 * The blog loop: post cards, pagination, and an empty state, beside the sidebar.
 *
 * Shared by index.php, archive.php, and search.php, which differ only in the
 * two strings passed here.
 *
 * @package Growmodo
 *
 * @since 1.0.0
 *
 * @var array $args {
 *     @type string $empty      Message shown when the query found nothing.
 *     @type string $pager_label Screen-reader label for the pagination.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_empty = isset( $args['empty'] )
	? $args['empty']
	: __( 'Nothing found.', 'growmodo' );
$growmodo_pager = isset( $args['pager_label'] )
	? $args['pager_label']
	: __( 'Pagination', 'growmodo' );
?>
<div class="with-sidebar">
	<div>
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--2">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card-post' );
				endwhile;
				?>
			</div>

			<?php growmodo_pagination( $growmodo_pager ); ?>
		<?php else : ?>
			<p class="notice"><?php echo esc_html( $growmodo_empty ); ?></p>
		<?php endif; ?>
	</div>

	<?php get_sidebar(); ?>
</div>
