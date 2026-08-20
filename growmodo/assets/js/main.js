/**
 * Growmodo theme front-end behaviour.
 *
 * Vanilla JS, no dependencies. Each feature degrades gracefully when its
 * markup is absent.
 */
( function () {
	'use strict';

	// Mobile navigation toggle.
	const toggle = document.querySelector( '.site-nav__toggle' );
	const nav = document.querySelector( '.site-nav' );

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			const isOpen = 'true' === toggle.getAttribute( 'aria-expanded' );

			toggle.setAttribute( 'aria-expanded', String( ! isOpen ) );
			nav.classList.toggle( 'is-open', ! isOpen );
		} );
	}
}() );
