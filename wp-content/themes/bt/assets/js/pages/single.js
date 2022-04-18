($ => {

    //======================================================================
    // Form choose companies
    //======================================================================

    // add selected companies to a hidden value
    $('.single .form .select-companies .companies-list .company [type="checkbox"]').on('change', e => {
        const $this = $(e.currentTarget);
        const selectedCompanies = [];

        $('.single .form .companies-list .company [type="checkbox"]:checked').map((key, cb) => {
            cb = $(cb);

            selectedCompanies.push(cb.data('company'));
        });

        if (selectedCompanies.length > 0) {
            $('.single .form .wpcf7 [name="comps_gfc"]').val(selectedCompanies.join(', '));
        } else {
            $('.single .form .wpcf7 [name="comps_gfc"]').val('');
        }
    });

    // validate selected companies
    $('.single .form .wpcf7 input[type="submit"]').on('click', e => {
        if ($(e.currentTarget).closest('form').find('[name="comps_gfc"]').val() === '') {
            e.preventDefault();

            if ($('.single .form .select-companies .bt-error').length === 0) {
                $('.single .form .select-companies').append('<span class="bt-error">יש לבחור בחברה אחת לפחות</span>');
            }
        } else {
            $('.single .form .select-companies .bt-error').remove();
        }
    });

    // //======================================================================
    // // Form choose companies
    // //======================================================================

})(jQuery);