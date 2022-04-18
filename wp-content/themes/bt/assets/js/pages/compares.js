($ => {

    //======================================================================
    // Package
    //======================================================================

    let fetchPackages;

    const outputLoaderHTML = () => {
        let loader = '<div class="lds-roller">';
        for (let i = 1; i < 8; i++) {
            loader += '<div></div>';
        }
        loader += '</div>';

        return loader;
    }

    (fetchPackages = async () => {
        const packages = localStorage.getItem('comparePackages');
        // get compared packages
        const packages_obj = JSON.parse(packages);

        // check if there are packages
        if (packages_obj === null || Object.keys(packages_obj).length === 0) {
            if ($('.my-compares .all-compares').length > 0) {
                $('.compare-packages .my-compares').prepend(outputLoaderHTML());
                // return;

                setTimeout(() => {
                    $('.compare-packages .lds-roller').fadeOut(400, () => {
                        $('.compare-packages .lds-roller').remove();

                        $('.my-compares .all-compares').fadeOut(300, () => {
                            $('.compare-packages .compare-content').hide();
                            $('.my-compares .all-compares').remove();

                            refreshComparePackagesCount();
                        });
                    });
                }, 900);
            }

            setTimeout(() => {
                $('.compare-packages .my-compares').prepend('<span class="no-packages">לא נבחרו חבילות להשוואה</span>');
            }, 1601);
            return;
        } else {
            $('.compare-packages .my-compares').prepend(outputLoaderHTML());
        }

        const axios_form_data = new FormData();

        axios_form_data.append('action', 'bt_get_compared_packages');
        axios_form_data.append('security', btSystemGlobals.ajaxNonce);
        axios_form_data.append('packages', packages);

        const res = await axios.post(btSystemGlobals.ajaxUrl + '/?compare_packages', axios_form_data);

        if (res.data) {
            $('.compare-packages .lds-roller').fadeOut(400, e => {
                $('.compare-packages .lds-roller').remove();

                $('.my-compares .all-compares').removeClass('working');

                $('.compare-packages .compare-content .compared-packages').html(res.data).fadeIn(200);
                $('.compare-packages .compare-content').fadeIn(200);

                setPackageFormValue();
                refreshComparePackagesCount();
            });

        }
    })();

    // handle form with selected packages value
    $(document).on('change', '.product .main-block .check-package [type="checkbox"]', e => {
        const selectedPackages = [];

        $('.product .main-block .check-package [type="checkbox"]:checked').map((key, cb) => {
            selectedPackages.push($(cb).val() + '<br>');
        });

        if (selectedPackages.length > 0) {
            $('.compare-packages .compare-content .form .overlay').fadeOut(200);
        } else {
            $('.compare-packages .compare-content .form .overlay').fadeIn(200);
        }

        $('.compare-packages .compare-content .form [name="packs_cpf"]').val(selectedPackages.join(''));
    });

    $(document).on('click', '.product .main-block .check-package', e => {
        e.stopPropagation();
    });

    // prevent submit if at least 1 checkbox wasn't checked
    $('.compare-packages .compare-content .form [type="submit"]').on('click', e => {
        if ($('.product .main-block .check-package [type="checkbox"]:checked').length == 0) {
            e.preventDefault();
        }
    });

    $(document).on('click', '.product .remove-package', e => {
        e.stopPropagation();

        const $this = $(e.currentTarget);
        const $packageID = $this.data('package-id');

        const LSPackages = JSON.parse(localStorage.getItem('comparePackages'));

        Object.keys(LSPackages).map(key => {
            LSPackages[key] = LSPackages[key].filter(packageID => packageID != $packageID);

            if (LSPackages[key].length == 0) {
                delete LSPackages[key];
            }
        });

        if (Object.keys(LSPackages).length === 0) {
            localStorage.removeItem('comparePackages');
        } else {
            localStorage.setItem('comparePackages', JSON.stringify(LSPackages));
        }

        $('.my-compares .all-compares').addClass('working');

        // run axios to get the refreshed packages
        fetchPackages();
    });

    //======================================================================
    // Package
    //======================================================================

})(jQuery);