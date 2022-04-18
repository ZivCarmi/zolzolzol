const setPackageFormValue = () => {
    // use selected companies as value in hidden field
    jQuery('.product .sub-block .form [name="pack_spf"]').map((key, input) => {
        input = jQuery(input);

        const packageName = input.closest('.product').find('.woocommerce-loop-product__title').text();

        input.val(`קטגוריות: ${input.closest('.sub-block').data('categories')} | חבילה: ${packageName}`);
    });
};

setPackageFormValue();

($ => {

    //======================================================================
    // Compare
    //======================================================================

    // set compare list to default behavior
    const initiateCompareList = () => {
        // remove default classes of berocket plugin
        $('.compare-prices .compare-box .select-list').removeClass('bapf_ccolaps bapf_ocolaps');

        $('.compare-prices .list-open').removeClass('list-open');

        $('.compare-prices .compare-box .select-list .bapf_body').hide();

        $('.compare-prices .compare-box .bapf_head .bapf_colaps_smb').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    };

    // toggle compare list
    $(document).on('click', '.compare-prices .compare-box .bapf_head', e => {
        $this = $(e.currentTarget);

        if ($this.hasClass('toggling')) return;

        $this.addClass('toggling');

        $('.compare-prices .list-open .bapf_head').not($this).find('.fa').toggleClass(['fa-chevron-up', 'fa-chevron-down']).closest('.bapf_head').next().slideUp(100).closest('.list-open').removeClass('list-open');

        $this.find('.fa').toggleClass(['fa-chevron-up', 'fa-chevron-down']);

        $this.next().slideToggle(100, () => $this.removeClass('toggling')).closest('.compare-box').toggleClass('list-open');
    });

    // prevent the click on filter inner
    $(document).on('click', '.compare-prices .compare-box .select-list', e => {
        e.stopPropagation();
    });

    // close the filter dropdown while click anywhere on document
    $(document).on('click', () => {
        $('.compare-prices .list-open .bapf_head').trigger('click');
    });

    $(document).ajaxSuccess(function (event, xhr, settings) {
        initiateCompareList();
        checkComparedPackages();
    });

    initiateCompareList();

    //======================================================================
    // Compare - end
    //======================================================================

    //======================================================================
    // Companies representatives
    //======================================================================

    // add selected companies to a hidden value
    $('.companies-reps .companies-list [type="checkbox"]').on('change', e => {
        const selectedCompanies = [category + ' -'];

        $('.companies-reps .companies-list [type="checkbox"]:checked').map((key, company) => {
            selectedCompanies.push($(company).attr('name'));
        });

        if (selectedCompanies.length > 1) {
            $('.site-modal .form [name="comps_cof"]').val(selectedCompanies.join(' '));
        } else {
            $('.site-modal .form [name="comps_cof"]').val('');
        }
    });

    // validate selected companies
    $('.site-modal .form .wpcf7 input[type="submit"]').on('click', e => {
        if ($(e.currentTarget).closest('form').find('[name="comps_cof"]').val() === '') {
            e.preventDefault();

            if ($('.site-modal .companies-reps .bt-error').length === 0) {
                $('.site-modal .companies-reps').append('<span class="bt-error">יש לבחור בחברה אחת לפחות</span>');
            }
        } else {
            $('.site-modal .companies-reps .bt-error').remove();
        }
    });

    //======================================================================
    // Companies representatives - end
    //======================================================================

    //======================================================================
    // Modal
    //======================================================================

    const popup_overlay_target = '.popups-overlay';
    const popup_overlay_animation_duration = 300;

    const bt_hide_popups_overlay = () => $(popup_overlay_target).fadeOut(popup_overlay_animation_duration);
    const bt_show_popups_overlay = () => $(popup_overlay_target).fadeIn(popup_overlay_animation_duration);

    let $modal = $('.site-modal');

    if ($modal.length > 0) {
        $(document).on('click', '.use-modal', e => {
            if ($modal.hasClass('toggling')) return;

            const modalData = $(e.currentTarget).data('modal');

            const $ref_modal = $(`.site-modal .${modalData}`).closest('.site-modal');

            bt_show_popups_overlay();
            $ref_modal.addClass('active').fadeIn(400);
        });

        // close modal when clicking the overlay
        $(popup_overlay_target).on('click', () => {
            $modal.addClass('toggling');

            $modal.removeClass('active').fadeOut(400, () => $modal.removeClass('toggling'));

            bt_hide_popups_overlay();
        });

        $('.site-modal .exit-modal').on('click', () => {
            $modal.addClass('toggling');

            $modal.removeClass('active').fadeOut(400, () => $modal.removeClass('toggling'));

            bt_hide_popups_overlay();
        });
    }

    //======================================================================
    // Modal - end
    //======================================================================

    //======================================================================
    // Package
    //======================================================================

    // $(document).on('click', '.product .main-block', e => {
    //     const $this = $(e.currentTarget);

    //     console.log(1234);

    //     $this.find('.toggle-package').trigger('click');
    // });

    // set checked for storaged compared packages
    (checkComparedPackages = () => {
        if (localStorage.getItem('comparePackages') !== null) {
            const getPackges = JSON.parse(localStorage.getItem('comparePackages'));

            Object.keys(getPackges).map(key => {
                getPackges[key].map(packageID => {
                    $(`.product .mid-column .compare-for-me [type="checkbox"][value="${packageID}"]`).prop('checked', true);
                });
            });
        }
    })();

    // store checked packages to compare
    $(document).on('change', '.product .mid-column .compare-for-me [type="checkbox"]', e => {
        const $this = $(e.currentTarget);
        const value = $this.val();
        const category = $this.closest('.product').find('.sub-block').data('categories');
        let storePackages = {};

        // Parse the serialized data back into an aray of objects
        storePackages = JSON.parse(localStorage.getItem('comparePackages')) || {};

        // Either remove the current package or add it
        if (storePackages.hasOwnProperty(category)) {
            if (storePackages[category].includes(value)) {
                storePackages[category].splice(storePackages[category].indexOf(value), 1);
            } else {
                // Push the new data (whether it be an object or anything else) onto the array
                storePackages[category].push(value);
            }
        } else {
            storePackages[category] = [value];
        }

        // only when checkbox is unchecked, delete categories (key) in the object that has empty arrays
        if (!$this.is(':checked')) {
            for (const categoryKey in storePackages) {
                if (storePackages[categoryKey].length == 0) {
                    delete storePackages[categoryKey];
                }
            }
        }

        if (Object.keys(storePackages).length === 0) {
            localStorage.removeItem('comparePackages');
            return;
        }

        // Re-serialize the array back into a string and store it in localStorage
        localStorage.setItem('comparePackages', JSON.stringify(storePackages));
    });

    // toggle package details dropdown
    $(document).on('click', '.product .main-block', e => {
        const $this = $(e.currentTarget);

        if ($this.hasClass('toggling')) return;

        $this.addClass('toggling');

        $this.find('.last-column').find('[class^="icon-"]').toggleClass(['icon-chevron-down', 'icon-chevron-up']);
        $this.closest('.product').find('.sub-block').slideToggle(400, () => $this.removeClass('toggling'));
    });

    $(document).on('click', '.product .mid-column .package-action-buttons', e => {
        e.stopPropagation();
    });

    //======================================================================
    // Package - end
    //======================================================================

    //======================================================================
    // Share
    //======================================================================

    $(document).on('click', '.product .mid-column .share-btn', e => {
        const packageLink = $(e.currentTarget).data('product-link');

        $('.site-modal .share-package .facebook').attr('href', `https://www.facebook.com/sharer/sharer.php?u=${packageLink}`);
        $('.site-modal .share-package .whatsapp').attr('href', `https://api.whatsapp.com/send?text=%D7%94%D7%99%D7%99,%20%D7%9E%D7%A6%D7%90%D7%AA%D7%99%20%D7%97%D7%91%D7%99%D7%9C%D7%94%20%D7%A9%D7%90%D7%95%D7%9C%D7%99%20%D7%AA%D7%A2%D7%A0%D7%99%D7%99%D7%9F%20%D7%90%D7%95%D7%AA%D7%9A${packageLink}`);
    });

    //======================================================================
    // Share - end
    //======================================================================

})(jQuery);