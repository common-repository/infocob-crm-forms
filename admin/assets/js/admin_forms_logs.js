const {__, _x, _n, _nx} = wp.i18n;

var table = undefined;

jQuery(document).ready(function ($) {
    $("#logs-file").on("change", onChangeLogs);
});

function getLogsFile(filename, level = "mails") {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: infocob_ajax_get_logs_forms.url ?? "",
            method: 'GET',
            data: {
                action: 'infocob_crm_forms_get_logs_file',
                security: infocob_ajax_get_logs_forms.nonce ?? "",
                filename: filename,
                level: level,
            }
        }).done((response) => {
            if (response.success) {
                resolve(response.data);
            } else {
                console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-forms'))
                reject();
            }
        });
    });
}

function downloadLogsFile(filename) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: infocob_ajax_get_logs_forms.url ?? "",
            method: 'GET',
            data: {
                action: 'infocob_crm_forms_download_logs_file',
                security: infocob_ajax_get_logs_forms.nonce ?? "",
                filename: filename,
            },
            xhrFields: {
                responseType: 'blob'
            },
        }).done((response) => {
            if (response !== 0) {
                let aElement = document.createElement('a');
                aElement.setAttribute('download', filename);
                let href = URL.createObjectURL(response);
                aElement.href = href;
                aElement.setAttribute('target', '_blank');
                aElement.click();
                URL.revokeObjectURL(href);
                resolve();
            } else {
                console.error(_x("Unable to retrieve data", "JS error ajax request", 'infocob-crm-forms'))
                reject();
            }
        });
    });
}

function onChangeLogs(event) {
    let file = jQuery(event.currentTarget).val();

    getLogsFile(file).then((responses) => {
        loadDataSet(responses);

        jQuery("#logs input.download").on("click", onClickDownload);
    });
}

function onClickDownload(event) {
    let filename = jQuery(event.currentTarget).data("filename");

    if (filename !== "") {
        downloadLogsFile(filename);
    }
}

function loadDataSet(responses) {
    // Format data
    let dataSet = [];
    responses.forEach((json) => {
        let response = JSON.parse(json);

        let context = response.context ?? {};

        let formatedDateTime = "";
        let dateTimeString = response.datetime ?? "";
        if (dateTimeString !== "") {
            let dateTime = new Date(dateTimeString);

            let year = dateTime.getFullYear();
            let month = String(dateTime.getMonth() + 1).padStart(2, '0');
            let day = String(dateTime.getDate()).padStart(2, '0');

            formatedDateTime = `${day}/${month}/${year} ${dateTime.getHours()}:${dateTime.getMinutes()}:${dateTime.getSeconds()}`
        }

        let to = "";
        (context.to ?? []).forEach((dest, index) => {
            to += dest[0] ?? ""
        });
        (context.cc ?? []).forEach((dest, index) => {
            if (index === 0) {
                to += "; ";
            }

            to += dest[0] ?? ""
            if (index < context.cc.length - 1) {
                to += "; ";
            }
        });

        let data = {
            subject: response.message ?? "",
            to: to,
            text: context.alt_body ?? "",
            error: context.error ?? "",
            date: formatedDateTime ?? "",
            file: context.file ?? "",
        }

        dataSet.push(data);
    });

    // Defining columns
    let columns = [
        {
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: '',
        },
        {
            data: 'subject',
        },
        {
            data: 'to'
        },
        {
            data: 'error',
        },
        {
            data: 'date'
        },
        {
            data: 'text',
            visible: false,
        },
        {
            data: 'file',
            render: function (data, type, row) {
                if (data.url) {
                    return `<input type="button" data-filename="${data.name}"  class="button button-primary download" value="${__("Download", "infocob-crm-forms")}">`;
                } else {
                    return '';
                }
            },
            targets: 0,
        },
    ];

    // Generate table
    table = jQuery("#logs").DataTable({
        data: dataSet,
        columns: columns,
        createdRow: (row, data, dataIndex) => {
            if ((data.error ?? "") !== "") {
                jQuery(row).addClass("error");
            }
        },
        ordering: false,
        destroy: true,
        pageLength: 50,
    });

    // Add event listener for opening and closing details
    jQuery("#logs tbody").off("click", "td.dt-control").on("click", "td.dt-control", (event) => {
        let tr = jQuery(event.currentTarget).closest('tr');
        let row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(formatSubDataSet(row.data())).show();
            // row.child(row.data()).show();
            tr.addClass('shown');
        }
    });
}

function formatSubDataSet(data) {
    // language=html
    return `
		<table>
			<tr>
				<td></td>
				<td class="detail">
					<pre>${data.text}</pre>
				</td>
			</tr>
		</table>
    `;
}

function syntaxHighlight(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
