const {__, _x, _n, sprintf} = wp.i18n;

jQuery(document).ready(function ($) {

    tippy('[data-tippy-content]');

    $('.color-field').wpColorPicker();

    $("span.option-dashicon").on("click", function () {
        if ($(this).parent().parent().next().hasClass("hidden")) {
            $(this).parent().parent().next().removeClass("hidden");
        } else {
            $(this).parent().parent().next().addClass("hidden");
        }
    });

    $(".input_type").on("change", changeInputEvent);
    $(".input_type_group").on("change", changeInputEventGroup);

    $(".nestedGroup .delInputGroup").on("click", deleteInputGroup);

    $(".infocob_crm_forms_copy").on("click", function () {
        $(this).select();
        document.execCommand('copy');
    });

    $('.select-multiple').multipleSelect({
        filter: true,
        displayDelimiter: ' | '
    });

    initSelectsAcceptFile();
    initMultipleFields();

    $(".addEmailTo").on("click", function () {
        let newRowInputs = $(".form-table.destinataires:not(.additional-email) tbody tr:last-child").clone();

        $(newRowInputs).find("input").each(function (index, element) {
            let nameRegex = /emails_to\[([0-9]+)](.+)/;
            let currentName = $(element).prop("name");

            if (currentName) {
                let nbName = currentName.match(nameRegex);
                let newNb = parseInt(nbName[1]) + 1;
                let newName = currentName.replace(/emails_to\[([0-9]+)].+/, "emails_to[" + newNb + "]" + nbName[2]);

                $(element).prop("name", newName);
                $(element).prop("value", "");
            }
        });

        $(".form-table.destinataires:not(.additional-email)").append(newRowInputs);

        $(".delEmailTo").off("click", delEmailTo);
        $(".delEmailTo").on("click", delEmailTo);
    });

    $(".addAdditionalEmailTo").on("click", function () {
        let newRowInputs = $(this).parents(".form-table.destinataires.additional-email").find("tbody tr:last-child").clone();

        $(newRowInputs).find("input").each(function (index, element) {
            let nameRegex = /additional_email\[[0-9]+]\[to]\[([0-9]+)](.+)/;
            let currentName = $(element).prop("name");

            if (currentName) {
                let nbName = currentName.match(nameRegex);
                let newNb = parseInt(nbName[1]) + 1;
                let newName = currentName.replace(/additional_email\[([0-9])+]\[to]\[([0-9]+)].+/, "additional_email[$1][to][" + newNb + "]" + nbName[2]);

                $(element).prop("name", newName);
                $(element).prop("value", "");
            }
        });

        $(this).parents(".form-table.destinataires.additional-email").append(newRowInputs);

        $(".delAdditionalEmailTo").off("click", delAdditionalEmailTo);
        $(".delAdditionalEmailTo").on("click", delAdditionalEmailTo);
    });

    $(".addOption").on("click", addOptionEvent);
    $(".addOptionGroup").on("click", addOptionEventGroup);

    $(".delInput").on("click", delInput);

    $(".delEmailTo").on("click", delEmailTo);
    $(".delAdditionalEmailTo").on("click", delAdditionalEmailTo);

    $(".delOption").on("click", delOption);

    function delInput() {
        if ($(".form-table.inputs:first tbody:first > tr:not(.accordion-options)").length > 1) {
            if ($(this).parent().parent().next().is("tr.accordion-options")) {
                $(this).next().remove();
                $(this).parent().parent().next().remove();
            }

            $(this).parent().parent().remove();
        }
    }

    function delEmailTo() {
        if ($(".form-table.destinataires:not(.additional-email) tbody tr").length > 1) {
            $(this).parent().parent().remove();
        }
    }

    function delAdditionalEmailTo() {
        if ($(this).parents(".form-table.destinataires.additional-email").find("tbody tr").length > 1) {
            $(this).parent().parent().remove();
        }
    }

    function delOption() {
        if ($(this).parent().parent().parent().parent().parent().find("tbody tr").length > 3) {
            $(this).parent().parent().remove();
        }
    }

    function initSelectsAcceptFile() {
        $(".input_type").each(function (index, element) {
            if ($(element).find("option:selected").val().toLowerCase() === "file") {
                $(element).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('enable');
            } else {
                $(element).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
                $(element).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            }
        });
    }

    function initMultipleFields() {
        $(".input_type").each(function (index, element) {
            if ($(element).find("option:selected").val().toLowerCase() === "select") {
                $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
                $(this).parent().parent().find("div.input_search input").prop("disabled", false);
            } else if ($(element).find("option:selected").val().toLowerCase() === "file") {
                $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
                $(this).parent().parent().find("div.input_search input").prop("disabled", true);
            } else {
                $(this).parent().parent().find("div.input_multiple input").prop("disabled", true);
                $(this).parent().parent().find("div.input_search input").prop("disabled", true);
            }
        });
    }

    function addOptionEvent() {
        let newRowInputs = $(this).parent().parent().parent().parent().find("tbody tr:last").clone();

        $(newRowInputs).find("input, select").each(function (index, element) {
            let nameRegex = new RegExp('input\\[([0-9]+)]\\[options]\\[([0-9]+)](.+)', "i");
            let currentName = $(element).prop("name");

            if (currentName) {
                let nbName = currentName.match(nameRegex);
                var newId = parseInt(nbName[2]) + 1;
                let newName = currentName.replace(/input\[([0-9]+)]\[options]\[([0-9]+)].+/, "input[" + nbName[1] + "][options][" + newId + "]" + nbName[3]);

                $(element).prop("name", newName);
                if ($(element).prop("type").toLowerCase() === "checkbox") {
                    $(element).prop("checked", false);
                } else {
                    $(element).prop("value", "");
                }
            }
        });

        $(this).parent().parent().parent().parent().parent().find("tbody").append(newRowInputs);

        $(".delOption").off("click", delOption);
        $(".delOption").on("click", delOption);

        $(".row-up-option, .row-down-option").off("click", rowUpDownOptionsEvent);
        $(".row-up-option, .row-down-option").on("click", rowUpDownOptionsEvent);

        tippy('[data-tippy-content]');
    }

    function changeInputEvent() {
        if ($(this).find("option:selected").val().toLowerCase() === "file") {
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('enable');

            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            $(this).parent().parent().find("div.input_multiple input").prop("disabled", true);
            $(this).parent().parent().find("div.input_search input").prop("disabled", true);

        } else if ($(this).find("option:selected").val().toLowerCase() === "select") {
            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td></td>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Placeholder :", "infocob-crm-forms") + "</td>" +
                "<td colspan='3'>" +
                "<input data-tippy-content='" + __("Placeholder", "infocob-crm-forms") + "' name='input[" + id + "][options][placeholder]' type='text' value=''>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Enable recipients", "infocob-crm-forms") + "' name='input[" + id + "][options][recipients_enabled]' type='checkbox' value='1'>" +
                "</td>" +
                "</tr>" +
                "<tr>" +
                "<th></th>" +
                "<th>" + __("Label", "infocob-crm-forms") + "</th>" +
                "<th>" + __("Value", "infocob-crm-forms") + "</th>" +
                "<th class='sm'>" + __("Default", "infocob-crm-forms") + "</th>" +
                "<th class='sm'></th>" +
                "</tr>" +
                "<tr class='opt-row' data-id='" + id + "'>" +
                "<td class='up-down-dashicons'>" +
                "<div>" +
                "<span class='row-up-option dashicons dashicons-arrow-up-alt2'></span>" +
                "<span class='row-down-option dashicons dashicons-arrow-down-alt2'></span>" +
                "</div>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Label", "infocob-crm-forms") + "' name='input[" + id + "][options][0][libelle]' type='text'>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Value", "infocob-crm-forms") + "' name='input[" + id + "][options][0][valeur]' type='text'>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Default", "infocob-crm-forms") + "' name='input[" + id + "][options][0][selected]' type='checkbox' value='1'>" +
                "</td>" +
                "<td>" +
                "<button class='delOption' type='button'>" + __("Delete", "infocob-crm-forms") + "</button>" +
                "</td>" +
                "</tr>" +
                "</tbody>" +
                "<tfoot>" +
                "<tr>" +
                "<td colspan='4'>" +
                "<button class='addOption' type='button'>" + __("Add", "infocob-crm-forms") + "</button>" +
                "</td>" +
                "</tr>" +
                "</tfoot>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            $(".addOption").off("click", addOptionEventGroup);
            $(".addOption").off("click", addOptionEvent);
            $(".addOption").on("click", addOptionEvent);

            $(".row-up-option, .row-down-option").off("click", rowUpDownOptionsEvent);
            $(".row-up-option, .row-down-option").on("click", rowUpDownOptionsEvent);

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');
        } else if ($(this).find("option:selected").val().toLowerCase() === "number") {
            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td></td>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Min :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Min", "infocob-crm-forms") + "' name='input[" + id + "][numbers][min]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "<tr>" +
                "<td>" + __("Max :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Max", "infocob-crm-forms") + "' name='input[" + id + "][numbers][min]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "<tr>" +
                "<td>" + __("Step :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Step", "infocob-crm-forms") + "' name='input[" + id + "][numbers][step]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');
        } else if ($(this).find("option:selected").val().toLowerCase() === "checkbox") {
            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Invert value sent :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Invert value sent", "infocob-crm-forms") + "' name='input[" + id + "][checkboxes][invert]' type='checkbox'></td>" +
                "</tr>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');
        } else {
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');

            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            $(this).parent().parent().find("div.input_multiple input").prop("disabled", true);
            $(this).parent().parent().find("div.input_search input").prop("disabled", true);
        }
    }

    $(".row-up-option, .row-down-option").on("click", rowUpDownOptionsEvent);

    function rowUpDownOptionsEvent() {
        let currentRow = $(this).parent().parent().parent();

        let targetRow = $(currentRow).nextAll("tr.opt-row").first();
        if ($(this).hasClass("row-up-option")) {
            targetRow = $(currentRow).prevAll("tr.opt-row").first();
        }

        if ($(this).hasClass("row-up-option")) {
            $(currentRow).insertBefore(targetRow);
        } else {
            $(currentRow).insertAfter(targetRow);
        }

        var allRows = $(currentRow).parent().find("tr.opt-row");

        $(allRows).each(function (index, elem) {
            var currentIndex = $(elem).attr("data-id");

            var inputs = $(elem).find("input, select");
            $(inputs).each(function (x, element) {
                let nameRegex = new RegExp('(input|select)\\[([0-9]+)]\\[options]\\[([0-9]+)](.+)', "i");
                let currentName = $(element).prop("name");

                if (currentName) {
                    let nbName = currentName.match(nameRegex);
                    let newName = currentName.replace(/(input|select)\[([0-9]+)]\[options]\[([0-9]+)].+/, nbName[1] + "[" + currentIndex + "][options][" + index + "]" + nbName[4]);

                    $(element).prop("name", newName);
                }
            });
        });
    }

    $("#mode_avance_enable").on("change", function () {
        if ($(this).is(':checked')) {
            $("tr.mode-avance-console").removeClass("hidden");
        } else {
            $("tr.mode-avance-console").addClass("hidden");
        }
    })

    // ############################

    $("#inputsList").each((index, element) => {
        new Sortable(element, {
            group: {
                name: 'nested-items',
                pull: true,
                put: true,
            },
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            draggable: ".draggable",
            handle: '.handle',
            onUpdate: function (evt) {
                sortableOnUpdate(evt);
            },
            onMove: function (evt) {
                let draggedElem = evt.dragged;
                let toElem = evt.to;
                if ($(draggedElem).hasClass("nestedGroup") && $(toElem).hasClass("nestedGroup")) {
                    return false;
                }
                return evt.related.className.indexOf('disabled') === -1;
            },
        });
    });

    $(".nestedGroup").each((index, element) => {
        new Sortable(element, {
            group: {
                name: 'nested-group',
                pull: true,
                put: true,
            },
            draggable: ".draggable",
            handle: '.handle',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onMove: function (evt) {
                console.debug("nestedGroup");
                return evt.related.className.indexOf('disabled') === -1;
            },
            onAdd: onAddGroup,
            onRemove: onDelGroup,
            onUpdate: sortableOnUpdateGroup,
        });
    });

    function sortableOnUpdate(evt) {
        let inputsList = $("#inputsList > .list-group-item.nested-1");
        for (let index = 0; index < inputsList.length; index++) {
            var elem = $(inputsList)[index];

            $(elem).attr("data-id", index);

            var inputs = $(elem).find("input, select");

            $(inputs).each(function (x, element) {
                let nameRegex = /input\[([0-9]+)](.+)/;
                let currentName = $(element).prop("name");

                if (currentName) {
                    let nbName = currentName.match(nameRegex);

                    let newName = currentName.replace(/input\[([0-9]+)].+/, "input[" + index + "]" + nbName[2]);

                    $(element).prop("name", newName);

                    if ($(element).hasClass("select-multiple")) {
                        $(element).removeClass("ms-offscreen");
                        $(element).removeAttr("style");
                        $(element).parent().find("div").remove();
                        $(element).multipleSelect({
                            filter: true,
                            displayDelimiter: ' | '
                        });
                    }
                }
            });

            var rowOptions = $(elem).find("> div.options tr.accordion-options");
            if (rowOptions.length) {
                $(rowOptions).find("table.form-table:first tbody:first > tr").attr("data-id", index);
                var inputsOptions = $(rowOptions).find("input");
                if (inputsOptions.length) {
                    $(inputsOptions).each(function (x, element) {
                        let nameRegexOptions = new RegExp('input\\[([0-9]+)]\\[options]\\[([0-9]+)](.+)', "i");
                        let nameRegex = new RegExp('input\\[([0-9]+)]\\[options]\\[(\\w+)]', "i");
                        let currentName = $(element).prop("name");

                        if (currentName) {
                            let nbName = currentName.match(nameRegexOptions);
                            if (nbName) {
                                let newId = parseInt(nbName[2]) + 1;
                                let newName = currentName.replace(/input\[([0-9]+)]\[options]\[([0-9]+)].+/, "input[" + index + "][options][" + newId + "]" + nbName[3]);

                                $(element).prop("name", newName);
                            }

                            nbName = currentName.match(nameRegex);
                            if (nbName) {
                                let name = nbName[2];
                                let newName = currentName.replace(/input\[([0-9]+)]\[options]\[(\w+)]/, "input[" + index + "][options][" + name + "]");

                                $(element).prop("name", newName);
                            }
                        }
                    });
                }

            }

        }
    }

    $(".addInputRow").on("click", addInputRow);
    $(".addInputGroup").on("click", addInputGroup);
    $(".delInputRow").on("click", delInputRow);

    function delInputRow() {
        $(this).parent().parent().parent().remove();
    }

    function addInputRow() {
        let column_base = $("#columns_base").val();
        let column_base_options = "";
        for (let i = 1; i <= column_base; i++) {
            column_base_options += "<option value='" + i + "'>" + i + "</option>"
        }

        let newRowInputs = "<div data-id='0' class='list-group-item nested-1 draggable'>" +
            "                <div class='rowInputField'>" +
            "                    <div class='up-down-dashicons'>" +
            "                        <span class='dashicons dashicons-move handle'></span>" +
            "                    </div>" +
            "                    <div class='input-flex'>" +
            "                        <select data-tippy-content='" + __("Type", "infocob-crm-forms") + "' class='input_type' name='input[0][type]' required>" +
            "                            <option value='text'>Text</option>" +
            "                            <option value='email'>Email</option>" +
            "                            <option value='number'>Number</option>" +
            "                            <option value='tel'>Tel</option>" +
            "                            <option value='password'>Password</option>" +
            "                            <option value='textarea'>Textarea</option>" +
            "                            <option value='checkbox'>Checkbox</option>" +
            "                            <option value='file'>File</option>" +
            "                            <option value='select'>Select</option>" +
            "                            <option value='date'>Date</option>" +
            "                            <option value='hidden'>Hidden</option>" +
            "                        </select>" +
            "                    </div>" +
            "                    <div>" +
            "                        <select data-tippy-content='" + __("Column(s)", "infocob-crm-forms") + "' name='input[0][col]' required>" +
            column_base_options +
            "                        </select>" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Name", "infocob-crm-forms") + "' name='input[0][nom]' type='text' value='' placeholder='" + __("Name", "infocob-crm-forms") + "' required />" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Label", "infocob-crm-forms") + "' name='input[0][libelle]' type='text' value='' placeholder='" + __("Label", "infocob-crm-forms") + "' />" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Value", "infocob-crm-forms") + "' name='input[0][valeur]' type='text' value='' placeholder='" + __("Value", "infocob-crm-forms") + "' />" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Post default", "infocob-crm-forms") + "' name='input[0][defaut_post]' type='text' value='' placeholder='" + __("Post default", "infocob-crm-forms") + "' />" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Display Label", "infocob-crm-forms") + "' name='input[0][display_libelle]' type='checkbox' value='1' />" +
            "                    </div>" +
            "                    <div>" +
            "                        <input data-tippy-content='" + __("Require", "infocob-crm-forms") + "' name='input[0][required]' type='checkbox' value='1' />" +
            "                    </div>" +
            "                    <div class='input_search'>" +
            "                        <input data-tippy-content='" + __("Display search bar in select input", "infocob-crm-forms") + "' name='input[0][search_select]' type='checkbox' value='1' />" +
            "                    </div>" +
            "                    <div class='input_multiple'>" +
            "                        <input data-tippy-content='" + __("Multiple", "infocob-crm-forms") + "' name='input[0][multiple]' type='checkbox' value='1' />" +
            "                    </div>" +
            "                    <div data-tippy-content='" + __("Files", "infocob-crm-forms") + "' class='accept-file'>" +
            "                        <select class='select-multiple' name='input[0][accept][]' multiple>" +
            "                            <option value='application/pdf'>PDF</option>" +
            "                            <option value='image/jpeg'>JPG</option>" +
            "                            <option value='image/png'>PNG</option>" +
            "                            <option value='application/zip'>ZIP</option>" +
            "                            <option value='text/plain'>TXT</option>" +
            "                            <option value='application/msword'>DOC</option>" +
            "                            <option value='application/vnd.openxmlformats-officedocument.wordprocessingml.document'>DOCX</option>" +
            "                        </select>" +
            "                    </div>" +
            "                    <div>" +
            "                        <button class='delInputRow' type='button'><span class='dashicons dashicons-trash'></span></button>" +
            "                    </div>" +
            "                </div>" +
            "                <div class='options hidden'></div>" +
            "            </div>";

        $(newRowInputs).find("input:not([type='checkbox']):not([type='number'])").each(function (index, element) {
            $(element).val("");
        });

        $(newRowInputs).find("input[type='number']").each(function (index, element) {
            $(element).val("1");
        });

        $(newRowInputs).find("input[type='checkbox']").each(function (index, element) {
            $(element).prop("checked", false);
        });

        $(newRowInputs).find("select option").each(function (index, element) {
            $(element).prop("selected", false);
        });

        $(newRowInputs).find("input, select").each(function (index, element) {
            let currentName = $(element).prop("name");
            if (currentName) {
                if ($(element).hasClass("select-multiple")) {
                    $(element).removeClass("ms-offscreen");
                    $(element).removeAttr("style");
                    $(element).parent().find("div").remove();
                    $(element).multipleSelect({
                        filter: true,
                        displayDelimiter: ' | '
                    });
                }
            }
        });

        $("#inputsList").append(newRowInputs);

        $(".input_type").off("change", changeInputEvent);
        $(".input_type").on("change", changeInputEvent);

        sortableOnUpdate();

        $(".delInputRow").on("click", delInputRow);

        initSelectsAcceptFile();
        initMultipleFields();

        tippy('[data-tippy-content]');
    }

    function addInputGroup() {
        let lastRow = $("#inputsList .list-group-item.nested-1:not(.nestedGroup):last");
        let currentId = -1;
        if (lastRow.length) {
            currentId = $(lastRow).attr("data-id");
        }
        let newId = parseInt(currentId) + 1;

        let column_base = $("#columns_base").val();
        let column_base_options = "";
        for (let i = 1; i <= column_base; i++) {
            column_base_options += "<option value='" + i + "'>" + i + "</option>"
        }

        let nestedGroup = "<div data-id='" + newId + "' class='list-group-item nested-1 nestedGroup draggable'>" +
            "<div class='rowInputGroup'>" +
            "<input type='hidden' name='input[" + newId + "][type]' value='groupe'>" +
            "<div class='up-down-dashicons'>" +
            "<span class='dashicons dashicons-move handle'></span>" +
            "</div>" +
            "<div class='inputsGroup'>" +
            "<div>" +
            "<label for='libelle'>" + __("Label", "infocob-crm-forms") + "</label>" +
            "<input data-tippy-content='" + __("Label", "infocob-crm-forms") + "' type='text' name='input[" + newId + "][libelle]'>" +
            "</div>" +
            "<div>" +
            "<select data-tippy-content='" + __("Column(s)", "infocob-crm-forms") + "' name='input[" + newId + "][col]' required>" +
            column_base_options +
            "</select>" +
            "</div>" +
            "<div class='input-flex'>" +
            "<label for='libelle'>" + __("Display label", "infocob-crm-forms") + "</label>" +
            "<input data-tippy-content='" + __("Display label", "infocob-crm-forms") + "' type='checkbox' name='input[" + newId + "][display_libelle]' value='1'>" +
            "</div>" +
            "<div>" +
            "<button class='delInputGroup' type='button'>" + __("Delete", "infocob-crm-forms") + "</button>" +
            "</div>" +
            "</div>" +
            "</div>" +
            "<div class='draggableZoneSeparator'></div>" +
            "</div>";
        $("#inputsList").append(nestedGroup);

        $(".nestedGroup").each((index, element) => {
            new Sortable(element, {
                group: {
                    name: 'nested-group',
                    pull: true,
                    put: true,
                },
                draggable: ".draggable",
                handle: '.handle',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onMove: function (evt) {
                    console.debug("nestedGroup");
                    return evt.related.className.indexOf('disabled') === -1;
                },
                onAdd: onAddGroup,
                onRemove: onDelGroup,
                onUpdate: sortableOnUpdateGroup,
            });
        });

        $(".nestedGroup").find(".delInputGroup").off("click");
        $(".nestedGroup").find(".delInputGroup").on("click", deleteInputGroup);

        tippy('[data-tippy-content]');
    }

    function onDelGroup(evt) {
        let item = (evt.item) ? evt.item : false;
        if (item) {
            var inputs = $(item).find("input, select");

            let input_type_elem = $(inputs).first();
            $(input_type_elem).off("change", changeInputEventGroup);
            $(input_type_elem).addClass("input_type");
            $(input_type_elem).removeClass("input_type_group");
            $(input_type_elem).on("change", changeInputEvent);

            $(inputs).each(function (x, element) {
                let nameRegex = /(input\[[0-9]+])\[champs]\[[0-9]+](.+)/;
                let currentName = $(element).prop("name");

                if (currentName) {
                    let nbName = currentName.match(nameRegex);
                    let newName = currentName.replace(/input\[[0-9]+].+/, nbName[1] + nbName[2]);
                    $(element).prop("name", newName);

                    if ($(element).find("option:selected").val() === "select") {
                        let add_option_elem = $(element).parent().parent().parent().find(".addOption, .addOptionGroup");
                        $(add_option_elem).off("click", addOptionEventGroup);
                        $(add_option_elem).on("click", addOptionEvent);
                    }
                }
            });

            sortableOnUpdate(evt);
        }
    }

    function onAddGroup(evt) {
        console.debug("OnAddGroup");
        let item = (evt.item) ? evt.item : false;
        if (item) {
            var inputs = $(item).find("input, select");

            let input_type_elem = $(inputs).first();
            $(input_type_elem).off("change", changeInputEvent);
            $(input_type_elem).addClass("input_type_group");
            $(input_type_elem).removeClass("input_type");
            $(input_type_elem).on("change", changeInputEventGroup);

            $(inputs).each(function (x, element) {
                let nameRegex = /(input\[[0-9]+])(.+)/;
                let currentName = $(element).prop("name");

                if (currentName) {
                    let nbName = currentName.match(nameRegex);
                    let newName = currentName.replace(/input\[[0-9]+].+/, nbName[1] + "[champs][0]" + nbName[2]);
                    $(element).prop("name", newName);

                    if ($(element).find("option:selected").val() === "select") {
                        let add_option_elem = $(element).parent().parent().parent().find(".addOption, .addOptionGroup");
                        $(add_option_elem).off("click", addOptionEvent);
                        $(add_option_elem).on("click", addOptionEventGroup);
                    }
                }
            });

            sortableOnUpdate(evt);
            sortableOnUpdateGroup(evt);
        }
    }

    function sortableOnUpdateGroup(evt) {
        var inputsList = (evt.item) ? $(evt.item).parent().find(".list-group-item.nested-1") : [];
        var groupIndex = (evt.item) ? $(evt.item).parent().attr("data-id") : 0;

        for (let index = 0; index < inputsList.length; index++) {
            var elem = $(inputsList)[index];

            $(elem).attr("data-id", index);

            var inputs = $(elem).find("input, select");

            $(inputs).each(function (x, element) {
                let nameRegex = /input\[([0-9]+)]\[champs]\[([0-9]+)](.+)/;
                let currentName = $(element).prop("name");

                if (currentName) {
                    let nbName = currentName.match(nameRegex);

                    let newName = currentName.replace(/input\[([0-9]+)]\[champs]\[([0-9]+)].+/, "input[" + groupIndex + "][champs][" + index + "]" + nbName[3]);

                    $(element).prop("name", newName);

                    if ($(element).hasClass("select-multiple")) {
                        $(element).removeClass("ms-offscreen");
                        $(element).removeAttr("style");
                        $(element).parent().find("div").remove();
                        $(element).multipleSelect({
                            filter: true,
                            displayDelimiter: ' | '
                        });
                    }
                }
            });

            var rowOptions = $(elem).find("> div.options tr.accordion-options");
            if (rowOptions.length) {
                $(rowOptions).find("table.form-table:first tbody:first > tr").attr("data-id", index);
                var inputsOptions = $(rowOptions).find("input");
                if (inputsOptions.length) {
                    $(inputsOptions).each(function (x, element) {
                        let nameRegexOptions = new RegExp('input\\[([0-9]+)]\\[champs]\\[([0-9]+)]\\[options]\\[([0-9]+)](.+)', "i");
                        let nameRegex = new RegExp('input\\[([0-9]+)]\\[champs]\\[([0-9]+)]\\[options]\\[(\\w+)]', "i");
                        let currentName = $(element).prop("name");

                        if (currentName) {
                            let nbName = currentName.match(nameRegexOptions);
                            if (nbName) {
                                let newIdOption = parseInt(nbName[3]) + 1;
                                let newName = currentName.replace(/input\[([0-9]+)]\[champs]\[([0-9]+)]\[options]\[([0-9]+)].+/, "input[" + groupIndex + "][champs][" + index + "][options][" + newIdOption + "]" + nbName[3]);

                                $(element).prop("name", newName);
                            }

                            nbName = currentName.match(nameRegex);
                            if (nbName) {
                                let name = nbName[3];
                                let newName = currentName.replace(/input\[([0-9]+)]\[champs]\[([0-9]+)]\[options]\[(\w+)]/, "input[" + groupIndex + "][champs][" + index + "][options][" + name + "]");

                                $(element).prop("name", newName);
                            }
                        }
                    });
                }

            }

        }
    }

    function changeInputEventGroup() {
        if ($(this).find("option:selected").val().toLowerCase() === "file") {
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('enable');

            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            $(this).parent().parent().find("div.input_multiple input").prop("disabled", true);
            $(this).parent().parent().find("div.input_search input").prop("disabled", true);

        } else if ($(this).find("option:selected").val().toLowerCase() === "checkbox") {
            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");
            let groupId = $(this).parent().parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Invert value sent :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Invert value sent", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][checkboxes][invert]' type='checkbox'></td>" +
                "</tr>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');
        } else if ($(this).find("option:selected").val().toLowerCase() === "number") {
            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");
            let groupId = $(this).parent().parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td></td>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Min :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Min", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][numbers][min]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "<tr>" +
                "<td>" + __("Max :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Max", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][numbers][min]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "<tr>" +
                "<td>" + __("Step :", "infocob-crm-forms") + "</td>" +
                "<td><input data-tippy-content='" + __("Step", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][numbers][step]' type='number' step='any' value=''></td>" +
                "</tr>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');
        } else if ($(this).find("option:selected").val().toLowerCase() === "select") {
            let accordion_btn = "<span class='dashicons dashicons-admin-generic option-dashicon'></span>";

            let id = $(this).parent().parent().parent().data("id");
            let groupId = $(this).parent().parent().parent().parent().data("id");

            let accordion_content = "" +
                "<table class='form-table inputs'>" +
                "<tr class='accordion-options'>" +
                "<td></td>" +
                "<td></td>" +
                "<td></td>" +
                "<td colspan='7'>" +
                "<table class='form-table'>" +
                "<tbody>" +
                "<tr>" +
                "<td>" + __("Placeholder : ", "infocob-crm-forms") + "</td>" +
                "<td colspan='3'>" +
                "<input data-tippy-content='" + __("Placeholder", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][options][placeholder]' type='text' value=''>" +
                "</td>" +
                "</tr>" +
                "<tr>" +
                "<th></th>" +
                "<th>" + __("Label", "infocob-crm-forms") + "</th>" +
                "<th>" + __("Value", "infocob-crm-forms") + "</th>" +
                "<th class='sm'>" + __("Default", "infocob-crm-forms") + "</th>" +
                "<th class='sm'></th>" +
                "</tr>" +
                "<tr class='opt-row' data-id='" + id + "'>" +
                "<td class='up-down-dashicons'>" +
                "<div>" +
                "<span class='row-up-option dashicons dashicons-arrow-up-alt2'></span>" +
                "<span class='row-down-option dashicons dashicons-arrow-down-alt2'></span>" +
                "</div>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Label", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][options][0][libelle]' type='text'>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Value", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][options][0][valeur]' type='text'>" +
                "</td>" +
                "<td>" +
                "<input data-tippy-content='" + __("Default", "infocob-crm-forms") + "' name='input[" + groupId + "][champs][" + id + "][options][0][selected]' type='checkbox' value='1'>" +
                "</td>" +
                "<td>" +
                "<button class='delOption' type='button'>" + __("Delete", "infocob-crm-forms") + "</button>" +
                "</td>" +
                "</tr>" +
                "</tbody>" +
                "<tfoot>" +
                "<tr>" +
                "<td colspan='4'>" +
                "<button class='addOption' type='button'>" + __("Add", "infocob-crm-forms") + "</button>" +
                "</td>" +
                "</tr>" +
                "</tfoot>" +
                "</table>" +
                "</td>" +
                "<td></td>" +
                "</tr>";
            "</table>";

            $(this).after($(accordion_btn));
            $(this).parent().parent().parent().find("> div.options").append($(accordion_content));

            $(this).next().on("click", function () {
                if ($(this).parent().parent().parent().find("> div.options").hasClass("hidden")) {
                    $(this).parent().parent().parent().find("> div.options").removeClass("hidden");
                } else {
                    $(this).parent().parent().parent().find("> div.options").addClass("hidden");
                }
            });

            let add_option_elem = $(this).parent().parent().parent().find(".addOption");
            $(add_option_elem).off("click");
            $(add_option_elem).addClass("addOptionGroup");
            $(add_option_elem).removeClass("addOption");
            $(add_option_elem).on("click", addOptionEventGroup);

            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');
            $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
            $(this).parent().parent().find("div.input_search input").prop("disabled", false);

            tippy('[data-tippy-content]');

        } else {
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('uncheckAll');
            $(this).parent().parent().find("div.accept-file select.select-multiple").multipleSelect('disable');

            $(this).parent().parent().parent().find("> div.options").children().remove();
            $(this).next().remove();

            if ($(this).find("option:selected").val().toLowerCase() === "checkbox") {
                $(this).parent().parent().find("div.input_multiple input").prop("disabled", false);
                $(this).parent().parent().find("div.input_search input").prop("disabled", false);
            } else {
                $(this).parent().parent().find("div.input_multiple input").prop("disabled", true);
                $(this).parent().parent().find("div.input_search input").prop("disabled", true);
            }
        }
    }

    function addOptionEventGroup() {
        let newRowInputs = $(this).parent().parent().parent().parent().find("tbody tr:last").clone();

        $(newRowInputs).find("input").each(function (index, element) {
            let nameRegex = new RegExp('input\\[([0-9]+)]\\[champs]\\[([0-9]+)]\\[options]\\[([0-9]+)](.+)', "i");
            let currentName = $(element).prop("name");

            if (currentName) {
                let nbName = currentName.match(nameRegex);
                var newId = parseInt(nbName[3]) + 1;
                let newName = currentName.replace(/input\[([0-9]+)]\[champs]\[([0-9]+)]\[options]\[([0-9]+)].+/, "input[" + nbName[1] + "][champs][" + nbName[2] + "][options][" + newId + "]" + nbName[4]);

                $(element).prop("name", newName);
                if ($(element).prop("type").toLowerCase() === "checkbox") {
                    $(element).prop("checked", false);
                } else {
                    $(element).prop("value", "");
                }
            }
        });

        $(this).parent().parent().parent().parent().parent().find("tbody").append(newRowInputs);

        $(".delOption").off("click", delOption);
        $(".delOption").on("click", delOption);

        tippy('[data-tippy-content]');
    }

    function deleteInputGroup() {
        $(this).parent().parent().parent().parent().remove();
    }

    /*
        Media Wordpress
     */

    // Uploading files
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id

    $(".logo_email").each(function (index, element) {
        let set_to_post_id = $(element).find(".logo_attachment_id").val();

        $(element).find("input.button.upload_logo").on("click", function (event) {
            event.preventDefault();
            let file_frame;

            // If the media frame already exists, reopen it.
            if (file_frame) {
                // Set the post ID to what we want
                file_frame.uploader.uploader.param('post_id', set_to_post_id);
                // Open frame
                file_frame.open();
                return;
            } else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = set_to_post_id;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: __('Choose a logo to use', 'infocob-crm-forms'),
                button: {
                    text: __('Use this logo', 'infocob-crm-forms'),
                },
                multiple: false,	// Set to true to allow multiple files to be selected
                library: {
                    type: [ 'image' ]
                },
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();

                // Do something with attachment.id and/or attachment.url here
                $(element).find("img.logo_preview").attr('src', attachment.url).css('width', 'auto');
                $(element).find("input.logo_attachment_id").val(attachment.id);

                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });

            // Finally, open the modal
            file_frame.open();
        });

        $(element).find("button.remove_logo").on("click", function () {
            $(this).parents("td.logo_email").find("img.logo_preview").prop("src", "");
            $(this).parents("td.logo_actions").find("input.logo_attachment_id").val("");
        });
    });

    $(".attachments_email").each(function (index, element) {
        // let set_to_post_id = $(element).find(".attachments_attachment_id").val();

        $(element).find("input.button.upload_attachments").on("click", function (event) {
            event.preventDefault();
            let file_frame;

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: __('Choose files to use', 'infocob-crm-forms'),
                button: {
                    text: __('Choose', 'infocob-crm-forms'),
                },
                multiple: true,	// Set to true to allow multiple files to be selected
                // library: {
                //     type: [ 'image', 'application/pdf', 'application/vnd.ms-excel' ]
                // },
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                attachments = file_frame.state().get('selection').toJSON();

                let id = $(element).data("id");

                attachments.forEach((attachment) => {
                    // language=html
                    let newInput = `
						<div class="input">
							<figure class='image-preview-wrapper'>
								<img class='attachment_preview' src="${attachment.url}" width='100' height='100' style='max-height: 100px; width: 100px;'/>
								<figcaption>${attachment.title}</figcaption>
							</figure>
							<input type='hidden' name='additional_email[${id}][attachments][][attachment_id]' class='attachments_attachment_id' value='${attachment.id}'>
							<button class="remove_attachment" type="button">${ __("Remove attachment", "infocob-crm-forms") }</button>
						</div>
                    `;

                    $(element).find(".inputs").append(newInput);

                    $(element).find("button.remove_attachment").last().on("click", function () {
                        $(this).parent().remove();
                    });
                });

                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });

            // Finally, open the modal
            file_frame.open();
        });

        $(element).find("button.remove_attachment").on("click", function () {
            $(this).parent().remove();
        });
    });


    // Restore the main ID when the add media button is pressed
    $('a.add_media').on("click", function () {
        wp.media.model.settings.post.id = wp_media_post_id;
    });

});
