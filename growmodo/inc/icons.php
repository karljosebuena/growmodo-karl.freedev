<?php
/**
 * Inline SVG icon library.
 *
 * Icons are inlined rather than sprited or webfont-loaded: the set is small,
 * it costs no extra request, and `currentColor` lets CSS theme each one.
 *
 * The returned markup is theme-authored — never user input — so it is safe to
 * echo directly. `growmodo_icon()` is registered with phpcs as an
 * auto-escaping function in phpcs.xml.dist.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the inline SVG markup for an icon.
 *
 * @since 1.0.0
 *
 * @param string $name  Icon name from the library below.
 * @param string $css_class Optional CSS class for the <svg> element.
 * @return string SVG markup, or an empty string when the name is unknown.
 */
function growmodo_icon( $name, $css_class = '' ) {
	$icons = array(

		/*
		 * Brand mark, derived by scanning the export row by row rather than by
		 * eye. It is the union of two circles of radius 24, centred at (0,24)
		 * and (24,24): the first forms the left petal, the second the right
		 * petal and the whole rounded bottom. The notch between them is not a
		 * slit cut into a solid shape — it is the gap where the two circles have
		 * not yet met, so it is widest at the top and closes at mid-height.
		 */
		'logo'           => '<path d="M0 0A24 24 0 0 1 24 24V0a24 24 0 1 1-24 24Z" fill="currentColor"/>',

		'sparkle'        => '<path d="M12 1c.6 6.1 3.9 9.4 10 10-6.1.6-9.4 3.9-10 10-.6-6.1-3.9-9.4-10-10 6.1-.6 9.4-3.9 10-10Z" fill="currentColor"/>',
		'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'arrow-left'     => '<path d="M19 12H5m0 0 6-6m-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'arrow-right'    => '<path d="M5 12h14m0 0-6-6m6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'chevron-down'   => '<path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'close'          => '<path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>',
		'menu'           => '<path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>',
		'search'         => '<circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="m15.5 15.5 4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/>',

		/*
		 * Stroked as well as filled, with round joins: that thickens the arms
		 * and rounds the points into the chunky star the design uses, without a
		 * second path to maintain.
		 */
		'star'           => '<path d="M12 3.2l2.7 5.9 6.2.66-4.6 4.22 1.22 6.1L12 17.05 6.48 20.08l1.22-6.1-4.6-4.22 6.2-.66L12 3.2Z" fill="currentColor" stroke="currentColor" stroke-width="2.6" stroke-linejoin="round" stroke-linecap="round"/>',

		/*
		 * Property specification and filter glyphs. Solid, not outlined: the
		 * design's whole icon set is filled, and an outline set beside it reads
		 * as a different family rather than a different weight. Holes are cut
		 * with fill-rule="evenodd" so each glyph stays a single path.
		 */
		'bed'            => '<path d="M3 7h9a2.5 2.5 0 0 1 2.5 2.5V12H3V7Z" fill="currentColor"/><path d="M1.5 12.5h21a1 1 0 0 1 1 1V17h-23v-3.5a1 1 0 0 1 1-1Z" fill="currentColor"/><path d="M1.5 18h2.3v2.2H1.5V18Zm18.7 0h2.3v2.2h-2.3V18Z" fill="currentColor"/>',
		'bath'           => '<path d="M2.5 10.5h19v3.8a4.7 4.7 0 0 1-4.7 4.7H7.2a4.7 4.7 0 0 1-4.7-4.7v-3.8Z" fill="currentColor"/><path d="M8.2 9V6.4a2.9 2.9 0 0 0-5.7 0V9h2V6.4a.9.9 0 0 1 1.8 0V9h1.9Z" fill="currentColor"/><circle cx="10.7" cy="8.2" r="1.3" fill="currentColor"/><path d="M6 19.6 5.1 21.4m12.9-1.8.9 1.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
		'building'       => '<path fill-rule="evenodd" d="M5.5 2.5h13a1 1 0 0 1 1 1V21h-15V3.5a1 1 0 0 1 1-1ZM8 6.4v2.4h2.4V6.4H8Zm5.6 0v2.4H16V6.4h-2.4ZM8 11v2.4h2.4V11H8Zm5.6 0v2.4H16V11h-2.4ZM10.4 16v5h3.2v-5h-3.2Z" fill="currentColor"/>',
		// Asymmetric roof, a chimney and a lean-to on the right, as the design draws it.
		'house'          => '<path fill-rule="evenodd" d="M9.9 2.9a1.2 1.2 0 0 1 1.5 0l7.3 6.2a1 1 0 0 1 .3.8V21H2V9.9a1 1 0 0 1 .4-.8l7.5-6.2ZM8.4 13.8h4.4V21H8.4v-7.2Z" fill="currentColor"/><path d="M13.4 3.6h2.1v2.6l-2.1-1.8V3.6Z" fill="currentColor"/><path d="M20.4 11.6H22V21h-1.6v-9.4Z" fill="currentColor"/>',
		'banknote'       => '<path fill-rule="evenodd" d="M2.5 4.5h19A1.5 1.5 0 0 1 23 6v8.5a1.5 1.5 0 0 1-1.5 1.5h-19A1.5 1.5 0 0 1 1 14.5V6a1.5 1.5 0 0 1 1.5-1.5ZM12 7.1a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Z" fill="currentColor"/><path d="M1.8 17.6h20.4v1.5a1 1 0 0 1-1.1 1L2.9 19a1 1 0 0 1-1.1-1v-.4Z" fill="currentColor"/>',

		/*
		 * Three faces, lightest on top: opacity, not three colours, so the whole
		 * glyph still follows currentColor. Getting this the wrong way round
		 * reads as a hole rather than a box.
		 */
		'cube'           => '<path d="M12 1.8 22 6.9 12 12 2 6.9 12 1.8Z" fill="currentColor"/><path d="M1.4 8.5 11.4 13.6v8.6L1.4 17.1V8.5Z" fill="currentColor" opacity=".7"/><path d="M22.6 8.5 12.6 13.6v8.6l10-5.1V8.5Z" fill="currentColor" opacity=".45"/>',
		'calendar'       => '<path d="M7.2 1.8h2v3.4h-2V1.8Zm7.6 0h2v3.4h-2V1.8Z" fill="currentColor"/><path fill-rule="evenodd" d="M5.6 3.8h12.8A2.6 2.6 0 0 1 21 6.4v13A2.6 2.6 0 0 1 18.4 22H5.6A2.6 2.6 0 0 1 3 19.4v-13a2.6 2.6 0 0 1 2.6-2.6ZM3 8.6h18v1.8H3V8.6Z" fill="currentColor"/>',
		'pin'            => '<path fill-rule="evenodd" d="M12 1.8a8 8 0 0 1 8 8c0 5.5-8 12.4-8 12.4S4 15.3 4 9.8a8 8 0 0 1 8-8Zm0 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" fill="currentColor"/>',

		// Feature and service icons.
		'home'           => '<path d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'value'          => '<rect x="2.5" y="6" width="19" height="12" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M6 10v4M18 10v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
		'manage'         => '<path d="M4 21V8l7-4 7 4v13M4 21h16M9 21v-5h6v5M8 11h1M12 11h1M8 14h1M12 14h1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'insight'        => '<circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5 19 19M19 5l-1.5 1.5M6.5 17.5 5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',

		// About-page value icons.
		'graduation'     => '<path d="M12 4 2.5 8.5 12 13l9.5-4.5L12 4Z" fill="currentColor"/><path d="M6 11.2v4.3c0 1.9 2.7 3.2 6 3.2s6-1.3 6-3.2v-4.3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" fill="none"/>',
		'people'         => '<circle cx="9" cy="8.5" r="3" fill="currentColor"/><circle cx="16.5" cy="9.5" r="2.2" fill="currentColor"/><path d="M3 19c0-3.1 2.7-5.2 6-5.2s6 2.1 6 5.2H3Z" fill="currentColor"/><path d="M16.2 14c2.6.1 4.8 1.9 4.8 4.4v.6h-4.3" fill="currentColor"/>',

		// Contact and social.
		'mail'           => '<rect x="2.5" y="5" width="19" height="14" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="m3 7 9 6 9-6" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/>',
		'phone'          => '<path d="M5 3h3l2 5-2.5 1.5a12 12 0 0 0 6 6L15 13l5 2v3a2 2 0 0 1-2.2 2A17 17 0 0 1 3 5.2A2 2 0 0 1 5 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" fill="none"/>',
		'send'           => '<path d="M21 3 10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8 21 3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
		'facebook'       => '<path d="M14.5 8.5h2V5.6h-2.4c-2.3 0-3.6 1.4-3.6 3.7v1.6H8.5v3h2v7h3v-7h2.2l.4-3h-2.6V9.6c0-.7.3-1.1 1-1.1Z" fill="currentColor"/>',
		'linkedin'       => '<path d="M6.9 8.7H4.2V20h2.7V8.7ZM5.5 4a1.6 1.6 0 1 0 0 3.2 1.6 1.6 0 0 0 0-3.2ZM20 13.6c0-2.9-1.6-4.2-3.7-4.2-1.7 0-2.5.9-2.9 1.6V8.7H10.7V20h2.7v-6.2c0-1.3.6-2.1 1.8-2.1s1.7.8 1.7 2.1V20H20v-6.4Z" fill="currentColor"/>',
		'twitter'        => '<path d="M18.9 3h3.3l-7.2 8.2L23 21h-6.6l-5.2-6.8L5.3 21H2l7.7-8.8L2 3h6.7l4.9 6.4L18.9 3Zm-1.2 16h1.8L7.3 4.9H5.4L17.7 19Z" fill="currentColor"/>',
		'youtube'        => '<path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8A26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8ZM10 15.2V8.8L15.5 12 10 15.2Z" fill="currentColor"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	// The logo path is authored on a 0 0 48 48 grid; everything else on 24.
	$view_box = 'logo' === $name ? '0 0 48 48' : '0 0 24 24';

	return sprintf(
		'<svg class="%1$s" viewBox="%2$s" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">%3$s</svg>',
		esc_attr( $css_class ),
		esc_attr( $view_box ),
		$icons[ $name ]
	);
}
