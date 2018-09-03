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
     * to wp option page.
     */
    $("#tos-admin-form").parsley();

    /**
     * WordPress ajax ToS update,
     * This method is connected with tos_update_ajax_handler() function inside
     * settings class.
     *
     */
    $("#tos-admin-form #update").click(function (event) {    //event
        event.preventDefault();
        $(this).addClass("spinner-demo disabled");

        $.post(tos_ajax_obj.ajax_url, {                 //POST request
            _ajax_nonce: tos_ajax_obj.nonce,            //nonce
            action: "tos_update_fields",                //action
            title: this.value                           //data
        }, function (data) {                            //callback
            let obj = JSON.parse(data);
            $.each(obj.verantwortlich, function (i, val) {
                if (null != val)
                    $("input[name ='rrze_tos[rrze_tos_responsible_" + i + "]']").val(val);
            });
            $.each(obj.webmaster, function (i, val) {
                if (null != val)
                    $("input[name ='rrze_tos[rrze_tos_content_" + i + "]']").val(val);
            });
        }).done(function (e) {
            let obj = JSON.parse(e);
            $('#ajax-response').removeClass("invisible visible notice-error").fadeIn().addClass("visible notice-success is-dismissible").delay(4000).fadeOut();
            $('#ajax-response p').empty().append(obj.success);
        }).fail(function (e) {
            $('#ajax-response').removeClass("invisible notice-error").fadeIn().addClass("visible notice-error").delay(4000).fadeOut();
            $('#ajax-response p').empty().append(e.responseText);
        }).always(function () {
            $(this).removeClass("spinner-demo disabled");
        }.bind(this));
    });
});