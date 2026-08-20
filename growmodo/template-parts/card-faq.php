<?php
/**
 * FAQ card rendered as a native disclosure.
 *
 * The Figma shows a "Read More" button with no destination; implementing the
 * card as <details>/<summary> makes that affordance real — it expands the
 * answer, works without JavaScript, and is keyboard accessible by default.
 *
 * @package Growmodo
 */

?>
<article <?php post_class( 'card faq' ); ?>>
	<details class="faq__disclosure">
		<summary class="faq__question">
			<h3 class="card__title"><?php the_title(); ?></h3>
			<span class="faq__marker" aria-hidden="true"></span>
		</summary>
		<div class="card__text entry-content"><?php the_content(); ?></div>
	</details>
</article>
