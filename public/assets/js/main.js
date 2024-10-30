jQuery(document).ready(function ($) {
    $("form.infocob-crm-forms select.display_search.select2JS").select2({
        allowClear: true,
    });

    $("form.infocob-crm-forms select.select2JS").not(".display_search").select2({
        allowClear: true,
        minimumResultsForSearch: -1
    });

    let forms = $("form.infocob-crm-forms");
    $(forms).on("submit", function (event) {
        if ($(this).attr("target") !== "_blank") {
            $(event.currentTarget).find(".if-btn-submit .infocob-crm-forms-ajax-loader").addClass("visible");
        }

        $(event.currentTarget).find(".if-btn-submit > button").prop("disabled", true);
    });

    $(forms).on("input", function (event) {
        $(event.currentTarget).find(".if-btn-submit > button").prop("disabled", false);
    });

    if (typeof (grecaptcha) !== 'undefined' && icf_form && icf_form.google_recaptcha_v3_enabled && icf_form.google_recaptcha_v3_client_key) {
        grecaptcha.ready(function () {
            $("form.infocob-crm-forms").each((index, form) => {
                $(form).on("submit.recaptcha", (event) => {
                    event.preventDefault();
                    grecaptcha.execute(icf_form.google_recaptcha_v3_client_key, {action: 'submit'}).then(function (token) {
                        $(form).find("input[name='recaptcha_token']").val(token);
                        $(form).off("submit.recaptcha");
                        $(form).submit();
                    });
                })
            });
        });
    }

    // hCaptcha
    if (typeof (hcaptcha) !== 'undefined' && icf_form && icf_form.hcaptcha_enabled && icf_form.hcaptcha_client_key) {
        $("form.infocob-crm-forms").each((index, form) => {
            let hCaptchaContainerElement = $(form).find(".h-captcha");
            if (hCaptchaContainerElement.length) {
                hCaptchaContainer = hCaptchaContainerElement[0];
            }

            // Manage theme
            let body = $("body");
            let theme = "light";
            let themeConfig = icf_form.hcaptcha_theme ?? "";
            if(themeConfig === "") { // Theme auto
                if($(body).data("theme") !== undefined && $(body).data("theme") !== "") {
                    theme = $(body).data("theme");
                }
            } else {
                theme = themeConfig;
            }

            if(!["light", "dark"].includes(theme.toLowerCase())) {
                theme = "light";
            }

            // Generate ID hcaptcha container (div)
            let widgetID = hcaptcha.render(hCaptchaContainer, {
                sitekey: icf_form.hcaptcha_client_key,
                'error-callback': function (error) {
                    console.error(error);
                },
                size: ((icf_form.hcaptcha_size ?? "") === "") ? "normal" : icf_form.hcaptcha_size,
                theme: theme
            });

            if ((icf_form.hcaptcha_size ?? "") === "invisible") {
                // Resolve captcha on submit
                $(form).on("submit.hcaptcha", (event) => {
                    event.preventDefault();

                    hcaptcha.execute(widgetID, {async: true}).then(({response, key}) => {
                        $(form).off("submit.hcaptcha");
                        $(form).submit();
                    }).catch(error => {
                        console.error(error);
                    });
                });
            }
        });
    }

});
