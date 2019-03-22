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
        var rrzeTosNewSection = $("input[name='rrze_tos[rrze_tos_privacy_new_section]']:checked", "#tos-admin-form").val();
        console.log(rrzeTosNewSection);
        if ('1' === rrzeTosNewSection) {
            $("#wp-rrze_tos_privacy_new_section_text-wrap").parents('tr').show();
        } else {
            $("#wp-rrze_tos_privacy_new_section_text-wrap").parents('tr').hide();
        }
    }

    checkConformity();
    $("#tos-admin-form input").on('change', function() {
        checkConformity();
    });

    checkNewSection();
    $("#tos-admin-form input").on('change', function() {
        checkNewSection();
    });

});