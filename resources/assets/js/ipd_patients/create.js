document.addEventListener("turbo:load", loadIpdPatientCreate);

function loadIpdPatientCreate() {
    if (!$("#ipdAdmissionDate").length && !$("#editIpdAdmissionDate").length) {
        return;
    }

    $(
        "#ipdPatientId, #ipdDoctorId, #ipdBedTypeId,#editIpdPatientId, #editIpdDoctorId, #editIpdBedTypeId"
    ).select2({
        width: "100%",
    });

    $("#ipdCaseId, #editIpdCaseId ").select2({
        width: "100%",
        placeholder:
            Lang.get("messages.common.choose") +
            " " +
            Lang.get("messages.ipd_patient.case_id"),
    });

    $("#ipdBedId, #editIpdBedId").select2({
        width: "100%",
        placeholder:
            Lang.get("messages.common.choose") +
            " " +
            Lang.get("messages.bed_assign.bed"),
    });

    let admissionFlatPicker = $(
        "#ipdAdmissionDate, #editIpdAdmissionDate"
    ).flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        locale: $(".userCurrentLanguage").val(),
    });

    if ($(".isEdit").val()) {
        $(".ipdPatientId, .ipdBedTypeId").trigger("change");
        admissionFlatPicker.set("minDate", $(".ipdAdmissionDate").val());
    } else {
        admissionFlatPicker.setDate(new Date());
        admissionFlatPicker.set("minDate", new Date());
    }
}

listenClick(".ipd-diagnosis-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href="#cases-tab"]').tab("show");
    $(this).removeClass("active");
    $("#cases-tab").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $("#cases-tab").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenClick(".ipd-charges-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href=".ipdCharges"]').tab("show");
    $(this).removeClass("active");
    $(".ipdCharges").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $(".ipdCharges").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenClick(".ipd-consultant-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href=".ipdConsultantInstruction"]').tab("show");
    $(this).removeClass("active");
    $(".ipdConsultantInstruction").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $(".ipdConsultantInstruction").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenClick(".ipd-prescription-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href=".ipdPrescriptions"]').tab("show");
    $(this).removeClass("active");
    $(".ipdPrescriptions").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $(".ipdPrescriptions").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenClick(".ipd-payment-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href=".ipdPayment"]').tab("show");
    $(this).removeClass("active");
    $(".ipdPayment").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $(".ipdPayment").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenClick(".ipd-operation-btn", function (event) {
    event.preventDefault();
    $('#myTab a[href=".ipdOperation"]').tab("show");
    $(this).removeClass("active");
    $(".ipdOperation").attr("aria-selected", true);
    $("#ipdOverview").attr("aria-selected", false);
    $(".ipdOperation").addClass("active");
    $("#ipdOverview").removeClass("active");
});

listenKeyup(".ipdDepartmentFloatNumber", function () {
    if ($(this).val().indexOf(".") != -1) {
        if ($(this).val().split(".")[1].length > 2) {
            if (isNaN(parseFloat(this.value))) return;
            this.value = parseFloat(this.value).toFixed(2);
        }
    }
    return this;
});

listenSubmit(
    "#createIpdPatientForm, #editIpdPatientDepartmentForm",
    function () {
        $("#ipdSave, #btnIpdPatientEdit").attr("disabled", true);
    }
);

listenChange(".ipdPatientId", function () {
    if ($(this).val() !== "") {
        $.ajax({
            url: $(".patientCasesUrl").val(),
            type: "get",
            dataType: "json",
            data: { id: $(this).val() },
            success: function (data) {
                if (data.data.length !== 0) {
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").empty();
                    $(
                        "#ipdDepartmentCaseId,#editIpdDepartmentCaseId"
                    ).removeAttr("disabled");
                    $.each(data.data, function (i, v) {
                        $(
                            "#ipdDepartmentCaseId,#editIpdDepartmentCaseId"
                        ).append(
                            $("<option></option>").attr("value", i).text(v)
                        );
                    });
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId")
                        .val($("#editIpdPatientCaseId").val())
                        .trigger("change.select2");
                } else {
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").prop(
                        "disabled",
                        true
                    );
                }
            },
        });
    }
    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").empty();
    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").prop("disabled", true);

    $("#ipdDepartmentCaseId, #editIpdDepartmentCaseId ").select2({
        width: "100%",
        placeholder:
            Lang.get("messages.common.choose") +
            " " +
            Lang.get("messages.ipd_patient.case_id"),
    });
});

listenChange(".ipdBedTypeId", function () {
    let bedId = null;
    let bedTypeId = null;
    if (
        typeof $("#editIpdPatientBedId").val() != "undefined" &&
        typeof $("#editIpdPatientBedTypeId").val() != "undefined"
    ) {
        bedId = $("#editIpdPatientBedId").val();
        bedTypeId = $("#editIpdPatientBedTypeId").val();
    }

    if ($(this).val() !== "") {
        let bedType = $(this).val();
        $.ajax({
            url: $(".patientBedsUrl").val(),
            type: "get",
            dataType: "json",
            data: {
                id: $(this).val(),
                isEdit: $(".isEdit").val(),
                bedId: bedId,
                ipdPatientBedTypeId: bedTypeId,
            },
            success: function (data) {
                if (data.data !== null) {
                    if (data.data.length !== 0) {
                        $("#ipdBedId,#editIpdBedId").empty();
                        $("#ipdBedId,#editIpdBedId").removeAttr("disabled");
                        $.each(data.data, function (i, v) {
                            $("#ipdBedId,#editIpdBedId").append(
                                $("<option></option>").attr("value", i).text(v)
                            );
                        });
                        if (
                            typeof $("#editIpdPatientBedId").val() !=
                                "undefined" &&
                            typeof $("#editIpdPatientBedTypeId").val() !=
                                "undefined"
                        ) {
                            if (
                                bedType === $("#editIpdPatientBedTypeId").val()
                            ) {
                                $("#ipdBedId,#editIpdBedId")
                                    .val($("#editIpdPatientBedId").val())
                                    .trigger("change.select2");
                            }
                        }
                        $("#ipdBedId,#editIpdBedId").prop("required", true);
                    }
                } else {
                    $("#ipdBedId,#editIpdBedId").prop("disabled", true);
                }
            },
        });
    }
    $("#ipdBedId,#editIpdBedId").empty();
    $("#ipdBedId,#editIpdBedId").prop("disabled", true);

    $("#ipdBedId, #editIpdBedId").select2({
        width: "100%",
        placeholder:
            Lang.get("messages.common.choose") +
            " " +
            Lang.get("messages.bed_assign.bed"),
    });
});
