const {__, _x, _n, sprintf} = wp.i18n;

jQuery(document).ready(function($) {
    
    $("#add-recipient").off("click").on("click", addRecipientEvent);
    $(".del-recipient").off("click").on("click", delRecipientEvent);
    
});

function addRecipientEvent(event) {
    let container = jQuery("#recipients-table tbody");
    jQuery(container).append(`
        <tr>
            <td>
                <input type="text" name="recipients[ 0 ][lastname]" value="" placeholder="${ __("Lastname", "infocob-crm-forms") }">
            </td>
            <td>
                <input type="text" name="recipients[ 0 ][firstname]" value="" placeholder="${ __("Firstname", "infocob-crm-forms") }">
            </td>
            <td>
                <input type="email" name="recipients[ 0 ][email]" value="" placeholder="${ __("Email", "infocob-crm-forms") }">
            </td>
            <td class="has-checkbox">
                <input type="checkbox" name="recipients[ 0 ][cc]">
            </td>
            <td class="has-checkbox">
                <input type="checkbox" name="recipients[ 0 ][bcc]">
            </td>
            <td>
                <button class="del-recipient" type="button">${ __("Delete", "infocob-crm-forms") }</button>
            </td>
        </tr>
    `);
    
    recalculateIndexes();
}

function delRecipientEvent(event) {
    let element = jQuery(event.currentTarget);
    let row = jQuery(element).parents("tr").first();
    jQuery(row).remove();
    
    recalculateIndexes();
}

function recalculateIndexes() {
    let rows = jQuery("#recipients-table tbody > tr");
    
    jQuery(rows).each((index, row) => {
        let inputs = jQuery(row).find("input");
        
        jQuery(inputs).each((key, input) => {
            let current_name = jQuery(input).attr("name");
            let new_name = current_name.replace(/(.+\[).+(]\[.+])/gm, `$1 ${ index } $2`);
            jQuery(input).attr("name", new_name);
        });
    });
    
    jQuery(".del-recipient").off("click").on("click", delRecipientEvent);
}
