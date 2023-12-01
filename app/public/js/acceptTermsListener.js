document.addEventListener('DOMContentLoaded', function() {
    const acceptTermsButton = document.getElementById('accept-terms');
    const agreeTerms = document.getElementById('registration_form_agreeTerms');

    agreeTerms.addEventListener('change', getTimeZone);
    acceptTermsButton.addEventListener('click', function () {
        agreeTerms.checked = true;
        getTimeZone();
    });
});

function getTimeZone() {
    let userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    let timeZoneForm = document.getElementById('time-zone');
    timeZoneForm.value = userTimeZone;
}