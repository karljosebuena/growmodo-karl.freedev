/**
 * Growmodo theme front-end behaviour.
 *
 * Vanilla JS, no dependencies. Every feature degrades gracefully when its
 * markup is absent, so template parts can be omitted freely.
 */
( function () {
	'use strict';

	/**
	 * Mobile navigation toggle.
	 *
	 * The panel is CSS-driven; JS only owns aria-expanded and the class.
	 */
	const nav = document.querySelector( '.nav' );
	const toggle = nav && nav.querySelector( '.nav__toggle' );

	if ( toggle ) {
		toggle.addEventListener( 'click', function () {
			const open = 'true' === toggle.getAttribute( 'aria-expanded' );

			toggle.setAttribute( 'aria-expanded', String( ! open ) );
			nav.classList.toggle( 'is-open', ! open );
		} );

		// Escape closes the panel and returns focus to the toggle.
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				nav.classList.remove( 'is-open' );
				toggle.focus();
			}
		} );
	}

	/**
	 * Announcement bar dismissal.
	 *
	 * The bar ships visible and a snippet in <head> hides it for visitors who
	 * dismissed it previously, so this only has to handle the click.
	 */
	const announcement = document.getElementById( 'announcement' );
	const dismiss = announcement && announcement.querySelector( '[data-dismiss="announcement"]' );

	if ( dismiss ) {
		dismiss.addEventListener( 'click', function () {
			document.documentElement.classList.add( 'has-announcement-dismissed' );

			try {
				window.localStorage.setItem( 'growmodo-announcement-dismissed', '1' );
			} catch ( error ) {
				// Private browsing can block storage; dismissal just will not persist.
			}
		} );
	}
}() );
