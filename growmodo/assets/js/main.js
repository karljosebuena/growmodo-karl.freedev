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
	 * Announcement bar.
	 *
	 * Server-rendered hidden, then revealed unless previously dismissed —
	 * this way a returning visitor never sees it flash before it is removed.
	 */
	const STORAGE_KEY = 'growmodo-announcement-dismissed';
	const announcement = document.getElementById( 'announcement' );

	if ( announcement ) {
		let dismissed = false;

		try {
			dismissed = window.localStorage.getItem( STORAGE_KEY ) === '1';
		} catch ( error ) {
			// Private browsing can block storage; showing the bar is the safe default.
			dismissed = false;
		}

		if ( ! dismissed ) {
			announcement.hidden = false;
		}

		const dismiss = announcement.querySelector( '[data-dismiss="announcement"]' );

		if ( dismiss ) {
			dismiss.addEventListener( 'click', function () {
				announcement.hidden = true;

				try {
					window.localStorage.setItem( STORAGE_KEY, '1' );
				} catch ( error ) {
					// Dismissal simply will not persist — no user-facing failure.
				}
			} );
		}
	}
}() );
