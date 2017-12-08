$(function () {
    var declarantLicenceForm = false;
    var driverLicenceForm = false;
    var judgeLicenceForm = false;
    var membershipLicence = false;
    var validDriverLicence = false;
    var validMedCert = false;
    var validInsurance = false;
    var fileUploadCount = 1;
    var readConditions = false;
    const POST_SIZE = 9000000;

    function validateDate(element) {
        var fieldType = element.data('type');
        var driverLicence = fieldType == 'driver_licence' ? true : false;
        var insurance = fieldType == 'insurance' ? true : false;
        var medCert = fieldType == 'medical_certificate' ? true : false;

        if (insurance) {
            if (new Date(element.val()) >= new Date(new Date().getFullYear(), 11, 31) ||
                $('#licence_info_lasfInsurance').is(':checked') ||
                useOldFile(element) ||
                isElementValueNull(element)) {
                validInsurance = true;
                removeErrors(element);
            } else {
                addErrors(element, 'insurance_expiration');
                validInsurance = false;
                showInsuranceError();
            }
        }

        if (driverLicence || medCert) {
            if (new Date(element.val()) >= new Date().setHours(0, 0, 0, 0) || useOldFile(element) || isElementValueNull(element)) {
                if (driverLicence) {
                    validDriverLicence = true;
                }
                if (medCert) {
                    validMedCert = true;
                }
                removeErrors(element);
            } else {
                addErrors(element, 'expired');
                if (driverLicence) {
                    validDriverLicence = false;
                }
                if (medCert) {
                    validMedCert = false;
                }
            }
        }
    }

    function useOldFile(element) {
        var parent = element.parents('.panel-body');
        return parent.find('input[id*=useOldFile]').is(':checked');
    }

    function isElementValueNull(element) {
        return element.val().length === 0 ? true : false;
    }

    function assignFormType() {
        if (typeElem = document.getElementById('licence_info_licenceType')) {
            licenceType = typeElem.value;
            if (licenceType.toLowerCase().indexOf("driver_licence") >= 0) {
                driverLicenceForm = true;
            } else if (licenceType.toLowerCase().indexOf("judge_licence") >= 0) {
                judgeLicenceForm = true;
            } else if (licenceType.toLowerCase().indexOf("declarant_licence") >= 0) {
                declarantLicenceForm = true;
            } else if (licenceType.toLowerCase().indexOf("membership") >= 0) {
                membershipLicence = true;
            }
        }
    }

    function removeErrors(elem) {
        $(elem).parent().children('.help-block').empty();
        $(elem).parent().removeClass('has-error');
    }

    function checkFiles(element) {
        removeErrors(element);
        var validFiles = true;
        var filesSize = 0;
        $("input[type='file']").each(function () {
            for (var i = 0; i < this.files.length; i++) {
                filesSize += this.files[i].size;
                if (this.files[i].size > POST_SIZE) {
                    addErrors(this, 'too_big_file');
                    validFiles = false;
                }
            }
        });

        if (filesSize > POST_SIZE - $("form").not("[type='file']").serialize().length) {
            addErrors(element, 'too_big_all_files');
            validFiles = false;
        }

        if (!validFiles) {
            return false;
        }
        return true;
    }

    function enableUploadInsurance() {
        var dataField = $('[data-type="insurance"]');
        var fileField = dataField.parent().parent().find('input[type="file"]');
        var useOldFile = $('#have-insurance').children().find('input[type=checkbox]');

        dataField.prop('required', true);
        dataField.siblings('label').addClass('required');
        dataField.prop('disabled', false);

        fileField.prop('required', true);
        fileField.siblings('label').addClass('required');
        fileField.prop('disabled', false);

        useOldFile.prop('disabled', false);
    }

    function disableUploadInsurance() {
        var dataField = $('[data-type="insurance"]');
        var fileField = dataField.parent().parent().find('input[type="file"]');
        var useOldFile = $('#have-insurance').children().find('input[type=checkbox]');

        $('#have-insurance').children().find('label[id*=file-error]').remove();
        $('#have-insurance').children().find('label[id*=validUntil-error]').remove();

        dataField.prop('required', false);
        dataField.prop('disabled', 'true');
        dataField.siblings('label').removeClass('required');

        fileField.prop('required', false);
        fileField.prop('disabled', 'true');
        fileField.siblings('label').removeClass('required');

        useOldFile.prop('disabled', true);
    }

    function displayInsuranceFields() {
        $("#read-insurance-agreement").click(function () {
            readConditions = true;
        });

        $("#insurance-conditions").click(function () {
            if (!readConditions) {
                $('.insurance-agreement-modal').modal('show');
                readConditions = true;
                return true;
            }
        });

        $("#licence_info_lasfInsurance").click(function () {
            if ($('#licence_info_lasfInsurance').is(':checked')) {
                $('#identity-code-field').removeClass('hidden');
                $('#licence_info_identityCode').prop('required', true);
                $('label[for=licence_info_identityCode]').addClass('required', 'required');
                disableUploadInsurance();
            } else {
                $('#identity-code-field').addClass('hidden');
                $('#licence_info_identityCode').val('');
                $('#licence_info_identityCode').prop('required', false);
                $('label[for=licence_info_identityCode]').removeClass('required', 'required');
                enableUploadInsurance();
            }
        });

        if ($('#licence_info_lasfInsurance').is(':checked')) {
            $('#identity-code-field').removeClass('hidden');
            $('#licence_info_identityCode').prop('required', true);
            $('label[for=licence_info_identityCode]').addClass('required', 'required');
            disableUploadInsurance();
        }
    }

    function showInsuranceError() {
        if (!$('#insurance-tab > li:first-child').hasClass('active')) {
            $('#insurance-tab > li, #insurance-tab-content > div').removeClass('active');
            $('#insurance-tab > li:first-child, #insurance-tab-content > div:first-child').addClass('active');
        }
    }

    assignFormType();

    $("button[type='submit']").click(function () {
        if (driverLicenceForm || judgeLicenceForm) {
            $("input[id*='validUntil']").each(function () {
                validateDate($(this));
            });
        }
        return checkFiles($(this));
    });

    if (declarantLicenceForm) {
        $('.form-collection').collectionForm();
    }

    if (membershipLicence) {
        $("#licence_info_personalCode").on('change', function () {
            if ($(this).val() == 0) {
                $('input[type="file"]').removeAttr('disabled');
                $(".document-list").show();
            } else {
                $('input[type="file"]').attr('disabled', 'disabled');
                $(".document-list").hide();
            }
        });
    }

    if (driverLicenceForm) {
        $(document).on('change', '#licence_info_licence', function (e) {
            var value = $(this).val();

            if ($.isNumeric(value)) {
                $.getJSON('/declarant/' + value + '/info', {}, function (res) {
                    fillField(res);
                })
            } else {
                fillField({lasfName: "", lasfAddress: "", personalCode: ""});
            }

            function fillField(data) {
                $('#licence_info_lasfName').val(data.lasfName);
                $('#licence_info_lasfAddress').val(data.lasfAddress);
                $('#licence_info_personalCode').val(data.personalCode);
            }
        });
    }

    if (!declarantLicenceForm) {
        $("input[id*='validUntil']").change(function () {
            validateDate($(this));
        });
    }

    $(document).on('change', 'form', function () {
        setRequireFile();
    });

    $(document).ready(function () {
        setRequireFile();
    });

    function setRequireFile() {
        $("input[type='file']").each(function () {
            var elementParent = $(this).parent();
            var elementGrandParent = elementParent.parent();
            if (elementGrandParent.children('label').hasClass('required')) {
                $(this).attr("required", "true");
            }
        });
    }

    $(document).on('click', 'input[id*=licence_info_documents]', function () {
        $(this).parents().eq(1).find('label[id*=file]').remove();
    });

    $("form").validate({
        rules: {
            field: {
                required: true
            }
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2")) {
                var sibling = element.next();
                error.insertBefore(sibling);
            } else if (element.attr('type') === 'text' || element.attr('type') === 'email' || element.hasClass('licence')) {
                error.insertBefore(element);
            } else if (element.attr('type') === 'file') {
                error.insertBefore(element.parent());
            } else if (element.attr('type') === 'checkbox') {
                error.insertAfter(element.parent());
            } else if (element.attr('type') === 'radio' && element.parents().eq(2).hasClass('deliverTo')) {
                error.insertBefore(element.parents().eq(4));
            } else if (element.attr('type') === 'radio' && element.parents().eq(2).hasClass('urgency')) {
                error.insertBefore(element.parents().eq(2));
            } else {
                error.insertAfter(element);
            }
        }
    });

    displayDeliverToAdress($("#licence_info_deliverTo"), $('#licence_info_deliverTo_1'));

    displayInsuranceFields();

    $("input[id*='useOldFile']").change(function () {
        if ($(this).is(':checked')) {
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('required', false);
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('disabled', true);
        } else {
            $(this).parents('.panel-body').find('input[aria-required=true]:not([id*=useOldFile])').prop('required', true);
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('disabled', false);
        }
    });

    $('input[id*=useOldFile]').each(function () {
        if ($(this).is(':checked')) {
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('required', false);
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('disabled', true);
        } else {
            $(this).parents('.panel-body').find('input[aria-required=true]:not([id*=useOldFile])').prop('required', true);
            $(this).parents('.panel-body').find('input:not([id*=useOldFile])').prop('disabled', false);
        }
    });

    $("input[type='file']").each(function () {
        var id = $(this).attr('id');
        $('#' + id).removeAttr('multiple', 'multiple');
        $('#' + id).MultiFile({
            list: ('#' + id),
            afterFileSelect: function (element, value, master_element) {
                $(element).css({
                    'visibility': 'hidden'
                });
            }
        });
    });
});
