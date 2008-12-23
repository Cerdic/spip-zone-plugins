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
					$names = array();
					$values = array();
					foreach (array_keys($GLOBALS['tables_principales']['spip_forms']['field']) as $key)
						if (!in_array($key,array('id_form','maj')) AND isset($form[$key])){
							$names[] = $key;
							$values[] = _q(trim(applatit_arbre($form[$key])));
						}
					spip_abstract_insert('spip_forms',"(".implode(",",$names).")","(".implode(",",$values).")");
					$id_form = spip_insert_id();
				}
				if ($id_form AND isset($form['fields'])){
					foreach($form['fields'] as $fields)
							foreach($fields['field'] as $field){
								$champ = trim(applatit_arbre($field['champ']));
								$type = trim(applatit_arbre($field['type']));
								$titre = trim(applatit_arbre($field['titre']));
								$champ = Forms_insere_nouveau_champ($id_form,$type,$titre,($id_form==$id_form_target)?"":$champ);
								$set = "";
								foreach (array_keys($GLOBALS['tables_principales']['spip_forms_champs']['field']) as $key)
									if (!in_array($key,array('id_form','champ','rang','titre','type')) AND isset($field[$key])){
										$set .= "$key="._q(trim(applatit_arbre($field[$key]))).", ";
									}
								if (strlen($set)){
									$set = substr($set,0,strlen($set)-2);
									$res = spip_query("UPDATE spip_forms_champs SET $set WHERE id_form="._q($id_form)." AND champ="._q($champ));
								}
								if(isset($field['les_choix'])&&is_array($field['les_choix']))
									foreach($field['les_choix'] as $les_choix)
										foreach($les_choix['un_choix'] as $un_choix){
											$titre = trim(applatit_arbre($un_choix['titre']));
											$choix = Forms_insere_nouveau_choix($id_form,$champ,$titre);
											if (isset($un_choix['rang'])){
												$rang = trim(applatit_arbre($un_choix['rang']));
												spip_query("UPDATE spip_forms_champs_choix SET rang="._q($rang)." WHERE id_form="._q($id_form)." AND champ="._q($champ)." AND choix="._q($choix));
											}
										}
							}
				}
			}
		}
	return $id_form;
}

?>