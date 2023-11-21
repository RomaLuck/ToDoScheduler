let countrySelect = document.getElementById('countryId');
let selectedTimezone = document.getElementById("selectedTimezone");
countrySelect.addEventListener('change', function () {
    let selectedOption = countrySelect.options[countrySelect.selectedIndex];
    let countryCode = selectedOption.value;
    let params = new URLSearchParams();
    params.append('key', 'XSICU5DY1Z6H');
    params.append('format', 'json');
    params.append('country', countryCode);
    fetch('https://api.timezonedb.com/v2.1/list-time-zone?' + params)
        .then(response => response.json())
        .then(data => {
            let zones = data.zones;
            zones.forEach(zone => {
                let option = document.createElement("option");
                option.textContent = zone.zoneName;
                selectedTimezone.appendChild(option);
            });
        });
});

const acceptTermsButton = document.getElementById('accept-terms');
const agreeTerms = document.getElementById('form_agreeTerms');
agreeTerms.addEventListener('change', getTimeZone)
acceptTermsButton.addEventListener('click', function () {
    agreeTerms.checked = true;
    getTimeZone();
})

function getTimeZone() {
    let userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    let option = document.createElement("option");
    option.textContent = userTimeZone;
    selectedTimezone.appendChild(option);
}