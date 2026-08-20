<?php
/**
 * Client card: relationship start, company, domain and category, and a quote.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 *
 * @var array $args {
 *     @type array $item {
 *         @type string $since    Relationship start, e.g. "Since 2019".
 *         @type string $name     Company name.
 *         @type string $url      Client website. Optional; the action is omitted without it.
 *         @type string $domain   Domain label.
 *         @type string $category Category label.
 *         @type string $quote    What the client said.
 *     }
 * }
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['item'] ) ) {
	return;
}

$growmodo_client = $args['item'];
?>
<li class="card client">
	<div class="client__head">
		<div>
			<p class="client__since"><?php echo esc_html( $growmodo_client['since'] ); ?></p>
			<h3 class="client__name"><?php echo esc_html( $growmodo_client['name'] ); ?></h3>
		</div>
		<?php if ( ! empty( $growmodo_client['url'] ) ) : ?>
			<a
				class="btn"
				href="<?php echo esc_url( $growmodo_client['url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<?php esc_html_e( 'Visit Website', 'growmodo' ); ?>
				<?php
				/*
				 * Six identical "Visit Website" links share this page, so the
				 * company name is appended for anyone listing links out of
				 * context. It also flags the new tab, which is otherwise a
				 * surprise for screen-reader users.
				 */
				?>
				<span class="screen-reader-text">
					<?php
					printf(
						/* translators: %s: client company name. */
						esc_html__( '— %s, opens in a new tab', 'growmodo' ),
						esc_html( $growmodo_client['name'] )
					);
					?>
				</span>
			</a>
		<?php endif; ?>
	</div>

	<div class="client__meta">
		<div>
			<p class="client__meta-label">
				<?php echo growmodo_icon( 'building' ); ?>
				<?php esc_html_e( 'Domain', 'growmodo' ); ?>
			</p>
			<p class="client__meta-value"><?php echo esc_html( $growmodo_client['domain'] ); ?></p>
		</div>
		<div>
			<p class="client__meta-label">
				<?php echo growmodo_icon( 'insight' ); ?>
				<?php esc_html_e( 'Category', 'growmodo' ); ?>
			</p>
			<p class="client__meta-value"><?php echo esc_html( $growmodo_client['category'] ); ?></p>
		</div>
	</div>

	<div class="client__quote">
		<p class="client__quote-label">
			<?php esc_html_e( 'What They Said', 'growmodo' ); ?> <span aria-hidden="true">&#129303;</span>
		</p>
		<p class="card__text"><?php echo esc_html( $growmodo_client['quote'] ); ?></p>
	</div>
</li>
