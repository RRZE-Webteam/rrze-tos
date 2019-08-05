jQuery(document).ready(function($) {
    function checkConformity() {
        var rrzeTosConformity = $("input[name='rrze_tos[accessibility_conformity]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosConformity) {
            $("textarea[name='rrze_tos[accessibility_non_accessible_content]']").parents('tr').hide();
        } else {
            $("textarea[name='rrze_tos[accessibility_non_accessible_content]']").parents('tr').show();
        }
    }

    function checkAccessibilityHelperSection() {
        var rrzeTosAccessibilityHelper = $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_helper]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosAccessibilityHelper) {
            $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_faillist]']").parents('tr').hide();
        } else {
            $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_faillist]']").parents('tr').show();
        }
    }  
    

    function checkNewPrivacySection() {
        var rrzeTosNewSection = $("input[name='rrze_tos[privacy-privacy_section_extra]']:checked", "#tos-admin-form").val();
   //     console.log(rrzeTosNewSection);
        if ('1' === rrzeTosNewSection) {
            $("#wp-privacy_section_extra_text-wrap").parents('tr').show();
        } else {
            $("#wp-privacy_section_extra_text-wrap").parents('tr').hide();
        }
    }
    function checkNewImprintSection() {
        var rrzeTosImprintNewSection = $("input[name='rrze_tos[imprint-imprint_section_extra]']:checked", "#tos-admin-form").val();
     //   console.log(rrzeTosNewSection);
        if ('1' === rrzeTosImprintNewSection) {
            $("#wp-imprint_section_extra_text-wrap").parents('tr').show();
        } else {
            $("#wp-imprint_section_extra_text-wrap").parents('tr').hide();
        }
    }

    checkConformity();
    $("#tos-admin-form input").on('change', function() {
        checkConformity();
    });

    checkNewPrivacySection();
    checkNewImprintSection();
    checkAccessibilityHelperSection();
    $("#tos-admin-form input").on('change', function() {
        checkNewPrivacySection();
	checkNewImprintSection();
	 checkAccessibilityHelperSection();
    });

    if ($('[type="date"]').prop('type') != 'date') {
        $('[type="date"]').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }
});
