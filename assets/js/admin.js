jQuery(document).ready(function($) {
    function checkConformity() {
        var rrzeTosConformity = $("input[name='rrze_tos[rrze_tos_conformity]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosConformity) {
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').hide();
        } else {
            $("textarea[name='rrze_tos[rrze_tos_no_reason]']").parents('tr').show();
        }
    }

    function checkNewSection() {
        var rrzeTosNewSection = $("input[name='rrze_tos[rrze_tos_protection_new_section]']:checked", "#tos-admin-form").val();
        console.log(rrzeTosNewSection);
        if ('1' === rrzeTosNewSection) {
            $("#wp-rrze_tos_protection_new_section_text-wrap").parents('tr').show();
        } else {
            $("#wp-rrze_tos_protection_new_section_text-wrap").parents('tr').hide();
        }
    }

    checkConformity();
    $("#tos-admin-form input").on('change', function () {
        checkConformity();
    });

    checkNewSection();
    $("#tos-admin-form input").on('change', function () {
        checkNewSection();
    });

    $("#tos-admin-form #update").click(function(event) { //event
        event.preventDefault();
        $(this).addClass("spinner-demo disabled");

        $.post(tos_ajax_obj.ajax_url, { //POST request
            _ajax_nonce: tos_ajax_obj.nonce, //nonce
            action: "tos_update_fields", //action
            title: this.value //data
        }, function(data) { //callback
            var obj = JSON.parse(data);
            $.each(obj.verantwortlich, function(i, val) {
                if (null != val)
                    $("input[name ='rrze_tos[rrze_tos_responsible_" + i + "]']").val(val);
            });
            $.each(obj.webmaster, function(i, val) {
                if (null != val)
                    $("input[name ='rrze_tos[rrze_tos_webmaster_" + i + "]']").val(val);
            });
        }).done(function(e) {
            var obj = JSON.parse(e);
            $('#ajax-response').removeClass("invisible visible notice-error").fadeIn().addClass("visible notice-success is-dismissible").delay(4000).fadeOut();
            $('#ajax-response p').empty().append(obj.success);
        }).fail(function(e) {
            $('#ajax-response').removeClass("invisible notice-error").fadeIn().addClass("visible notice-error").delay(4000).fadeOut();
            $('#ajax-response p').empty().append(e.responseText);
        }).always(function() {
            $(this).removeClass("spinner-demo disabled");
        }.bind(this));
    });

    $("#tabs").tabs({
        activate: function(event, ui) {
            window.location.hash = ui.newPanel.attr('id');
        }
    });

});
