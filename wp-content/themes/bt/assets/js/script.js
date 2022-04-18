var AOS = require('aos');

const $myComparesCount = jQuery('li.my-compares .count');

function refreshComparePackagesCount() {
	setTimeout(() => {
		storedPackages = JSON.parse(localStorage.getItem('comparePackages'));
		let count = 0;

		if (storedPackages !== null) {
			Object.keys(storedPackages).map(key => {
				count += storedPackages[key].length;
			});
		}

		$myComparesCount.text(count);
	}, 1);
}

($ => {

	//======================================================================
	// WHOLE SITE
	//======================================================================

	//----------------------------------------------------------------------
	// Google analytics
	//----------------------------------------------------------------------

	if (typeof (dataLayer) !== 'undefined' && btSystemGlobals.userId != 0) {
		dataLayer.push({
			event: 'login',
			user_id: btSystemGlobals.userId
		});
	}

	//----------------------------------------------------------------------
	// Google analytics - end
	//----------------------------------------------------------------------

	//----------------------------------------------------------------------
	// Logo
	//----------------------------------------------------------------------

	$(document).on('scroll', () => {
		if ($(window).scrollTop() > 0) {
			$('body').addClass('scrolled');
		} else {
			$('body').removeClass('scrolled');
		}
	});

	//----------------------------------------------------------------------
	// Logo - end
	//----------------------------------------------------------------------

	//----------------------------------------------------------------------
	// Menu
	//----------------------------------------------------------------------

	// display fixed header on scroll
	const headerMenu = $('.site-header .bottom');
	$(document).on('scroll', () => {
		if (screen.width < 1024) {
			$('.fixed-header').fadeIn(250);
			return;
		}

		const scrollTop = $(window).scrollTop();
		if (scrollTop >= (headerMenu.offset().top + headerMenu.height())) {
			$('.fixed-header').fadeIn(250);
		} else if (scrollTop <= headerMenu.offset().top) {
			$('.fixed-header').fadeOut(150);
		}
	});
	$(document).trigger('scroll');

	$(window).on('resize', () => {
		if (screen.width < 1024) {
			$(document).trigger('scroll');
		}
	});

	$('.hamburger').on('click', e => {
		const $this = $(e.currentTarget);

		if ($this.hasClass('toggling')) return;
		else $this.addClass('toggling');

		$this.closest('header').toggleClass('menu-open').find('nav').slideToggle(300, () => $this.removeClass('toggling'));
	});

	$('.menu-item-has-children [class^="icon-"]').on('click', e => {
		if (screen.width > 1023) return;

		const $this = $(e.currentTarget);

		if ($this.hasClass('toggling')) return;
		else $this.addClass('toggling');

		$this.closest('li').find('.submenu').slideToggle(300, () => $this.removeClass('toggling'));
	});

	//----------------------------------------------------------------------
	// Menu - end
	//----------------------------------------------------------------------

	//----------------------------------------------------------------------
	// Search bar (Mobile)
	//----------------------------------------------------------------------

	// open search bar
	$('.fixed-header .mobile .search .open-search').on('click', e => {
		const $this = $(e.currentTarget);
		const $search = $this.closest('.search');

		if ($search.hasClass('toggling')) return;
		else $search.addClass('toggling');

		$search.addClass('active').find('.search-inner').fadeIn(500, () => $search.removeClass('toggling'));
	});

	// delete search value or close the search bar
	$('.fixed-header .mobile .search .icon-x').on('click', e => {
		const $this = $(e.currentTarget);
		const $search = $this.closest('.search');
		const $searchInput = $search.find('[name="s"]');

		if ($search.hasClass('toggling')) return;
		else $search.addClass('toggling');

		if ($searchInput.val() != '') $searchInput.val('');
		else $('.mobile .search').removeClass('active').find('.search-inner').fadeOut(500, () => $search.removeClass('toggling'));
	});

	//----------------------------------------------------------------------
	// Search bar (Mobile) - end
	//----------------------------------------------------------------------

	//----------------------------------------------------------------------
	// Heading with toggle
	//----------------------------------------------------------------------

	$(document).on('click', '.heading-with-toggle .toggle-cat', e => {
		$this = $(e.currentTarget);

		if ($this.hasClass('toggling')) return;

		$this.addClass('toggling');

		$this.children('[class^="icon-"]').toggleClass(['icon-chevron-up', 'icon-chevron-down']);
		$this.closest('.heading-with-toggle').next().slideToggle(500, () => $this.removeClass('toggling'));
	});

	//----------------------------------------------------------------------
	// Heading with toggle - end
	//----------------------------------------------------------------------

	//======================================================================
	// Companies list
	//======================================================================

	const companiesGrid = $('.companies-list');

	$(window).on('resize', () => {
		if (companiesGrid.length === 0) return;

		companiesGrid.map((key, list) => {
			list = $(list);

			resizedCompaniesGTCLength = list.css('grid-template-columns').split(' ').length;

			list.children().removeClass('last-in-row');

			list.children(`:nth-child(${resizedCompaniesGTCLength}n)`).addClass('last-in-row');
		});
	});

	$(document).trigger('resize');

	//======================================================================
	// Categories list - end
	//======================================================================

	//======================================================================
	// My compares link
	//======================================================================

	let LSComparedPackages = JSON.parse(localStorage.getItem('comparePackages'));
	let count = 0;

	// set compare packages count on page load
	if (LSComparedPackages !== null) {

		Object.keys(LSComparedPackages).map(key => {
			count += LSComparedPackages[key].length;
		});
	}

	$myComparesCount.text(count);

	// set compare packages count on checkbox change
	$(document).on('change', '.compare-for-me [type="checkbox"]', () => {
		refreshComparePackagesCount();
	});

	//======================================================================
	// My compares link - end
	//======================================================================

	//======================================================================
	// AOS
	//======================================================================

	AOS.init();

	//======================================================================
	// AOS - end
	//======================================================================

	//======================================================================
	// WHOLE SITE - END
	//======================================================================

})(jQuery);