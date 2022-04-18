($ => {

    //======================================================================
    // Choose company & Set form Mail
    //======================================================================

    $('.choose-company .companies-list .company [type="checkbox"]').on('change', e => {
        const $this = $(e.currentTarget);

        $('.choose-company .companies-list .company').removeClass('checked');
        $('.choose-company .companies-list .company [type="checkbox"]').not($this).prop('checked', false);

        if ($this.is(':checked')) {
            $this.closest('.company').addClass('checked');
            $('.disconnection-form .wpcf7').show();
            $('.disconnection-form .wpcf7 [name="dc_comp_mail"]').val($this.data('email'));
            $('.disconnection-form .wpcf7 [name="dc_comp"]').val($this.data('company'));
        } else {
            $('.disconnection-form .wpcf7').hide();
            $('.disconnection-form .wpcf7 [name="dc_comp_mail"]').val('');
            $('.disconnection-form .wpcf7 [name="dc_comp"]').val('');
        }
    });

    //======================================================================
    // Choose company & Set form Mail - end
    //======================================================================

})(jQuery);