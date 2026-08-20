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
	 * FAQ "Read More" controls.
	 *
	 * The answer ships complete and unclamped. Clamping and the button are both
	 * applied here, so a visitor without JavaScript reads the whole answer and
	 * never sees a control that cannot work.
	 */
	document.querySelectorAll( '[data-faq-more]' ).forEach( function ( button ) {
		const answer = document.getElementById( button.getAttribute( 'aria-controls' ) );

		if ( ! answer ) {
			return;
		}

		answer.classList.add( 'is-clamped' );

		// Nothing is being hidden by the clamp, so the control would be a no-op.
		if ( answer.scrollHeight <= answer.clientHeight + 1 ) {
			answer.classList.remove( 'is-clamped' );

			return;
		}

		button.hidden = false;

		button.addEventListener( 'click', function () {
			const expanded = 'true' === button.getAttribute( 'aria-expanded' );

			button.setAttribute( 'aria-expanded', String( ! expanded ) );
			answer.classList.toggle( 'is-clamped', expanded );
			button.textContent = expanded
				? button.dataset.labelMore
				: button.dataset.labelLess;
		} );
	} );

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

		/*
		 * Optional jump-to controls — the property gallery's thumbnail strip.
		 * They address slides, not pages, since a page holds two of them at the
		 * widest breakpoint.
		 */
		const thumbs = Array.from( carousel.querySelectorAll( '[data-carousel-goto]' ) );

		const slideAt = function ( index ) {
			return track.children[ index ] || null;
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

			/*
			 * Mark the thumbnails whose slides are in view, by midpoint rather
			 * than by both edges: slide widths are fractional, so an edge test
			 * loses the last slide to a sub-pixel rounding error.
			 */
			thumbs.forEach( function ( thumb ) {
				const slide = slideAt( Number( thumb.dataset.carouselGoto ) );
				const middle = slide ? slide.offsetLeft + slide.offsetWidth / 2 - track.scrollLeft : -1;
				const shown = middle >= 0 && middle <= track.clientWidth;

				thumb.classList.toggle( 'is-current', shown );
				thumb.setAttribute( 'aria-current', shown ? 'true' : 'false' );
			} );
		};

		thumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				const slide = slideAt( Number( thumb.dataset.carouselGoto ) );

				if ( slide ) {
					track.scrollTo( { left: slide.offsetLeft, behavior: 'smooth' } );
				}
			} );
		} );

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

		// Filtering hides cards, which changes how many pages there are.
		track.addEventListener( 'growmodo:relayout', sync );

		sync();
	} );

	/**
	 * Property filters.
	 *
	 * A filter is a view over the results already on the page, so this hides
	 * cards instead of asking the server for a different set — the search box
	 * beside it is the control that does that. Nothing is submitted, so every
	 * change applies at once and there is no button to press.
	 *
	 * The markup owns the field list: each select names the card attribute it
	 * reads and how to compare it, so adding a filter is a template change.
	 */
	document.querySelectorAll( '[data-filter-target]' ).forEach( function ( group ) {
		const results = document.querySelector( group.dataset.filterTarget );

		if ( ! results ) {
			return;
		}

		const selects = Array.from( group.querySelectorAll( '[data-filter]' ) );
		const cards = Array.from( results.children );
		const scope = group.parentElement;
		const count = scope.querySelector( '[data-filter-count]' );
		const empty = scope.querySelector( '[data-filter-empty]' );

		/**
		 * Does one card satisfy one select? An unset select matches everything,
		 * which is what makes its first option double as the cleared state.
		 */
		const matches = function ( card, select ) {
			const want = select.value;

			if ( '' === want ) {
				return true;
			}

			const have = card.dataset[ select.dataset.filter ];

			if ( 'min' === select.dataset.match ) {
				return Number( have ) >= Number( want );
			}

			/*
			 * Half-open: the lower bound is included, the upper is not. Bands
			 * share their boundary number ("under 1,500" next to "1,500 to
			 * 3,000"), so an inclusive upper bound would put a property of
			 * exactly 1,500 in both of them.
			 */
			if ( 'range' === select.dataset.match ) {
				const bounds = want.split( '-' );

				return Number( have ) >= Number( bounds[ 0 ] ) &&
					( '' === bounds[ 1 ] || Number( have ) < Number( bounds[ 1 ] ) );
			}

			return have === want;
		};

		const apply = function () {
			let shown = 0;

			cards.forEach( function ( card ) {
				const keep = selects.every( function ( select ) {
					return matches( card, select );
				} );

				card.hidden = ! keep;

				if ( keep ) {
					shown += 1;
				}
			} );

			if ( count ) {
				count.textContent = count.dataset.template
					.replace( '%1$s', String( shown ) )
					.replace( '%2$s', String( cards.length ) );
			}

			if ( empty ) {
				empty.hidden = 0 !== shown;
			}

			/*
			 * Back to the first page: the old scroll position means nothing
			 * now. Instant, not smooth — the track's CSS animates assignments
			 * to scrollLeft, and the counter below would then read the position
			 * the carousel is still travelling away from.
			 */
			results.scrollTo( { left: 0, behavior: 'instant' } );
			results.dispatchEvent( new CustomEvent( 'growmodo:relayout' ) );
		};

		selects.forEach( function ( select ) {
			select.addEventListener( 'change', apply );
		} );
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
