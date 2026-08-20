<?php
/**
 * FAQ card: question, answer, and a "Read More" control.
 *
 * The design shows the answer already visible with a Read More button beneath
 * it, so the answer is rendered in full and clamped to two lines by CSS. The
 * button is revealed by main.js and expands the clamp, which means with
 * JavaScript disabled the whole answer is simply visible and no dead control
 * is shown.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

$growmodo_answer_id = 'faq-answer-' . get_the_ID();
?>
<article <?php post_class( 'card faq' ); ?>>
	<h3 class="card__title"><?php echo esc_html( get_the_title() ); ?></h3>

	<div class="card__text faq__answer" id="<?php echo esc_attr( $growmodo_answer_id ); ?>">
		<?php the_content(); ?>
	</div>

	<button
		class="btn faq__more"
		type="button"
		aria-expanded="false"
		aria-controls="<?php echo esc_attr( $growmodo_answer_id ); ?>"
		data-faq-more
		data-label-more="<?php esc_attr_e( 'Read More', 'growmodo' ); ?>"
		data-label-less="<?php esc_attr_e( 'Read Less', 'growmodo' ); ?>"
		hidden
	>
		<?php esc_html_e( 'Read More', 'growmodo' ); ?>
	</button>
</article>
