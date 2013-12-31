WebFontConfig = {
    google: { families: [ 'Oswald::latin' ] }
};
(function () {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
        '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
})();

jQuery(document).ready(function ($) {

    // Disable deletion of admins, because you just can't fix stupid
    var optionset = $('#the-options');
    optionset.find('input[name=include-administrator]').attr('disabled', 'disabled');
    optionset.find('label[name=include-administrator]').css('color', 'rgb(180,180,180)');

    // Hide the confirmation options by default
    confirmation = $('#confirmation');
    confirmation.hide();

    // Check the radio button
    $('input:radio[name="operation"]').change(
        function () {
            if ($(this).is(':checked') && $(this).val() == 'test') {
                confirmation.hide();
                $('input[name=confirm-deletes]').attr('checked', false);
                $('input[name=confirm-backup]').attr('checked', false);
            }
            if ($(this).is(':checked') && $(this).val() == 'live') {
                confirmation.show();
            }
        }
    );

    // Hide the user limit field by default
    userlimit = $('#userlimit');
    userlimit.hide();

    // Conditionally Show the user limit field
    $('input:radio[name="limiter"]').change(
        function () {
            if ($(this).is(':checked') && $(this).val() == 'unlimited') {
                userlimit.hide();
            }
            if ($(this).is(':checked') && $(this).val() == 'limited') {
                userlimit.show();
            }
        }
    );
});