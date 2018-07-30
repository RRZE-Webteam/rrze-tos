jQuery(document).ready(function ($) {
    /**
     * Only show the text area if no is selected.
     *
     * @type {*|jQuery|HTMLElement}
     */
    function checkConformity(){
        let rrzeTosConformity = $("input[name='rrze_tos[rrze_tos_conformity]']:checked", "#tos-admin-form").val();
        if ('2' === rrzeTosConformity) {
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').show();
        } else {
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').hide();
        }
    }

    // Check when load plugin settings
    checkConformity();

    /**
     * Show or hide text area when input radio is modify.
     */
    $("#tos-admin-form input").on('change', function () {
        checkConformity();
    });

    /**
     * Validate client-side form before send data
     */
    // $("#tos-admin-form").parsley();
});