jQuery(document).ready(function($) {
    $("#google_recpatcha_v3-enabled").on("change", onGoogleCaptchaChange);
    $("#hcaptcha-enabled").on("change", onhCaptchaChange);
});

function onGoogleCaptchaChange(event) {
    let hcaptcha = jQuery("#hcaptcha-enabled");
    if(event.currentTarget.checked) {
        jQuery(hcaptcha).prop("checked", false);
    }
}

function onhCaptchaChange(event) {
    let google_captcha = jQuery("#google_recpatcha_v3-enabled");
    if(event.currentTarget.checked) {
        jQuery(google_captcha).prop("checked", false);
    }
}
