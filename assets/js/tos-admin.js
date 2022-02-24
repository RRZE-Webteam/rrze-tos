jQuery(document).ready(function($) {


    function checkAccessibilityHelperSection() {
        var rrzeTosAccessibilityHelper = $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_helper]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosAccessibilityHelper) {
            $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_faillist][]']").parents('tr').hide();
        } else {
            $("input[name='rrze_tos[accessibility-accessibility_non_accessible_content_faillist][]']").parents('tr').show();
        }
    }  
    

    function checkNewPrivacySection() {
        var rrzeTosNewSection = $("input[name='rrze_tos[privacy-privacy_section_extra]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosNewSection) {
            $("#wp-privacy_section_extra_text-wrap").parents('tr').show();
        } else {
            $("#wp-privacy_section_extra_text-wrap").parents('tr').hide();
        }
    }
    function checkPrivacyOwnDSBSection() {
        var rrzeDSBSection = $("input[name='rrze_tos[privacy-privacy_section_owndsb]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeDSBSection) {
            $("#wp-privacy_section_owndsb_text-wrap").parents('tr').show();
        } else {
            $("#wp-privacy_section_owndsb_text-wrap").parents('tr').hide();
        }
    }
    function checkNewImprintSection() {
        var rrzeTosImprintNewSection = $("input[name='rrze_tos[imprint-imprint_section_extra]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosImprintNewSection) {
            $("#wp-imprint_section_extra_text-wrap").parents('tr').show();
        } else {
            $("#wp-imprint_section_extra_text-wrap").parents('tr').hide();
        }
    }
    
    function checkNewImprintBildrechteSection() {
        var rrzeTosImprintNewSectionBildrechte = $("input[name='rrze_tos[imprint-imprint_section_bildrechte]']:checked", "#tos-admin-form").val();
        if ('1' === rrzeTosImprintNewSectionBildrechte) {
            $("#wp-imprint_section_bildrechte_text-wrap").parents('tr').show();
        } else {
            $("#wp-imprint_section_bildrechte_text-wrap").parents('tr').hide();
        }
    }
    


    checkNewPrivacySection();
    checkNewImprintSection();
    checkNewImprintBildrechteSection();
    checkAccessibilityHelperSection();
    checkPrivacyOwnDSBSection();
    $("#tos-admin-form input").on('change', function() {
        checkNewPrivacySection();
	checkNewImprintSection();
	checkNewImprintBildrechteSection();
	 checkAccessibilityHelperSection();
	  checkPrivacyOwnDSBSection();
    });

    if ($('[type="date"]').prop('type') != 'date') {
        $('[type="date"]').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }
});
