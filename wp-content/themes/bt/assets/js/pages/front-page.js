// import '../../../node_modules/jquery/src/jquery';
// import '../script';

($ => {

    const headerHeight = $('.site-header').height();
    const bannersList = $('.banners .banners-list');

    //======================================================================
    // Banners
    //======================================================================

    // $('.banners .banner-img img').css('height', `calc(100vh - ${headerHeight}px - 82px)`);

    if (bannersList.data('slides') > 1) {
        bannersList.slick({
            prevArrow: '<button type="button" class="slick-prev"><span class="icon-chevron-right"></span></button>',
            nextArrow: '<button type="button" class="slick-next"><span class="icon-chevron-left"></span></button>',
        });
    }

    //======================================================================
    // Banners - end
    //======================================================================

    //======================================================================
    // Welcome box
    //======================================================================

    $(window).on('resize', () => $('.welcome-box .bg-wrapper img').height($('.welcome-box .box-content').height() + 70));

    $(document).trigger('resize');

    //======================================================================
    // Welcome box - end
    //======================================================================

})(jQuery);