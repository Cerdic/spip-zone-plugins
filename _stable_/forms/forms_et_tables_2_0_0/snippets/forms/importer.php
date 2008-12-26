<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

function snippets_forms_importer($id_form_target,$formtree,$contexte=array()){
	include_spip('inc/forms');
	include_spip('base/forms');
	include_spip('base/abstract_sql');
	include_spip('inc/forms_edit');
	$contenu = "";
	if (isset($formtree['forms']))
		foreach($formtree['forms'] as $forms){
			foreach($forms['form'] as $form){
				// si c'est une creation, creer le formulaire avec les infos d'entete
				if (!($id_form=intval($id_form_target))){
					$ins = array();
					foreach (array_keys($GLOBALS['tables_principales']['spip_forms']['field']) as $key)
						if (!in_array($key,array('id_form','maj')) AND isset($form[$key])){
							$ins[$key] = trim(applatit_arbre($form[$key]));
						}
					$id_form = sql_insertq('spip_forms',$ins);
				}
				if ($id_form AND isset($form['fields'])){
					foreach($form['fields'] as $fields)
							foreach($fields['field'] as $field){
								$champ = trim(applatit_arbre($field['champ']));
								$type = trim(applatit_arbre($field['type']));
								$titre = trim(applatit_arbre($field['titre']));
								$champ = forms_insere_nouveau_champ($id_form,$type,$titre,($id_form==$id_form_target)?"":$champ);
								$set = array();
								foreach (array_keys($GLOBALS['tables_principales']['spip_forms_champs']['field']) as $key)
									if (!in_array($key,array('id_form','champ','rang','titre','type')) AND isset($field[$key])){
										$set[$key]=trim(applatit_arbre($field[$key]));
									}
								if (count($set))
									$res = sql_updateq("spip_forms_champs",$set,"id_form=".intval($id_form)." AND champ=".sql_quote($champ));
								if(isset($field['les_choix'])&&is_array($field['les_choix']))
									foreach($field['les_choix'] as $les_choix)
										foreach($les_choix['un_choix'] as $un_choix){
											$titre = trim(applatit_arbre($un_choix['titre']));
											$choix = forms_insere_nouveau_choix($id_form,$champ,$titre);
											if (isset($un_choix['rang'])){
												$rang = trim(applatit_arbre($un_choix['rang']));
												sql_updateq("spip_forms_champs_choix",array("rang"=>$rang),"id_form=".intval($id_form)." AND champ=".sql_quote($champ)." AND choix=".sql_quote($choix));
											}
										}
							}
				}
			}
		}
	return $id_form;
}

?>