;(function ($, window) {
    'use strict';

    var $cmbRepeatableGroup, formIdSelect, unsavedChanges, updateShortcode,
        ajaxUpdate, formID, imageField, shortcodeSelector, captionField, data, selected;

    $cmbRepeatableGroup = $('.cmb-repeatable-group');
    formIdSelect = $cmbRepeatableGroup.find('.form-id-select');
    unsavedChanges = false;

    updateShortcode = function () {
        formID = $(this).find('select.cmb2_select').val();
        shortcodeSelector = $(this).siblings('.slideshow-shortcode').find('input.regular-text');
        $(shortcodeSelector).val('[gf-slideshow gform_id="' + formID + '"]');
    };

    ajaxUpdate = function () {

        imageField = $(this).siblings('.image-id-select').find('select.cmb2_select');
        captionField = $(this).siblings('.caption-id-select').find('select.cmb2_select');

        $(imageField).empty();
        $(captionField).empty();

        $(imageField).parent().addClass('ajaxing');
        $(captionField).parent().addClass('ajaxing');

        selected = $(this).find('select.cmb2_select').val();
        data = {
            'action': 'gf_slideshow_get_gform_fields',
            'formID': selected
        };

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',
            success: function (data) {
                $(imageField).parent().removeClass('ajaxing');
                $(captionField).parent().removeClass('ajaxing');

                $.each(data, function (key, value) {
                    $(imageField).append($("<option/>", {
                        value: key,
                        text: value
                    }));

                    $(captionField).append($("<option/>", {
                        value: key,
                        text: value
                    }));
                });
            }
        })

    };

    $.each(formIdSelect, updateShortcode);

    $cmbRepeatableGroup.on('change', '.form-id-select', ajaxUpdate);

    $cmbRepeatableGroup.on('change', '.form-id-select', updateShortcode);

    $cmbRepeatableGroup.on('change', function () {
        unsavedChanges = true;
    });

    $('input[name="submit-cmb"]').on('click', function () {
        unsavedChanges = false;
    });

    window.onbeforeunload = function () {
        if (unsavedChanges) {
            return 'Did you save your changes?';
        }
    };

})(jQuery, window);
