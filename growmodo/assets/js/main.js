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
	 * Card carousels.
	 *
	 * The track is already a usable scrollable row from CSS alone; this adds
	 * paging buttons, a live page counter, and disabled states at the ends.
	 * Page size is derived from the rendered card width, so it follows the
	 * responsive layout instead of being duplicated here.
	 */
	document.querySelectorAll( '[data-carousel]' ).forEach( function ( carousel ) {
		const track = carousel.querySelector( '[data-carousel-track]' );
		const prev = carousel.querySelector( '[data-carousel-prev]' );
		const next = carousel.querySelector( '[data-carousel-next]' );
		const current = carousel.querySelector( '[data-carousel-current]' );
		const total = carousel.querySelector( '[data-carousel-total]' );

		if ( ! track || ! prev || ! next ) {
			return;
		}

		const pad = function ( n ) {
			return String( n ).padStart( 2, '0' );
		};

		const pageCount = function () {
			// Round to absorb sub-pixel track widths.
			return Math.max( 1, Math.round( track.scrollWidth / track.clientWidth ) );
		};

		const pageIndex = function () {
			return Math.round( track.scrollLeft / track.clientWidth );
		};

		const sync = function () {
			const pages = pageCount();
			const page = Math.min( pageIndex(), pages - 1 );

			if ( current ) {
				current.textContent = pad( page + 1 );
			}

			if ( total ) {
				// translators-free: rendered as "01 of 03".
				total.textContent = ' of ' + pad( pages );
			}

			// Hide the pager entirely when everything already fits.
			carousel.classList.toggle( 'has-single-page', pages < 2 );

			prev.disabled = page <= 0;
			next.disabled = page >= pages - 1;
		};

		const scrollByPage = function ( direction ) {
			track.scrollBy( { left: direction * track.clientWidth, behavior: 'smooth' } );
		};

		prev.addEventListener( 'click', function () {
			scrollByPage( -1 );
		} );

		next.addEventListener( 'click', function () {
			scrollByPage( 1 );
		} );

		let ticking = false;
		track.addEventListener( 'scroll', function () {
			if ( ticking ) {
				return;
			}

			ticking = true;
			window.requestAnimationFrame( function () {
				sync();
				ticking = false;
			} );
		} );

		window.addEventListener( 'resize', sync );
		sync();
	} );

	/**
	 * Reveal sections as they scroll into view.
	 *
	 * Opt-in per element via the `is-revealable` class, which CSS only acts on
	 * once this script adds `has-reveal` to <html> — so with JavaScript off,
	 * or with reduced motion requested, everything is simply visible.
	 * Animates opacity and transform only, so it cannot cause layout shift.
	 */
	const reveals = document.querySelectorAll( '.is-revealable' );
	const wantsMotion = ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( reveals.length && wantsMotion && 'IntersectionObserver' in window ) {
		document.documentElement.classList.add( 'has-reveal' );

		const observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-revealed' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -10% 0px' }
		);

		reveals.forEach( function ( el ) {
			observer.observe( el );
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
