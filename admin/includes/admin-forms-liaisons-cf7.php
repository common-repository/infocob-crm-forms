<?php use Infocob\CrmForms\Admin\Tools;
	
	if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
	
	require_once(ABSPATH . 'wp-includes/pluggable.php');
?>

<form id="configForm" method="POST" action="<?php echo esc_attr('admin-post.php'); ?>" accept-charset="UTF-8">
    <input type="hidden" name="form_type" value="cf7" />
    <input type="hidden" name="post_id" value="<?php echo $post_id ?? ""; ?>" />
    <input type="hidden" name="action" value="save_form_liaisons" />
	<?php wp_nonce_field('infocob_save_form_liaisons_nonce', 'nonce'); ?>
    <div class="containerNav">
        <div class="wrapperTab">
            <!------------------------------------------->
            <!-- NAV -->
            <!------------------------------------------->
            
            <nav class="navData">
                <img src="<?php echo ROOT_INFOCOB_CRM_FORMS_DIR_URL . "admin/assets/logo-infocob-crm.png"; ?>" />
                <ul>
                    <li class="itemNav">
                        <a class="navData-active tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['ACTIONS'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['ACTIONS']) ? $libellesTables['ACTIONS'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['CONTACTFICHE'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['CONTACTFICHE']) ? $libellesTables['CONTACTFICHE'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['INTERLOCUTEURFICHE'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['INTERLOCUTEURFICHE']) ? $libellesTables['INTERLOCUTEURFICHE'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['AFFAIRE'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['AFFAIRE']) ? $libellesTables['AFFAIRE'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['PRODUITFICHE'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['PRODUITFICHE']) ? $libellesTables['PRODUITFICHE'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['TICKET'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
			                <?php echo isset($libellesTables['TICKET']) ? $libellesTables['TICKET'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['CONTRAT'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
			                <?php echo isset($libellesTables['CONTRAT']) ? $libellesTables['CONTRAT'] : ""; ?></a>
                    </li>
                    <li class="itemNav">
                        <a class="tab-navigation" href="#" data-table="<?php echo array_search(strtoupper($libellesTables['HISTORIQUE'] ?? ""), array_map('strtoupper', $libellesTables)); ?>">
							<?php echo isset($libellesTables['HISTORIQUE']) ? $libellesTables['HISTORIQUE'] : ""; ?></a>
                    </li>
                </ul>
            </nav>
            <div class="box-plugin box-data">
                
                <!------------------------------------------->
                <!-- DATA ACTION -->
                <!------------------------------------------->
                
                <div style="display: block;" id="data-action" class="data-action container-table">
                    <h3><?php _e("Action configuration", "infocob-crm-forms"); ?> <span id="addRowAction" class="spanClick module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span>
                    </h3>
					
					<?php if(isset($dataDbForm['tables'])) { ?>
						<?php if((isset($dataDbForm['tables']['action']) && $dataDbForm['tables']['action'] == "true") || !isset($dataDbForm['tables']['action'])) { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_action_oui" name='tables_action' value='true' checked />
                            <label for='tables_action_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_action_non" name='tables_action' value='false' />
                            <label for='tables_action_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_action_oui" name='tables_action' value='true' />
                            <label for='tables_action_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_action_non" name='tables_action' value='false' checked />
                            <label for='tables_action_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_action_oui" name='tables_action' value='true' />
                        <label for='tables_action_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_action_non" name='tables_action' value='false' checked />
                        <label for='tables_action_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableAction" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectAction" class="select">
                                            <select name="data[action][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataActions ?? []) as $field => $libAction) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['action'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['action'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libAction . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libAction . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['action'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['action'])) { ?>
                                                <input checked type="checkbox" name="pivot[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['action'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['action'])) { ?>
                                                <input checked type="checkbox" name="maj[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['action']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['action']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[action][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowAction">
                                            <div id="selectAction" class="select">
                                                <select name="data[action][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach(($dataActions ?? []) as $field => $libAction) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libAction . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libAction . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[action][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[action][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[action][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[action][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowAction"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
                        <h3><?php _e("Action Type", "infocob-crm-forms"); ?></h3>
                        <table border="0">
                            <tr>
                                <th><?php _e("Type", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Recipient", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Others", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Alarm", "infocob-crm-forms"); ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select id="typeaction" name="data[action][type_action][type]" size="1">
                                            <option></option>
											<?php
												// Types action | 1er niveau
												foreach(($typesaction['result'] ?? "") as $type1) {
													if(!empty($type1["LTA_CODE"])) {
														if(($dataDbForm['fieldAssoc']['action']['type_action']['type'] ?? "") == $type1['LTA_CODE']): ?>
                                                            <option class="type" selected value="<?php echo $type1['LTA_CODE']; ?>"><?php echo $type1['LTA_NOM']; ?></option>
														<?php else: ?>
                                                            <option class="type" value="<?php echo $type1['LTA_CODE']; ?>"><?php echo $type1['LTA_NOM']; ?></option>
														<?php endif;
														
														// Sous-types action | 2eme niveau
														if(!empty($type1["SOUS_TYPES"])) {
															foreach($type1["SOUS_TYPES"] as $type2) {
																if(!empty($type2["LTA_CODE"])) {
																	if(($dataDbForm['fieldAssoc']['action']['type_action']['type'] ?? "") == $type1['LTA_CODE'].".".$type2['LTA_CODE']): ?>
                                                                        <option class="subType" selected value="<?php echo $type1['LTA_CODE'].".".$type2['LTA_CODE']; ?>">
                                                                            --- <?php echo $type2['LTA_NOM']; ?></option>
																	<?php else: ?>
                                                                        <option class="subType" value="<?php echo $type1['LTA_CODE'].".".$type2['LTA_CODE']; ?>">
                                                                            --- <?php echo $type2['LTA_NOM']; ?></option>
																	<?php endif;
																	
																	// Sous-sous types action | 3eme niveau
																	if(!empty($type2["SOUS_TYPES"])) {
																		foreach($type2["SOUS_TYPES"] as $type3) {
																			if(!empty($type3["LTA_CODE"])) {
																				if(($dataDbForm['fieldAssoc']['action']['type_action']['type'] ?? "") == $type1['LTA_CODE'].".".$type2['LTA_CODE'].".".$type3['LTA_CODE']): ?>
                                                                                    <option class="subSubType" selected value="<?php echo $type1['LTA_CODE'].".".$type2['LTA_CODE'].".".$type3['LTA_CODE']; ?>">
                                                                                        ------ <?php echo $type3['LTA_NOM']; ?></option>
																				<?php else: ?>
                                                                                    <option class="subSubType" value="<?php echo $type1['LTA_CODE'].".".$type2['LTA_CODE'].".".$type3['LTA_CODE']; ?>">
                                                                                        ------ <?php echo $type3['LTA_NOM']; ?></option>
																				<?php endif;
																			}
																		}
																	}
																}
															}
														}
													}
												}
											?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[action][type_action][destinataires]" size="1">
                                            <option></option>
			                                <?php if(isset($vendeurs['result'])) { ?>
				                                <?php foreach($vendeurs['result'] as $vendeur) { ?>
					                                <?php if(($dataDbForm['fieldAssoc']['action']['type_action']['destinataires'] ?? "") == $vendeur['V_CODE']) { ?>
                                                        <option selected value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
					                                <?php } else { ?>
                                                        <option value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
					                                <?php } ?>
				                                <?php } ?>
			                                <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <select name="data[action][type_action][autres_destinataires][]" id="autres_destinataires" multiple="multiple" size="1">
                                        <optgroup label="Utilisateurs">
											<?php if(isset($vendeurs['result'])) { ?>
												<?php foreach($vendeurs['result'] as $vendeur) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['action']['type_action']['autres_destinataires']) && in_array($vendeur['V_CODE'], $dataDbForm['fieldAssoc']['action']['type_action']['autres_destinataires'])) { ?>
                                                        <option selected value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
													<?php } ?>
												<?php } ?>
											<?php } ?>
                                        </optgroup>
                                        <optgroup label="Groupements">
											<?php if(isset($groupements['result'])) { ?>
												<?php foreach($groupements['result'] as $keyGroupement => $valueGroupement) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['action']['type_action']['autres_destinataires']) && in_array($keyGroupement, $dataDbForm['fieldAssoc']['action']['type_action']['autres_destinataires'])) { ?>
                                                        <option selected value="<?php echo $keyGroupement; ?>"><?php echo $valueGroupement['nom']; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $keyGroupement; ?>"><?php echo $valueGroupement['nom']; ?></option>
													<?php } ?>
												<?php } ?>
											<?php } ?>
                                        </optgroup>
                                    </select>
                                </td>
                                <td class="center-text">
                                    <input type="checkbox" name="data[action][alarme]" id="alarme" value="1" <?php echo (isset($dataDbForm['fieldAssoc']['action']['alarme']) && ($dataDbForm['fieldAssoc']['action']['alarme'])) ? "checked" : ""; ?> />
                                </td>
                            </tr>
                        </table>
	
	                    <?php if(!empty($shortcodesFichiersLies)) { ?>
                            <h3><?php _e("Links files", "infocob-crm-forms"); ?></h3>
                            <table id="tableActionFichiersLies" border="0" style="text-align: center;">
                                <tr>
                                    <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Enable Infocob link", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Cloud file", "infocob-crm-forms"); ?></th>
                                </tr>
			                    <?php foreach($shortcodesFichiersLies as $key => $name_shortcode) { ?>
                                    <tr>
                                        <td><?php echo $name_shortcode ?></td>
                                        <td>
                                            <?php if(isset($dataDbForm['fichiersLies']['action']) && in_array($name_shortcode, $dataDbForm['fichiersLies']['action'])) { ?>
                                                <input checked type="checkbox" name="fichiersLies[action][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="fichiersLies[action][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($dataDbForm['cloudFichiers']['action']) && in_array($name_shortcode, $dataDbForm['cloudFichiers']['action'])) { ?>
                                                <input checked type="checkbox" name="cloudFichiers[action][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="cloudFichiers[action][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
	                    <?php } ?>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireAction" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_action" class="inventaire_enable" name="tables_inventaire_action" <?php echo ($dataDbForm['tables']['inventaire-action'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_action'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
                        
                        <table id="tableInventaireAction" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireAction" class="select">
                                            <select name="data[inventaire-action][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-action'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-action'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-action'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-action'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-action'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-action'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-action][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-action']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-action']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-action][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireAction">
                                            <div id="selectInventaireAction" class="select">
                                                <select name="data[inventaire-action][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-action][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-action][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-action][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-action][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireAction"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsActions) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsActions ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
                
                <!------------------------------------------->
                <!-- DATA CONTACT -->
                <!------------------------------------------->
                
                <div style="display: none;" id="data-contact" class="data-contact container-table">
                    
                    <h3><?php _e("Contact configuration", "infocob-crm-forms"); ?>
                        <span id="addRowContact" class="spanClick hide module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
					
					<?php if(isset($dataDbForm['tables'])) { ?>
						<?php if(isset($dataDbForm['tables']['contact']) && $dataDbForm['tables']['contact'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contact_oui" name='tables_contact' value='true' checked />
                            <label for='tables_contact_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_contact_non" name='tables_contact' value='false' />
                            <label for='tables_contact_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contact_oui" name='tables_contact' value='true' />
                            <label for='tables_contact_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_contact_non" name='tables_contact' value='false' checked />
                            <label for='tables_contact_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contact_oui" name='tables_contact' value='true' />
                        <label for='tables_contact_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_contact_non" name='tables_contact' value='false' checked />
                        <label for='tables_contact_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableContact" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div class="select" id="selectContact">
                                            <select name="data[contact][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataContact ?? []) as $field => $libContact) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['contact'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['contact'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libContact . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libContact . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['contact'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['contact'])) { ?>
                                                <input checked type="checkbox" name="pivot[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['contact'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['contact'])) { ?>
                                                <input checked type="checkbox" name="maj[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['contact']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['contact']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[contact][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowContact">
                                            <div id="selectContact" class="select">
                                                <select name="data[contact][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach(($dataContact ?? []) as $field => $libContact) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libContact . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libContact . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[contact][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[contact][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[contact][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[contact][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowContact"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireContact" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_contact" class="inventaire_enable" name="tables_inventaire_contact" <?php echo ($dataDbForm['tables']['inventaire-contact'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_contact'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireContact" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireContact" class="select">
                                            <select name="data[inventaire-contact][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-contact'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-contact'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-contact'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-contact'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-contact'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-contact'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-contact][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-contact']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-contact']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireContact">
                                            <div id="selectInventaireContact" class="select">
                                                <select name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-contact][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireContact"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsContact) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsContact ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
                
                <!------------------------------------------->
                <!-- DATA INTERLOCUTEUR -->
                <!------------------------------------------->
                
                <div style="display: none;" id="data-interlocuteur" class="data-interlocuteur container-table">
                    <h3><?php _e("Interlocuteur configuration", "infocob-crm-forms"); ?> <span id="addRowInterlocuteur" class="spanClick hide module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span>
                    </h3>
					
					<?php if(isset($dataDbForm['tables'])) { ?>
						<?php if(isset($dataDbForm['tables']['interlocuteur']) && $dataDbForm['tables']['interlocuteur'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_interlocuteur_oui" name='tables_interlocuteur' value='true' checked />
                            <label for='tables_interlocuteur_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_interlocuteur_non" name='tables_interlocuteur' value='false' />
                            <label for='tables_interlocuteur_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_interlocuteur_oui" name='tables_interlocuteur' value='true' />
                            <label for='tables_interlocuteur_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_interlocuteur_non" name='tables_interlocuteur' value='false' checked />
                            <label for='tables_interlocuteur_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_interlocuteur_oui" name='tables_interlocuteur' value='true' />
                        <label for='tables_interlocuteur_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_interlocuteur_non" name='tables_interlocuteur' value='false' checked />
                        <label for='tables_interlocuteur_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableInterlocuteur" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div class="select" id="selectInterlocuteur">
                                            <select name="data[interlocuteur][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataInterlocuteur ?? []) as $field => $libInterlocuteur) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['interlocuteur'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['interlocuteur'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInterlocuteur . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInterlocuteur . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['interlocuteur'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['interlocuteur'])) { ?>
                                                <input checked type="checkbox" name="pivot[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['interlocuteur'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['interlocuteur'])) { ?>
                                                <input checked type="checkbox" name="maj[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['interlocuteur']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['interlocuteur']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[interlocuteur][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInterlocuteur">
                                            <div id="selectInterlocuteur" class="select">
                                                <select name="data[interlocuteur][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach(($dataInterlocuteur ?? []) as $field => $libInterlocuteur) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInterlocuteur . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInterlocuteur . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[interlocuteur][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[interlocuteur][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[interlocuteur][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[interlocuteur][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td><span class="spanClick deleteRowInterlocuteur"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireInterlocuteur" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_interlocuteur" class="inventaire_enable" name="tables_inventaire_interlocuteur" <?php echo ($dataDbForm['tables']['inventaire-interlocuteur'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_interlocuteur'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireInterlocuteur" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireInterlocuteur" class="select">
                                            <select name="data[inventaire-interlocuteur][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-interlocuteur'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-interlocuteur'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-interlocuteur'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-interlocuteur'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-interlocuteur'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-interlocuteur'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-interlocuteur][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-interlocuteur']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-interlocuteur']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireInterlocuteur">
                                            <div id="selectInventaireInterlocuteur" class="select">
                                                <select name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach($dataInventaires as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-interlocuteur][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireInterlocuteur"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsInterlocuteur) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsInterlocuteur ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
                
                <!------------------------------------------->
                <!-- DATA AFFAIRE -->
                <!------------------------------------------->
                
                <div style="display: none;" id="data-affaire" class="data-affaire container-table">
                    <h3><?php _e("Affaire configuration", "infocob-crm-forms"); ?>
                        <span id="addRowAffaire" class="spanClick hide module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
					
					<?php if(isset($dataDbForm['tables'])) { ?>
						<?php if(isset($dataDbForm['tables']['affaire']) && $dataDbForm['tables']['affaire'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_affaire_oui" name='tables_affaire' value='true' checked />
                            <label for='tables_affaire_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_affaire_non" name='tables_affaire' value='false' />
                            <label for='tables_affaire_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_affaire_oui" name='tables_affaire' value='true' />
                            <label for='tables_affaire_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_affaire_non" name='tables_affaire' value='false' checked />
                            <label for='tables_affaire_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_affaire_oui" name='tables_affaire' value='true' />
                        <label for='tables_affaire_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_affaire_non" name='tables_affaire' value='false' checked />
                        <label for='tables_affaire_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableAffaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div class="select" id="selectAffaire">
                                            <select name="data[affaire][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataAffaire ?? []) as $field => $libAffaire) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['affaire'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['affaire'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libAffaire . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libAffaire . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['affaire'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['affaire'])) { ?>
                                                <input checked type="checkbox" name="pivot[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['affaire'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['affaire'])) { ?>
                                                <input checked type="checkbox" name="maj[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['affaire']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['affaire']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[affaire][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowAffaire">
                                            <div id="selectAffaire" class="select">
                                                <select name="data[affaire][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach(($dataAffaire ?? []) as $field => $libAffaire) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libAffaire . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libAffaire . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[affaire][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[affaire][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[affaire][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[affaire][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
                                        <span class="spanClick deleteRowAffaire"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
	
	                    <?php if(!empty($shortcodesFichiersLies)) { ?>
                            <h3><?php _e("Links files", "infocob-crm-forms"); ?></h3>
                            <table id="tableAffaireFichiersLies" border="0" style="text-align: center;">
                                <tr>
                                    <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Enable Infocob link", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Cloud file", "infocob-crm-forms"); ?></th>
                                </tr>
			                    <?php foreach($shortcodesFichiersLies as $key => $name_shortcode) { ?>
                                    <tr>
                                        <td><?php echo $name_shortcode ?></td>
                                        <td>
                                            <?php if(isset($dataDbForm['fichiersLies']['affaire']) && in_array($name_shortcode, $dataDbForm['fichiersLies']['affaire'])) { ?>
                                                <input checked type="checkbox" name="fichiersLies[affaire][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="fichiersLies[affaire][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($dataDbForm['cloudFichiers']['affaire']) && in_array($name_shortcode, $dataDbForm['cloudFichiers']['affaire'])) { ?>
                                                <input checked type="checkbox" name="cloudFichiers[affaire][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="cloudFichiers[affaire][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
	                    <?php } ?>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireAffaire" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_affaire" class="inventaire_enable" name="tables_inventaire_affaire" <?php echo ($dataDbForm['tables']['inventaire-affaire'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_affaire'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireAffaire" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireAffaire" class="select">
                                            <select name="data[inventaire-affaire][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-affaire'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-affaire'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-affaire'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-affaire'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-affaire'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-affaire'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-affaire][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-affaire']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-affaire']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireAffaire">
                                            <div id="selectInventaireAffaire" class="select">
                                                <select name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-affaire][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireAffaire"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsAffaire) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsAffaire ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
                
                <!------------------------------------------->
                <!-- DATA PRODUIT -->
                <!------------------------------------------->
                
                <div style="display: none;" id="data-produit" class="data-produit container-table">
                    <h3><?php _e("Produit configuration", "infocob-crm-forms"); ?>
                        <span id="addRowProduit" class="spanClick hide module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
					
					<?php if(isset($dataDbForm['tables'])) { ?>
						<?php if(isset($dataDbForm['tables']['produit']) && $dataDbForm['tables']['produit'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_produit_oui" name='tables_produit' value='true' checked />
                            <label for='tables_produit_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_produit_non" name='tables_produit' value='false' />
                            <label for='tables_produit_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_produit_oui" name='tables_produit' value='true' />
                            <label for='tables_produit_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_produit_non" name='tables_produit' value='false' checked />
                            <label for='tables_produit_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_produit_oui" name='tables_produit' value='true' />
                        <label for='tables_produit_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_produit_non" name='tables_produit' value='false' checked />
                        <label for='tables_produit_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableProduit" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div class="select" id="selectProduit">
                                            <select name="data[produit][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataProduit ?? []) as $field => $libProduit) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['produit'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['produit'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libProduit . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libProduit . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['produit'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['produit'])) { ?>
                                                <input checked type="checkbox" name="pivot[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['produit'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['produit'])) { ?>
                                                <input checked type="checkbox" name="maj[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['produit']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['produit']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[produit][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowProduit">
                                            <div id="selectProduit" class="select">
                                                <select name="data[produit][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach($dataProduit as $field => $libProduit) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libProduit . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libProduit . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[produit][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[produit][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[produit][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[produit][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowProduit"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
	
	                    <?php if(!empty($shortcodesFichiersLies)) { ?>
                            <h3><?php _e("Links files", "infocob-crm-forms"); ?></h3>
                            <table id="tableProduitFichiersLies" border="0" style="text-align: center;">
                                <tr>
                                    <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Enable Infocob link", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Cloud file", "infocob-crm-forms"); ?></th>
                                </tr>
			                    <?php foreach($shortcodesFichiersLies as $key => $name_shortcode) { ?>
                                    <tr>
                                        <td><?php echo $name_shortcode ?></td>
                                        <td>
                                            <?php if(isset($dataDbForm['fichiersLies']['produit']) && in_array($name_shortcode, $dataDbForm['fichiersLies']['produit'])) { ?>
                                                <input checked type="checkbox" name="fichiersLies[produit][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="fichiersLies[produit][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($dataDbForm['cloudFichiers']['produit']) && in_array($name_shortcode, $dataDbForm['cloudFichiers']['produit'])) { ?>
                                                <input checked type="checkbox" name="cloudFichiers[produit][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="cloudFichiers[produit][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
	                    <?php } ?>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireProduit" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_produit" class="inventaire_enable" name="tables_inventaire_produit" <?php echo ($dataDbForm['tables']['inventaire-produit'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_produit'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireProduit" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireProduit" class="select">
                                            <select name="data[inventaire-produit][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-produit'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-produit'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-produit'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-produit'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-produit'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-produit'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-produit][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-roduit']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-produit']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireProduit">
                                            <div id="selectInventaireProduit" class="select">
                                                <select name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-produit][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireProduit"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsProduit) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsProduit ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
    
                <!------------------------------------------->
                <!-- DATA TICKET -->
                <!------------------------------------------->
    
                <div style="display: none;" id="data-ticket" class="data-ticket container-table">
                    <h3><?php _e("Ticket configuration", "infocob-crm-forms"); ?> <span id="addRowTicket" class="spanClick module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span>
                    </h3>
		
		            <?php if(isset($dataDbForm['tables'])) { ?>
			            <?php if(isset($dataDbForm['tables']['ticket']) && $dataDbForm['tables']['ticket'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_ticket_oui" name='tables_ticket' value='true' checked />
                            <label for='tables_ticket_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_ticket_non" name='tables_ticket' value='false' />
                            <label for='tables_ticket_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
			            <?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_ticket_oui" name='tables_ticket' value='true' />
                            <label for='tables_ticket_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_ticket_non" name='tables_ticket' value='false' checked />
                            <label for='tables_ticket_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
			            <?php } ?>
		            <?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_ticket_oui" name='tables_ticket' value='true' />
                        <label for='tables_ticket_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_ticket_non" name='tables_ticket' value='false' checked />
                        <label for='tables_ticket_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
		            <?php } ?>
        
                    <div class="module-table hide">
                        <table id="tableTicket" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
				            <?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectTicket" class="select">
                                            <select name="data[ticket][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
									            <?php foreach(($dataTicket ?? []) as $field => $libTicket) { ?>
										            <?php if(isset($dataDbForm['fieldAssoc']['ticket'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['ticket'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libTicket . "]"; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libTicket . "]"; ?></option>
										            <?php } ?>
									            <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
							            <?php if(isset($dataDbForm['pivot']['ticket'])) { ?>
								            <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['ticket'])) { ?>
                                                <input checked type="checkbox" name="pivot[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
								            <?php } else { ?>
                                                <input type="checkbox" name="pivot[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
								            <?php } ?>
							            <?php } else { ?>
                                            <input type="checkbox" name="pivot[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
							            <?php } ?>
                                    </td>
                                    <td>
							            <?php if(isset($dataDbForm['maj']['ticket'])) { ?>
								            <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['ticket'])) { ?>
                                                <input checked type="checkbox" name="maj[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
								            <?php } else { ?>
                                                <input type="checkbox" name="maj[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
								            <?php } ?>
							            <?php } else { ?>
                                            <input type="checkbox" name="maj[ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
							            <?php } ?>
                                    </td>
                                </tr>
				            <?php } ?>
				            <?php if(isset($dataDbForm['fieldAssoc']['ticket']['moreData'])) { ?>
					            <?php $compteur = 0; ?>
					            <?php foreach($dataDbForm['fieldAssoc']['ticket']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[ticket][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowTicket">
                                            <div id="selectTicket" class="select">
                                                <select name="data[ticket][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
										            <?php foreach(($dataTicket ?? []) as $field => $libTicket) { ?>
											            <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libTicket . "]"; ?></option>
											            <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libTicket . "]"; ?></option>
											            <?php } ?>
										            <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
								            <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[ticket][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
								            <?php } else { ?>
                                                <input type="checkbox" name="data[ticket][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
								            <?php } ?>
                                        </td>
                                        <td>
								            <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[ticket][moreData][<?php echo $compteur; ?>][maj]" value="true" />
								            <?php } else { ?>
                                                <input type="checkbox" name="data[ticket][moreData][<?php echo $compteur; ?>][maj]" value="true" />
								            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowTicket"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
						            <?php $compteur ++; ?>
					            <?php } ?>
				            <?php } ?>
                        </table>
                        <h3><?php printf(__('%1$s definition', "infocob-crm-forms"), $libellesTables["TICKET"] ?? ""); ?></h3>
                        <table border="0">
                            <tr>
                                <th><?php echo $dataTicket["TI_STATUS"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_MODULE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_TYPE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_LISTEDESTINATAIRE"] ?? ""; ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][status]" size="1">
                                            <option></option>
				                            <?php if(isset($ticketStatus['result'])) { ?>
					                            <?php foreach($ticketStatus['result'] as $status) { ?>
						                            <?php if(($dataDbForm['fieldAssoc']['ticket']['type_ticket']['status'] ?? "") == ($status["TS_CODE"] ?? "")) { ?>
                                                        <option selected value="<?php echo $status["TS_CODE"] ?? ""; ?>"><?php echo $status["TS_NOM"] ?? ""; ?></option>
						                            <?php } else { ?>
                                                        <option value="<?php echo $status["TS_CODE"] ?? ""; ?>"><?php echo $status["TS_NOM"] ?? ""; ?></option>
						                            <?php } ?>
					                            <?php } ?>
				                            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select id="moduleTicket" name="data[ticket][type_ticket][module]" size="1">
                                            <option></option>
						                    <?php
							                    // Types ticket | 1er niveau
							                    foreach(($ticketModules['result'] ?? []) as $module1) {
								                    if(!empty($module1["LTM_CODE"])) {
									                    if(($dataDbForm['fieldAssoc']['ticket']['type_ticket']['module'] ?? "") == $module1['LTM_CODE']): ?>
                                                            <option class="type" selected value="<?php echo $module1['LTM_CODE']; ?>"><?php echo $module1['LTM_NOM']; ?></option>
									                    <?php else: ?>
                                                            <option class="type" value="<?php echo $module1['LTM_CODE']; ?>"><?php echo $module1['LTM_NOM']; ?></option>
									                    <?php endif;
									
									                    // Sous-types ticket | 2eme niveau
									                    if(!empty($module1["SOUS_MODULES"])) {
										                    foreach($module1["SOUS_MODULES"] as $type2) {
											                    if(!empty($type2["LTM_CODE"])) {
												                    if(($dataDbForm['fieldAssoc']['ticket']['type_ticket']['module'] ?? "") == $module1['LTM_CODE'].".".$type2['LTM_CODE']): ?>
                                                                        <option class="subType" selected value="<?php echo $module1['LTM_CODE'].".".$type2['LTM_CODE']; ?>">
                                                                            --- <?php echo $type2['LTM_NOM']; ?></option>
												                    <?php else: ?>
                                                                        <option class="subType" value="<?php echo $module1['LTM_CODE'].".".$type2['LTM_CODE']; ?>">
                                                                            --- <?php echo $type2['LTM_NOM']; ?></option>
												                    <?php endif;
												
												                    // Sous-sous types ticket | 3eme niveau
												                    if(!empty($type2["SOUS_MODULES"])) {
													                    foreach($type2["SOUS_MODULES"] as $type3) {
														                    if(!empty($type3["LTM_CODE"])) {
															                    if(($dataDbForm['fieldAssoc']['ticket']['type_ticket']['module'] ?? "") == $module1['LTM_CODE'].".".$type2['LTM_CODE'].".".$type3['LTM_CODE']): ?>
                                                                                    <option class="subSubType" selected value="<?php echo $module1['LTM_CODE'].".".$type2['LTM_CODE'].".".$type3['LTM_CODE']; ?>">
                                                                                        ------ <?php echo $type3['LTM_NOM']; ?></option>
															                    <?php else: ?>
                                                                                    <option class="subSubType" value="<?php echo $module1['LTM_CODE'].".".$type2['LTM_CODE'].".".$type3['LTM_CODE']; ?>">
                                                                                        ------ <?php echo $type3['LTM_NOM']; ?></option>
															                    <?php endif;
														                    }
													                    }
												                    }
											                    }
										                    }
									                    }
								                    }
							                    }
						                    ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][type]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketTypes['result'])) { ?>
							                    <?php foreach($ticketTypes['result'] as $type) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['type'] ?? "" == $type["TICKETTYPE"]) { ?>
                                                        <option selected value="<?php echo $type["TICKETTYPE"]; ?>"><?php echo $type["TICKETTYPE"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $type["TICKETTYPE"]; ?>"><?php echo $type["TICKETTYPE"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <select name="data[ticket][type_ticket][destinataires][]" id="destinataires" multiple="multiple" size="1">
                                        <optgroup label="Utilisateurs">
						                    <?php if(isset($vendeurs['result'])) { ?>
							                    <?php foreach($vendeurs['result'] as $vendeur) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['ticket']['type_ticket']['destinataires']) && in_array($vendeur['V_CODE'], $dataDbForm['fieldAssoc']['ticket']['type_ticket']['destinataires'])) { ?>
                                                        <option selected value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $vendeur['V_CODE']; ?>"><?php echo $vendeur['V_NOM'] . " " . $vendeur['V_PRENOM']; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </optgroup>
                                        <optgroup label="Groupements">
						                    <?php if(isset($groupements['result'])) { ?>
							                    <?php foreach($groupements['result'] as $keyGroupement => $valueGroupement) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['ticket']['type_ticket']['destinataires']) && in_array($keyGroupement, $dataDbForm['fieldAssoc']['ticket']['type_ticket']['destinataires'])) { ?>
                                                        <option selected value="<?php echo $keyGroupement; ?>"><?php echo $valueGroupement['nom']; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $keyGroupement; ?>"><?php echo $valueGroupement['nom']; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </optgroup>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <table border="0">
                            <tr>
                                <th><?php echo $dataTicket["TI_CATEGORIE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_FREQUENCE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_PLATEFORME"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_PRIORITE"] ?? ""; ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][categorie]" size="1">
                                            <option></option>
				                            <?php if(isset($ticketCategories['result'])) { ?>
					                            <?php foreach($ticketCategories['result'] as $categorie) { ?>
						                            <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['categorie'] ?? "" == $categorie["TICKETCATEGORIE"]) { ?>
                                                        <option selected value="<?php echo $categorie["TICKETCATEGORIE"]; ?>"><?php echo $categorie["TICKETCATEGORIE"]; ?></option>
						                            <?php } else { ?>
                                                        <option value="<?php echo $categorie["TICKETCATEGORIE"]; ?>"><?php echo $categorie["TICKETCATEGORIE"]; ?></option>
						                            <?php } ?>
					                            <?php } ?>
				                            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][frequence]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketFrequences['result'])) { ?>
							                    <?php foreach($ticketFrequences['result'] as $frequence) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['frequence'] ?? "" == $frequence["TICKETFREQUENCE"]) { ?>
                                                        <option selected value="<?php echo $frequence["TICKETFREQUENCE"]; ?>"><?php echo $frequence["TICKETFREQUENCE"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $frequence["TICKETFREQUENCE"]; ?>"><?php echo $frequence["TICKETFREQUENCE"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][plateforme]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketPlateformes['result'])) { ?>
							                    <?php foreach($ticketPlateformes['result'] as $plateforme) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['plateforme'] ?? "" == $plateforme["TICKETPLATEFORME"]) { ?>
                                                        <option selected value="<?php echo $plateforme["TICKETPLATEFORME"]; ?>"><?php echo $plateforme["TICKETPLATEFORME"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $plateforme["TICKETPLATEFORME"]; ?>"><?php echo $plateforme["TICKETPLATEFORME"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][priorite]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketPriorites['result'])) { ?>
							                    <?php foreach($ticketPriorites['result'] as $priorite) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['priorite'] ?? "" == $priorite["TP_CODE"]) { ?>
                                                        <option selected value="<?php echo $priorite["TP_CODE"]; ?>"><?php echo $priorite["TP_NOM"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $priorite["TP_CODE"]; ?>"><?php echo $priorite["TP_NOM"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table border="0">
                            <tr>
                                <th><?php echo $dataTicket["TI_SEVERITE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_SOURCE"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_VERSION"] ?? ""; ?></th>
                                <th><?php echo $dataTicket["TI_DATEALERT"] ?? ""; ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][severite]" size="1">
                                            <option></option>
				                            <?php if(isset($ticketSeverites['result'])) { ?>
					                            <?php foreach($ticketSeverites['result'] as $severite) { ?>
						                            <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['severite'] ?? "" == $severite["TSE_CODE"]) { ?>
                                                        <option selected value="<?php echo $severite["TSE_CODE"]; ?>"><?php echo $severite["TSE_NOM"]; ?></option>
						                            <?php } else { ?>
                                                        <option value="<?php echo $severite["TSE_CODE"]; ?>"><?php echo $severite["TSE_NOM"]; ?></option>
						                            <?php } ?>
					                            <?php } ?>
				                            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][source]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketSources['result'])) { ?>
							                    <?php foreach($ticketSources['result'] as $source) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['source'] ?? "" == $source["TICKETSOURCE"]) { ?>
                                                        <option selected value="<?php echo $source["TICKETSOURCE"]; ?>"><?php echo $source["TICKETSOURCE"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $source["TICKETSOURCE"]; ?>"><?php echo $source["TICKETSOURCE"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[ticket][type_ticket][version]" size="1">
                                            <option></option>
						                    <?php if(isset($ticketVersions['result'])) { ?>
							                    <?php foreach($ticketVersions['result'] as $version) { ?>
								                    <?php if($dataDbForm['fieldAssoc']['ticket']['type_ticket']['version'] ?? "" == $version["TICKETVERSION"]) { ?>
                                                        <option selected value="<?php echo $version["TICKETVERSION"]; ?>"><?php echo $version["TICKETVERSION"]; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $version["TICKETVERSION"]; ?>"><?php echo $version["TICKETVERSION"]; ?></option>
								                    <?php } ?>
							                    <?php } ?>
						                    <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td class="center-text">
                                    <input type="checkbox" name="data[ticket][alarme]" id="alarme" value="1" <?php echo (isset($dataDbForm['fieldAssoc']['ticket']['alarme']) && ($dataDbForm['fieldAssoc']['ticket']['alarme'])) ? "checked" : ""; ?> />
                                </td>
                            </tr>
                        </table>
	
	                    <?php if(!empty($shortcodesFichiersLies)) { ?>
                            <h3><?php _e("Links files", "infocob-crm-forms"); ?></h3>
                            <table id="tableTicketFichiersLies" border="0" style="text-align: center;">
                                <tr>
                                    <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Enable Infocob link", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Cloud file", "infocob-crm-forms"); ?></th>
                                </tr>
			                    <?php foreach($shortcodesFichiersLies as $key => $name_shortcode) { ?>
                                    <tr>
                                        <td><?php echo $name_shortcode ?></td>
                                        <td>
                                            <?php if(isset($dataDbForm['fichiersLies']['ticket']) && in_array($name_shortcode, $dataDbForm['fichiersLies']['ticket'])) { ?>
                                                <input checked type="checkbox" name="fichiersLies[ticket][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="fichiersLies[ticket][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($dataDbForm['cloudFichiers']['ticket']) && in_array($name_shortcode, $dataDbForm['cloudFichiers']['ticket'])) { ?>
                                                <input checked type="checkbox" name="cloudFichiers[ticket][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="cloudFichiers[ticket][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
	                    <?php } ?>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireTicket" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_ticket" class="inventaire_enable" name="tables_inventaire_ticket" <?php echo ($dataDbForm['tables']['inventaire-ticket'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_ticket'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireTicket" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireTicket" class="select">
                                            <select name="data[inventaire-ticket][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-ticket'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-ticket'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-ticket'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-ticket'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-ticket'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-ticket'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-ticket][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-ticket']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-ticket']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireTicket">
                                            <div id="selectInventaireTicket" class="select">
                                                <select name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-ticket][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireTicket"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsTicket) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsTicket ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
    
                <!------------------------------------------->
                <!-- DATA CONTRAT -->
                <!------------------------------------------->
    
                <div style="display: none;" id="data-contrat" class="data-contrat container-table">
                    <h3><?php _e("Contrat configuration", "infocob-crm-forms"); ?> <span id="addRowContrat" class="spanClick module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span>
                    </h3>
		
		            <?php if(isset($dataDbForm['tables'])) { ?>
			            <?php if(isset($dataDbForm['tables']['contrat']) && $dataDbForm['tables']['contrat'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contrat_oui" name='tables_contrat' value='true' checked />
                            <label for='tables_contrat_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_contrat_non" name='tables_contrat' value='false' />
                            <label for='tables_contrat_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
			            <?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contrat_oui" name='tables_contrat' value='true' />
                            <label for='tables_contrat_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_contrat_non" name='tables_contrat' value='false' checked />
                            <label for='tables_contrat_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
			            <?php } ?>
		            <?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_contrat_oui" name='tables_contrat' value='true' />
                        <label for='tables_contrat_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_contrat_non" name='tables_contrat' value='false' checked />
                        <label for='tables_contrat_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
		            <?php } ?>
        
                    <div class="module-table hide">
                        <table id="tableContrat" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
				            <?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo $name_shortcode; ?></td>
                                    <td>
                                        <div id="selectContrat" class="select">
                                            <select name="data[contrat][<?php echo $name_shortcode; ?>]" size="1">
                                                <option></option>
									            <?php foreach(($dataContrat ?? []) as $field => $libContrat) { ?>
										            <?php if(isset($dataDbForm['fieldAssoc']['contrat'][ $name_shortcode ]) && $dataDbForm['fieldAssoc']['contrat'][ $name_shortcode ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libContrat . "]"; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libContrat . "]"; ?></option>
										            <?php } ?>
									            <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
							            <?php if(isset($dataDbForm['pivot']['contrat'])) { ?>
								            <?php if(in_array($name_shortcode, $dataDbForm['pivot']['contrat'])) { ?>
                                                <input checked type="checkbox" name="pivot[contrat][]" value="<?php echo $name_shortcode; ?>" />
								            <?php } else { ?>
                                                <input type="checkbox" name="pivot[contrat][]" value="<?php echo $name_shortcode; ?>" />
								            <?php } ?>
							            <?php } else { ?>
                                            <input type="checkbox" name="pivot[contrat][]" value="<?php echo $name_shortcode; ?>" />
							            <?php } ?>
                                    </td>
                                    <td>
							            <?php if(isset($dataDbForm['maj']['contrat'])) { ?>
								            <?php if(in_array($name_shortcode, $dataDbForm['maj']['contrat'])) { ?>
                                                <input checked type="checkbox" name="maj[contrat][]" value="<?php echo $name_shortcode; ?>" />
								            <?php } else { ?>
                                                <input type="checkbox" name="maj[contrat][]" value="<?php echo $name_shortcode; ?>" />
								            <?php } ?>
							            <?php } else { ?>
                                            <input type="checkbox" name="maj[contrat][]" value="<?php echo $name_shortcode; ?>" />
							            <?php } ?>
                                    </td>
                                </tr>
				            <?php } ?>
				            <?php if(isset($dataDbForm['fieldAssoc']['contrat']['moreData'])) { ?>
					            <?php $compteur = 0; ?>
					            <?php foreach($dataDbForm['fieldAssoc']['contrat']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[contrat][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowContrat">
                                            <div id="selectContrat" class="select">
                                                <select name="data[contrat][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
										            <?php foreach(($dataContrat ?? []) as $field => $libContrat) { ?>
											            <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libContrat . "]"; ?></option>
											            <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libContrat . "]"; ?></option>
											            <?php } ?>
										            <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
								            <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[contrat][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
								            <?php } else { ?>
                                                <input type="checkbox" name="data[contrat][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
								            <?php } ?>
                                        </td>
                                        <td>
								            <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[contrat][moreData][<?php echo $compteur; ?>][maj]" value="true" />
								            <?php } else { ?>
                                                <input type="checkbox" name="data[contrat][moreData][<?php echo $compteur; ?>][maj]" value="true" />
								            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowContrat"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
						            <?php $compteur ++; ?>
					            <?php } ?>
				            <?php } ?>
                        </table>
                        <h3><?php printf(__('%1$s definition', "infocob-crm-forms"), $libellesTables["CONTRAT"] ?? ""); ?></h3>
                        <table border="0">
                            <tr>
                                <th><?php echo $dataContrat["CT_ETATCONTRAT"] ?? ""; ?></th>
                                <th><?php echo $dataContrat["CT_TYPE"] ?? ""; ?></th>
                                <th><?php echo $dataContrat["CT_PERIODICITE"] ?? ""; ?></th>
                                <th><?php echo $dataContrat["CT_FACTURATION"] ?? ""; ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select name="data[contrat][type_contrat][etat]" size="1">
                                            <option></option>
								            <?php if(isset($contratEtats['result'])) { ?>
									            <?php foreach($contratEtats['result'] as $etat) { ?>
										            <?php if($dataDbForm['fieldAssoc']['contrat']['type_contrat']['etat'] ?? "" == $etat["LCE_CODE"]) { ?>
                                                        <option selected value="<?php echo $etat["LCE_CODE"]; ?>"><?php echo $etat["LCE_NOM"]; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $etat["LCE_CODE"]; ?>"><?php echo $etat["LCE_NOM"]; ?></option>
										            <?php } ?>
									            <?php } ?>
								            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[contrat][type_contrat][type]" size="1">
                                            <option></option>
								            <?php if(isset($contratTypes['result'])) { ?>
									            <?php foreach($contratTypes['result'] as $type) { ?>
										            <?php if($dataDbForm['fieldAssoc']['contrat']['type_contrat']['type'] ?? "" == $type["CONTRATTYPE"]) { ?>
                                                        <option selected value="<?php echo $type["CONTRATTYPE"]; ?>"><?php echo $type["CONTRATTYPE"]; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $type["CONTRATTYPE"]; ?>"><?php echo $type["CONTRATTYPE"]; ?></option>
										            <?php } ?>
									            <?php } ?>
								            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[contrat][type_contrat][periodicite]" size="1">
                                            <option></option>
								            <?php if(isset($contratPeriodicites['result'])) { ?>
									            <?php foreach($contratPeriodicites['result'] as $periodicite) { ?>
										            <?php if($dataDbForm['fieldAssoc']['contrat']['type_contrat']['periodicite'] ?? "" == $periodicite["LCP_CODE"]) { ?>
                                                        <option selected value="<?php echo $periodicite["LCP_CODE"]; ?>"><?php echo $periodicite["LCP_NOM"]; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $periodicite["LCP_CODE"]; ?>"><?php echo $periodicite["LCP_NOM"]; ?></option>
										            <?php } ?>
									            <?php } ?>
								            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="select">
                                        <select name="data[contrat][type_contrat][facturation]" size="1">
                                            <option></option>
								            <?php if(isset($contratFacturations['result'])) { ?>
									            <?php foreach($contratFacturations['result'] as $facturation) { ?>
										            <?php if($dataDbForm['fieldAssoc']['contrat']['type_contrat']['facturation'] ?? "" == $facturation["LCP_CODE"]) { ?>
                                                        <option selected value="<?php echo $facturation["LCP_CODE"]; ?>"><?php echo $facturation["LCP_NOM"]; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $facturation["LCP_CODE"]; ?>"><?php echo $facturation["LCP_NOM"]; ?></option>
										            <?php } ?>
									            <?php } ?>
								            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table border="0">
                            <tr>
                                <th><?php echo $dataContrat["CT_MODERECONDUCTION"] ?? ""; ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>
                                    <div class="select">
                                        <select name="data[contrat][type_contrat][mode]" size="1">
                                            <option></option>
								            <?php if(isset($contratModesReconduction['result'])) { ?>
									            <?php foreach($contratModesReconduction['result'] as $mode) { ?>
										            <?php if($dataDbForm['fieldAssoc']['contrat']['type_contrat']['mode'] ?? "" == $mode["LCR_CODE"]) { ?>
                                                        <option selected value="<?php echo $mode["LCR_CODE"]; ?>"><?php echo $mode["LCR_NOM"]; ?></option>
										            <?php } else { ?>
                                                        <option value="<?php echo $mode["LCR_CODE"]; ?>"><?php echo $mode["LCR_NOM"]; ?></option>
										            <?php } ?>
									            <?php } ?>
								            <?php } ?>
                                        </select>
                                        <div class="select__arrow"></div>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
	
	                    <?php if(!empty($shortcodesFichiersLies)) { ?>
                            <h3><?php _e("Links files", "infocob-crm-forms"); ?></h3>
                            <table id="tableContratFichiersLies" border="0" style="text-align: center;">
                                <tr>
                                    <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Enable Infocob link", "infocob-crm-forms"); ?></th>
                                    <th><?php _e("Cloud file", "infocob-crm-forms"); ?></th>
                                </tr>
			                    <?php foreach($shortcodesFichiersLies as $key => $name_shortcode) { ?>
                                    <tr>
                                        <td><?php echo $name_shortcode ?></td>
                                        <td>
                                            <?php if(isset($dataDbForm['fichiersLies']['contrat']) && in_array($name_shortcode, $dataDbForm['fichiersLies']['contrat'])) { ?>
                                                <input checked type="checkbox" name="fichiersLies[contrat][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="fichiersLies[contrat][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($dataDbForm['cloudFichiers']['contrat']) && in_array($name_shortcode, $dataDbForm['cloudFichiers']['contrat'])) { ?>
                                                <input checked type="checkbox" name="cloudFichiers[contrat][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } else { ?>
                                                <input type="checkbox" name="cloudFichiers[contrat][]" value="<?php echo $name_shortcode; ?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
	                    <?php } ?>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireContrat" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_contrat" class="inventaire_enable" name="tables_inventaire_contrat" <?php echo ($dataDbForm['tables']['inventaire-contrat'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_contrat'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireContrat" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireContrat" class="select">
                                            <select name="data[inventaire-contrat][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-contrat'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-contrat'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-contrat'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-contrat'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-contrat'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-contrat'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-contrat][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-contrat']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-contrat']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireContrat">
                                            <div id="selectInventaireContrat" class="select">
                                                <select name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-contrat][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireContrat"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsContrat) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsContrat ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
                
                <!------------------------------------------->
                <!-- DATA HISTORIQUE -->
                <!------------------------------------------->
                
                <div style="display: none;" id="data-historique" class="data-historique container-table">
                    <h3><?php _e("Historique configuration", "infocob-crm-forms"); ?> <span id="addRowHistorique" class="spanClick hide module-table"><?php _e("Add row", "infocob-crm-forms"); ?></span>
                    </h3>
					
					<?php if(isset($dataDbForm['tables']['historique'])) { ?>
						<?php if($dataDbForm['tables']['historique'] == "true") { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_historique_oui" name='tables_historique' value='true' checked />
                            <label for='tables_historique_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_historique_non" name='tables_historique' value='false' />
                            <label for='tables_historique_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } else { ?>
                            <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_historique_oui" name='tables_historique' value='true' />
                            <label for='tables_historique_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                            <input type='radio' class="inputGetTable" id="tables_historique_non" name='tables_historique' value='false' checked />
                            <label for='tables_historique_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
						<?php } ?>
					<?php } else { ?>
                        <input style="margin-left: 10px;" class="inputGetTable" type='radio' id="tables_historique_oui" name='tables_historique' value='true' />
                        <label for='tables_historique_oui'><?php _e("Yes", "infocob-crm-forms"); ?></label>
                        <input type='radio' class="inputGetTable" id="tables_historique_non" name='tables_historique' value='false' checked />
                        <label for='tables_historique_non'><?php _e("No", "infocob-crm-forms"); ?></label><br /><br />
					<?php } ?>
                    
                    <div class="module-table hide">
                        <table id="tableHistorique" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
							<?php foreach($shortcodes as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div class="select" id="selectHistorique">
                                            <select name="data[historique][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
												<?php foreach(($dataHistorique ?? []) as $field => $libHistorique) { ?>
													<?php if(isset($dataDbForm['fieldAssoc']['historique'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['historique'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libHistorique . "]"; ?></option>
													<?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libHistorique . "]"; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['pivot']['historique'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['historique'])) { ?>
                                                <input checked type="checkbox" name="pivot[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="pivot[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="pivot[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                    <td>
										<?php if(isset($dataDbForm['maj']['historique'])) { ?>
											<?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['historique'])) { ?>
                                                <input checked type="checkbox" name="maj[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } else { ?>
                                                <input type="checkbox" name="maj[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
											<?php } ?>
										<?php } else { ?>
                                            <input type="checkbox" name="maj[historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
										<?php } ?>
                                    </td>
                                </tr>
							<?php } ?>
							<?php if(isset($dataDbForm['fieldAssoc']['historique']['moreData'])) { ?>
								<?php $compteur = 0; ?>
								<?php foreach($dataDbForm['fieldAssoc']['historique']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[historique][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowHistorique">
                                            <div id="selectHistorique" class="select">
                                                <select name="data[historique][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
													<?php foreach(($dataHistorique ?? []) as $field => $libHistorique) { ?>
														<?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libHistorique . "]"; ?></option>
														<?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libHistorique . "]"; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
											<?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[historique][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[historique][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
											<?php } ?>
                                        </td>
                                        <td>
											<?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[historique][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } else { ?>
                                                <input type="checkbox" name="data[historique][moreData][<?php echo $compteur; ?>][maj]" value="true" />
											<?php } ?>
                                        </td>
                                        <td><span class="spanClick deleteRowHistorique"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
									<?php $compteur ++; ?>
								<?php } ?>
							<?php } ?>
                        </table>
    
                        <!--
                            Inventaire
                        -->
                        <h3><?php _e("Inventory", "infocob-crm-forms"); ?> <span id="addRowInventaireHistorique" class="spanClick"><?php _e("Add row", "infocob-crm-forms"); ?></span></h3>
    
                        <input style="margin-left: 10px;" type="checkbox" id="tables_inventaire_historique" class="inventaire_enable" name="tables_inventaire_historique" <?php echo ($dataDbForm['tables']['inventaire-historique'] ?? false) ? "checked" : ""; ?>>
                        <label for='tables_inventaire_historique'><?php _e("Enable", "infocob-crm-forms"); ?></label><br /><br />
    
                        <table id="tableInventaireHistorique" class="inventaire" border="0" style="text-align: center;">
                            <tr>
                                <th><?php _e("Form fields", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Fields assignment", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Pivot", "infocob-crm-forms"); ?></th>
                                <th><?php _e("Update", "infocob-crm-forms"); ?></th>
                            </tr>
		                    <?php foreach(($shortcodes ?? []) as $key => $name_shortcode) { ?>
                                <tr>
                                    <td><?php echo Tools::explodeShortName($name_shortcode); ?></td>
                                    <td>
                                        <div id="selectInventaireHistorique" class="select">
                                            <select name="data[inventaire-historique][<?php echo Tools::getShortCodeName($name_shortcode); ?>]" size="1">
                                                <option></option>
							                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
								                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-historique'][ Tools::getShortCodeName($name_shortcode) ]) && $dataDbForm['fieldAssoc']['inventaire-historique'][ Tools::getShortCodeName($name_shortcode) ] == $field) { ?>
                                                        <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } else { ?>
                                                        <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
								                    <?php } ?>
							                    <?php } ?>
                                            </select>
                                            <div class="select__arrow"></div>
                                        </div>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['pivot']['inventaire-historique'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['pivot']['inventaire-historique'])) { ?>
                                                <input checked type="checkbox" name="pivot[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="pivot[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="pivot[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                    <td>
					                    <?php if(isset($dataDbForm['maj']['inventaire-historique'])) { ?>
						                    <?php if(in_array(Tools::getShortCodeName($name_shortcode), $dataDbForm['maj']['inventaire-historique'])) { ?>
                                                <input checked type="checkbox" name="maj[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="maj[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
						                    <?php } ?>
					                    <?php } else { ?>
                                            <input type="checkbox" name="maj[inventaire-historique][]" value="<?php echo Tools::getShortCodeName($name_shortcode); ?>" />
					                    <?php } ?>
                                    </td>
                                </tr>
		                    <?php } ?>
		                    <?php if(isset($dataDbForm['fieldAssoc']['inventaire-istorique']['moreData'])) { ?>
			                    <?php $compteur = 0; ?>
			                    <?php foreach($dataDbForm['fieldAssoc']['inventaire-historique']['moreData'] as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" value="<?php echo $value['value']; ?>" name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][value]" />
                                        </td>
                                        <td class="addRowInventaireHistorique">
                                            <div id="selectInventaireHistorique" class="select">
                                                <select name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][champ]" size="1">
                                                    <option></option>
								                    <?php foreach(($dataInventaires ?? []) as $field => $libInventaire) { ?>
									                    <?php if($value['champ'] == $field) { ?>
                                                            <option selected value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } else { ?>
                                                            <option value="<?php echo $field; ?>"><?php echo $field . " [" . $libInventaire . "]"; ?></option>
									                    <?php } ?>
								                    <?php } ?>
                                                </select>
                                                <div class="select__arrow"></div>
                                            </div>
                                        </td>
                                        <td>
						                    <?php if(isset($value['pivot'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][pivot]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
						                    <?php if(isset($value['maj'])) { ?>
                                                <input checked type="checkbox" name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } else { ?>
                                                <input type="checkbox" name="data[inventaire-historique][moreData][<?php echo $compteur; ?>][maj]" value="true" />
						                    <?php } ?>
                                        </td>
                                        <td>
                                            <span class="spanClick deleteRowInventaireHistorique"><?php _e("Delete row", "infocob-crm-forms"); ?></span>
                                        </td>
                                    </tr>
				                    <?php $compteur ++; ?>
			                    <?php } ?>
		                    <?php } ?>
                        </table>
	
	                    <?php if(!empty($requireFieldsHistorique) || !empty($requireFieldsInventaires)) { ?>
                            <h3><?php _e("Require fields", "infocob-crm-forms"); ?></h3>
		                    <?php foreach(($requireFieldsHistorique ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
		
		                    <?php foreach(($requireFieldsInventaires ?? []) as $field => $values) { ?>
                                <table>
                                    <td><?php echo $values["DI_DISPLAYLABEL"]; ?></td>
                                    <td><b><?php echo $values["DI_CHAMP"]; ?></b></td>
                                </table>
		                    <?php } ?>
	                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-plugin">
		<?php if($dataDbForm['sendmail'] ?? false || !isset($dataDbForm['sendmail'])) { ?>
            <input style="margin-left: 10px;" type='radio' id='sendMailAlways' name='sendmail' value='true' checked />
            <label for='sendMailAlways'><?php _e("Send email", "infocob-crm-forms"); ?></label>
            
            <input type='radio' id='sendMailNever' name='sendmail' value='false' />
            <label for='sendMailNever'><?php _e("Don't send email", "infocob-crm-forms"); ?></label><br />
		<?php } else { ?>
            <input style="margin-left: 10px;" type='radio' id='sendMailAlways' name='sendmail' value='true' />
            <label for='sendMailAlways'><?php _e("Send email", "infocob-crm-forms"); ?></label>
            
            <input type='radio' id='sendMailNever' name='sendmail' value='false' checked />
            <label for='sendMailNever'><?php _e("Don't send email", "infocob-crm-forms"); ?></label><br />
		<?php } ?>
        
        <input class="inputPlugin" type="submit" value="<?php _e("Save form", "infocob-crm-forms"); ?>" />
        <input class="inputPlugin" data-type="cf7" id="deleteData" type="button" value="<?php _e("Delete form", "infocob-crm-forms"); ?>" />
    </div>
</form>
