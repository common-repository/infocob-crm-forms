jQuery(document).ready(function($) {
    $("#export_form").on("submit", exportJson);
    
    function exportJson(event) {
        event.preventDefault();
        
        let post_id = $("#form_export_id").val();
        
        if(post_id) {
            let export_config_crm = $("#export_config_crm").prop("checked");
            
            var data = {
                'action': 'infocob_crm_forms_export_action',
                'post_type': 'POST',
                'form_export_id': post_id,
                'export_config_crm': export_config_crm,
                'nonce': infocob_ajax_export_form.nonce
            };
            
            jQuery.post(infocob_ajax_export_form.url, data, function(response) {
                let post_title = (response["post"]["post_title"]) ? response["post"]["post_title"] + "_infocob_crm_forms_export.json" : "infocob_crm_forms_export.json";
                var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(response));
                var dlExportJson = document.getElementById('download_export_json');
                dlExportJson.setAttribute("href", dataStr);
                dlExportJson.setAttribute("download", post_title);
                dlExportJson.click();
            });
        }
    }
    
});
