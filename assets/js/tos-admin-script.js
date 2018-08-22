jQuery(document).ready(function ($) {
    /**
     * Only show the text area if no is selected.
     *
     * @type {*|jQuery|HTMLElement}
     */
    function checkConformity() {
        let rrzeTosConformity = $("input[name='rrze_tos[rrze_tos_conformity]']:checked", "#tos-admin-form").val();
        if ('2' === rrzeTosConformity) {
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').show();
        } else {
            // $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').hide();
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').hide();
        }
    }

    // Check when load plugin settings
    checkConformity();

    /**
     * Show or hide text area when input radio is modified.
     */
    $("#tos-admin-form input").on('change', function () {
        checkConformity();
    });

    /**
     * Validate client-side form before send data
     */
    $("#tos-admin-form").parsley();

    $("#tos-admin-form #update").click(function () {             //event
        $('#wpbody-content .wrap').prepend('<div class="updated settings-error notice is-dismissible"><p>Updating data</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Diese Meldung ausblenden.</span></button></div>');

        $.post(tos_ajax_obj.ajax_url, {        //POST request
            _ajax_nonce: tos_ajax_obj.nonce,   //nonce
            action: "tos_update_fields",       //action
            title: this.value                  //data
        }, function (data) {                    //callback
            console.log(data);
            $('.updated').remove();
            $('#wpbody-content .wrap').prepend('<div class="updated settings-error notice is-dismissible"><p>Updated</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Diese Meldung ausblenden.</span></button></div>').delay(100);
        });
    });
});