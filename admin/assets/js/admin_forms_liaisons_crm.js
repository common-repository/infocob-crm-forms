const { __, _x, _n, sprintf } = wp.i18n;

jQuery(document).ready(function($) {
    $(window).bind("beforeunload", function() {
        toggleLoader(true);
    });
    
    addEventPluginInfocobAdmin();
    
    jQuery(".inventaire_enable").on("change", (event) => {
        if(jQuery(event.currentTarget).prop("checked")) {
            jQuery(event.currentTarget).nextAll("table.inventaire").removeClass("hide");
            jQuery(event.currentTarget).prevAll("h3").first().find(".spanClick").removeClass("hide");
        } else {
            jQuery(event.currentTarget).nextAll("table.inventaire").addClass("hide");
            jQuery(event.currentTarget).prevAll("h3").first().find(".spanClick").addClass("hide");
        }
    }).trigger("change");
    
    $(".itemNav a.tab-navigation").on("click", navigation);
    
    $("#addRowAction").on("click", addRowAction);
    $("#addRowContact").on("click", addRowContact);
    $("#addRowInterlocuteur").on("click", addRowInterlocuteur);
    $("#addRowAffaire").on("click", addRowAffaire);
    $("#addRowProduit").on("click", addRowProduit);
    $("#addRowTicket").on("click", addRowTicket);
    $("#addRowContrat").on("click", addRowContrat);
    $("#addRowHistorique").on("click", addRowHistorique);
    
    $(".spanClick.deleteRowAction").on("click", deleteRowAction);
    $(".spanClick.deleteRowContact").on("click", deleteRowContact);
    $(".spanClick.deleteRowInterlocuteur").on("click", deleteRowInterlocuteur);
    $(".spanClick.deleteRowAffaire").on("click", deleteRowAffaire);
    $(".spanClick.deleteRowProduit").on("click", deleteRowProduit);
    $(".spanClick.deleteRowTicket").on("click", deleteRowTicket);
    $(".spanClick.deleteRowContrat").on("click", deleteRowContrat);
    $(".spanClick.deleteRowHistorique").on("click", deleteRowHistorique);
    
    $("#addRowInventaireAction").on("click", addRowInventaireAction);
    $("#addRowInventaireContact").on("click", addRowInventaireContact);
    $("#addRowInventaireInterlocuteur").on("click", addRowInventaireInterlocuteur);
    $("#addRowInventaireAffaire").on("click", addRowInventaireAffaire);
    $("#addRowInventaireProduit").on("click", addRowInventaireProduit);
    $("#addRowInventaireTicket").on("click", addRowInventaireTicket);
    $("#addRowInventaireContrat").on("click", addRowInventaireContrat);
    $("#addRowInventaireHistorique").on("click", addRowInventaireHistorique);
    
    $(".spanClick.deleteRowInventaireAction").on("click", deleteRowInventaireAction);
    $(".spanClick.deleteRowInventaireContact").on("click", deleteRowInventaireContact);
    $(".spanClick.deleteRowInventaireInterlocuteur").on("click", deleteRowInventaireInterlocuteur);
    $(".spanClick.deleteRowInventaireAffaire").on("click", deleteRowInventaireAffaire);
    $(".spanClick.deleteRowInventaireProduit").on("click", deleteRowInventaireProduit);
    $(".spanClick.deleteRowInventaireTicket").on("click", deleteRowInventaireTicket);
    $(".spanClick.deleteRowInventaireContrat").on("click", deleteRowInventaireContrat);
    $(".spanClick.deleteRowInventaireHistorique").on("click", deleteRowInventaireHistorique);
    
    
    $("#autres_destinataires, #destinataires").multipleSelect({
        filter: true,
    });
    
    // Delete data form
    var buttonDeleteDataForm = document.getElementById("deleteData");
    var postId = document.querySelector("input[name='post_id']");
    
    if(buttonDeleteDataForm) {
        buttonDeleteDataForm.addEventListener("click", deleteData, false);
        
        function deleteData(e) {
            var form_type = $(this).data("type");
            console.log(form_type);
            if(confirm(__('Do you really want to delete all the form data ?', 'infocob-crm-forms'))) {
                
                var data = {
                    'action': 'delete_data_form_liaisons',
                    'post_type': 'POST',
                    'postId': postId.value,
                    'form_type': form_type,
                    'nonce': infocob_ajax_delete_form_liaisons.nonce
                };
                
                jQuery.post(infocob_ajax_delete_form_liaisons.url, data, function(response) {
                    alert(response);
                    document.location.href = "?post_type=ifb_crm_forms&page=infocob-crm-forms-admin-liaisons-crm-page";
                });
            }
        }
    }
    
    function addEventPluginInfocobAdmin() {
        
        var inputGetTable = document.getElementsByClassName('inputGetTable');
        var array = Array.from(inputGetTable);
        array.forEach(function(element) {
            if(element.value == "true" && element.checked) {
                var parent = element.closest(".container-table");
                var array = Array.from(parent.getElementsByClassName('module-table'));
                array.forEach(function(element) {
                    element.classList.remove("hide");
                    //element.style.display = "inline-block";
                });
            }
        });
        getTable();
    }
    
    function navigation() {
        if(this.dataset.table == "ACTIONS") {
            getParent(this);
            document.getElementById('data-action').style.display = "block";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "CONTACTFICHE") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "block";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "INTERLOCUTEURFICHE") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "block";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "AFFAIRE") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "block";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "PRODUITFICHE") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "block";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "TICKET") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "block";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "HISTORIQUE") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "none";
            document.getElementById('data-historique').style.display = "block";
            this.classList.add('navData-active');
        } else if(this.dataset.table == "CONTRAT") {
            getParent(this);
            document.getElementById('data-action').style.display = "none";
            document.getElementById('data-contact').style.display = "none";
            document.getElementById('data-interlocuteur').style.display = "none";
            document.getElementById('data-affaire').style.display = "none";
            document.getElementById('data-produit').style.display = "none";
            document.getElementById('data-ticket').style.display = "none";
            document.getElementById('data-contrat').style.display = "block";
            document.getElementById('data-historique').style.display = "none";
            this.classList.add('navData-active');
        }
    }
    
    function getParent(element) {
        var ul = element.parentNode.parentNode;
        var li = ul.children;
        var array = Array.from(li);
        array.forEach(function(elementArray) {
            elementArray.children[0].classList.remove('navData-active');
        });
    }
    
    // For Action
    
    function addRowAction() {
        compteur = document.getElementsByClassName('addRowAction').length;
        var selectAction = document.getElementById('selectAction').cloneNode(true);
        
        var newRow = document.getElementById('tableAction').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[action][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowAction');
        
        
        var classList = document.getElementsByClassName('addRowAction');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[action][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[action][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[action][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowAction">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowAction").last().on("click", deleteRowAction);
    }
    
    function deleteRowAction() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableAction');
        table.deleteRow(indexElement);
    }
    
    function addRowInventaireAction() {
        compteur = document.getElementsByClassName('addRowInventaireAction').length;
        var selectAction = document.getElementById('selectInventaireAction').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireAction').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-action][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireAction');
        
        
        var classList = document.getElementsByClassName('addRowInventaireAction');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[inventaire-action][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-action][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-action][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireAction">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireAction").last().on("click", deleteRowInventaireAction);
    }
    
    function addRowInventaireContact() {
        compteur = document.getElementsByClassName('addRowInventaireContact').length;
        var selectContact = document.getElementById('selectInventaireContact').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireContact').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-contact][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireContact');
        
        
        var classList = document.getElementsByClassName('addRowInventaireContact');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectContact);
        newCell.children[0].children[0].name = 'data[inventaire-contact][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-contact][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-contact][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireContact">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireContact").last().on("click", deleteRowInventaireContact);
    }
    
    function addRowInventaireInterlocuteur() {
        compteur = document.getElementsByClassName('addRowInventaireInterlocuteur').length;
        var selectInterlocuteur = document.getElementById('selectInventaireInterlocuteur').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireInterlocuteur').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-interlocuteur][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireInterlocuteur');
        
        
        var classList = document.getElementsByClassName('addRowInventaireInterlocuteur');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectInterlocuteur);
        newCell.children[0].children[0].name = 'data[inventaire-interlocuteur][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-interlocuteur][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-interlocuteur][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireInterlocuteur">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireInterlocuteur").last().on("click", deleteRowInventaireContact);
    }
    
    function addRowInventaireAffaire() {
        compteur = document.getElementsByClassName('addRowInventaireAffaire').length;
        var selectAffaire = document.getElementById('selectInventaireAffaire').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireAffaire').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-affaire][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireAffaire');
        
        
        var classList = document.getElementsByClassName('addRowInventaireAffaire');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAffaire);
        newCell.children[0].children[0].name = 'data[inventaire-affaire][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-affaire][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-affaire][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireAffaire">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireAffaire").last().on("click", deleteRowInventaireAffaire);
    }
    
    function addRowInventaireProduit() {
        compteur = document.getElementsByClassName('addRowInventaireProduit').length;
        var selectProduit = document.getElementById('selectInventaireProduit').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireProduit').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-produit][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireProduit');
        
        
        var classList = document.getElementsByClassName('addRowInventaireProduit');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectProduit);
        newCell.children[0].children[0].name = 'data[inventaire-produit][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-produit][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-produit][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireProduit">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireProduit").last().on("click", deleteRowInventaireProduit);
    }
    
    function addRowInventaireTicket() {
        compteur = document.getElementsByClassName('addRowInventaireTicket').length;
        var selectTicket = document.getElementById('selectInventaireTicket').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireTicket').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-ticket][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireTicket');
        
        
        var classList = document.getElementsByClassName('addRowInventaireTicket');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectTicket);
        newCell.children[0].children[0].name = 'data[inventaire-ticket][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-ticket][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-ticket][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireTicket">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireTicket").last().on("click", deleteRowInventaireTicket);
    }
    
    function addRowInventaireContrat() {
        compteur = document.getElementsByClassName('addRowInventaireContrat').length;
        var selectContrat = document.getElementById('selectInventaireContrat').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireContrat').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-contrat][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireContrat');
        
        
        var classList = document.getElementsByClassName('addRowInventaireContrat');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectContrat);
        newCell.children[0].children[0].name = 'data[inventaire-contrat][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-contrat][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-contrat][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireContrat">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireContrat").last().on("click", deleteRowInventaireContrat);
    }
    
    function addRowInventaireHistorique() {
        compteur = document.getElementsByClassName('addRowInventaireHistorique').length;
        var selectHistorique = document.getElementById('selectInventaireHistorique').cloneNode(true);
        
        var newRow = document.getElementById('tableInventaireHistorique').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[inventaire-historique][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '"/>';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInventaireHistorique');
        
        
        var classList = document.getElementsByClassName('addRowInventaireHistorique');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectHistorique);
        newCell.children[0].children[0].name = 'data[inventaire-historique][moreData][' + compteur + '][champ]';
        newCell.children[0].children[0].value = '';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-historique][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[inventaire-historique][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInventaireHistorique">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInventaireHistorique").last().on("click", deleteRowInventaireHistorique);
    }
    
    function deleteRowInventaireAction() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireAction');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireContact() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireContact');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireInterlocuteur() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireInterlocuteur');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireAffaire() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireAffaire');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireProduit() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireProduit');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireTicket() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireTicket');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireContrat() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireContrat');
        table.deleteRow(indexElement);
    }
    
    function deleteRowInventaireHistorique() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInventaireHistorique');
        table.deleteRow(indexElement);
    }
    
    // Finish Action
    
    // For Contact
    
    function addRowContact() {
        compteur = document.getElementsByClassName('addRowContact').length;
        var selectAction = document.getElementById('selectContact').cloneNode(true);
        
        var newRow = document.getElementById('tableContact').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[contact][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowContact');
        
        var classList = document.getElementsByClassName('addRowContact');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[contact][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[contact][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[contact][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowContact">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowContact").last().on("click", deleteRowContact);
    }
    
    function deleteRowContact() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableContact');
        table.deleteRow(indexElement);
    }
    
    // Finish Contact
    
    // For Interlocuteur
    
    function addRowInterlocuteur() {
        compteur = document.getElementsByClassName('addRowInterlocuteur').length;
        var selectAction = document.getElementById('selectInterlocuteur').cloneNode(true);
        
        var newRow = document.getElementById('tableInterlocuteur').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[interlocuteur][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowInterlocuteur');
        
        var classList = document.getElementsByClassName('addRowInterlocuteur');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[interlocuteur][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[interlocuteur][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[interlocuteur][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowInterlocuteur">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowInterlocuteur").last().on("click", deleteRowInterlocuteur);
    }
    
    function deleteRowInterlocuteur() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableInterlocuteur');
        table.deleteRow(indexElement);
    }
    
    // Finish Interlocuteur
    
    // For Affaire
    
    function addRowAffaire() {
        compteur = document.getElementsByClassName('addRowAffaire').length;
        var selectAction = document.getElementById('selectAffaire').cloneNode(true);
        
        var newRow = document.getElementById('tableAffaire').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[affaire][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowAffaire');
        
        var classList = document.getElementsByClassName('addRowAffaire');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[affaire][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[affaire][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[affaire][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowAffaire">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowAffaire").last().on("click", deleteRowAffaire);
    }
    
    function deleteRowAffaire() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableAffaire');
        table.deleteRow(indexElement);
    }
    
    // Finish Affaire
    
    // For Produit
    
    function addRowProduit() {
        compteur = document.getElementsByClassName('addRowProduit').length;
        var selectAction = document.getElementById('selectProduit').cloneNode(true);
        
        var newRow = document.getElementById('tableProduit').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[produit][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowProduit');
        
        var classList = document.getElementsByClassName('addRowProduit');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[produit][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[produit][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[produit][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowProduit">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowProduit").last().on("click", deleteRowProduit);
    }
    
    function deleteRowProduit() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableProduit');
        table.deleteRow(indexElement);
    }
    
    // Finish Produit
    
    // For Ticket
    
    function addRowTicket() {
        compteur = document.getElementsByClassName('addRowTicket').length;
        var selectAction = document.getElementById('selectTicket').cloneNode(true);
        
        var newRow = document.getElementById('tableTicket').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[ticket][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowTicket');
        
        var classList = document.getElementsByClassName('addRowTicket');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[ticket][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[ticket][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[ticket][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowTicket">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowTicket").last().on("click", deleteRowTicket);
    }
    
    function deleteRowTicket() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableTicket');
        table.deleteRow(indexElement);
    }
    
    // Finish Ticket
    
    // For Contrat
    
    function addRowContrat() {
        compteur = document.getElementsByClassName('addRowContrat').length;
        var selectAction = document.getElementById('selectContrat').cloneNode(true);
        
        var newRow = document.getElementById('tableContrat').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[contrat][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowContrat');
        
        var classList = document.getElementsByClassName('addRowContrat');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[contrat][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[contrat][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[contrat][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowContrat">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowContrat").last().on("click", deleteRowContrat);
    }
    
    function deleteRowContrat() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableContrat');
        table.deleteRow(indexElement);
    }
    
    // Finish Contrat
    
    // For Historique
    
    function addRowHistorique() {
        compteur = document.getElementsByClassName('addRowHistorique').length;
        var selectAction = document.getElementById('selectHistorique').cloneNode(true);
        
        var newRow = document.getElementById('tableHistorique').insertRow(-1);
        var newCell = newRow.insertCell(0);
        newCell.innerHTML = '<input type="text" name="data[historique][moreData][' + compteur + '][value]" placeholder="' + __("Value", "infocob-crm-forms") + '" />';
        
        newCell = newRow.insertCell(1);
        newCell.classList.add('addRowHistorique');
        
        var classList = document.getElementsByClassName('addRowHistorique');
        var nbLenght = classList.length;
        
        classList[nbLenght - 1].appendChild(selectAction);
        newCell.children[0].children[0].name = 'data[historique][moreData][' + compteur + '][champ]';
        
        newCell = newRow.insertCell(2);
        newCell.innerHTML = '<input type="checkbox" name="data[historique][moreData][' + compteur + '][pivot]" value="true" />';
        
        newCell = newRow.insertCell(3);
        newCell.innerHTML = '<input type="checkbox" name="data[historique][moreData][' + compteur + '][maj]" value="true" />';
        
        newCell = newRow.insertCell(4)
        newCell.innerHTML = '<span class="spanClick deleteRowHistorique">' + __("Delete row", "infocob-crm-forms") + '</span>';
        
        $(".spanClick.deleteRowHistorique").last().on("click", deleteRowHistorique);
    }
    
    function deleteRowHistorique() {
        var indexElement = this.parentNode.parentNode.rowIndex;
        var table = document.getElementById('tableHistorique');
        table.deleteRow(indexElement);
    }
    
    // Finish Historique
    
    function getTable() {
        var inputGetTable = document.getElementsByClassName('inputGetTable');
        var array = Array.from(inputGetTable);
        
        for(let i = 0; i < inputGetTable.length; i++) {
            inputGetTable[i].onchange = displayTable;
        }
        
    }
    
    
    function displayTable(element) {
        var parent = this.closest(".container-table");
        var array = Array.from(parent.getElementsByClassName('module-table'));
        if(this.value == "true") {
            array.forEach(function(element) {
                element.classList.remove("hide");
                //element.style.display = "inline-table";
            });
        } else {
            array.forEach(function(element) {
                //element.style.display = "none";
                element.classList.add("hide");
            });
        }
        
    }
    
    function toggleLoader(state = null) {
        let loader = $("div.infocob_crm_forms_loader");
        if(loader.is(':visible') || state === false) {
            loader.removeClass('loading');
            $("body").css("overflow", "inherit");
        } else if(loader.is(":hidden") || state === true) {
            $("body").css("overflow", "hidden");
            loader.addClass('loading');
        }
    }
});
