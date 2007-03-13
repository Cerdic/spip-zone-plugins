<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2007 - Distribue sous licence GNU/GPL
 *
 */

// creation d'une table a partir de sa structure xml
// le type est surchargŽ par $type
// $unique : ne pas creer la table si une du meme type existe deja
function Forms_creer_table($structure_xml,$type=NULL, $unique = true, $c=NULL){
	include_spip('inc/xml');

	$xml = spip_xml_load($structure_xml);
	foreach($xml as $k1=>$forms)
		foreach($forms as $k2=>$formscont)
			foreach($formscont as $k3=>$form)
				foreach($form as $k4=>$formcont)
					foreach($formcont as $prop=>$datas)
					if ($prop=='type_form'){
						if ($type)
							$xml[$k1][$k2][$k3][$k4][$prop] = array($type);
						else 
							$type = trim(applatit_arbre($datas));
					}

	if (!$type) return;
	if ($unique){
		$res = spip_query("SELECT id_form FROM spip_forms WHERE type_form="._q($type));
		if (spip_num_rows($res))
			return;
	}
	// ok on peut creer la table
	$snippets_forms_importer = charger_fonction('importer','snippets/forms');
	$id_form = $snippets_forms_importer(0,$xml);
	if ($c!==NULL){
		include_spip('forms_crayons');
		form_revision($id_form,$c);
	}
	return $id_form;
}

function Forms_liste_tables($type){
	static $liste = array();
	if (!isset($liste[$type])){
		$liste[$type] = array();
		$res = spip_query("SELECT id_form FROM spip_forms WHERE type_form="._q($type));
		while ($row = spip_fetch_array($res)){
			$liste[$type][] = $row['id_form'];
		}
	}
	return $liste[$type];
}

function Forms_supprimer_tables($type_ou_id){
	if (!$id_form = intval($type_ou_id) OR !is_numeric($type_ou_id)){
		$liste = Forms_liste_tables($type_ou_id);
		foreach($liste as $id)
			Forms_supprimer_tables($id);
		return;
	}
	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE id_form="._q($id_form));
	while ($row = spip_fetch_array($res)){
		spip_query("DELETE FROM spip_forms_donnees_champs WHERE id_donnee="._q($row['id_donnee']));
	}
	spip_query("DELETE FROM spip_forms_donnees WHERE id_form="._q($id_form));
	spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form="._q($id_form));
	spip_query("DELETE FROM spip_forms_champs WHERE id_form="._q($id_form));
	spip_query("DELETE FROM spip_forms WHERE id_form="._q($id_form));
	spip_query("DELETE FROM spip_forms_articles WHERE id_form="._q($id_form));
}

include_spip('forms_fonctions');
function Forms_les_valeurs($id_form, $id_donnee, $champ, $separateur=",",$etoile=false, $traduit=true){
	return forms_calcule_les_valeurs('forms_donnees_champs', $id_donnee, $champ, $id_form, $separateur,$etoile,$traduit);
}
function Forms_creer_champ($id_form,$type,$titre,$c=NULL,$champ=""){
	include_spip('inc/forms_edit');
	$champ = Forms_insere_nouveau_champ($id_form,$type,$titre,$champ);
	if ($c!==NULL){
		include_spip('forms_crayons');
		forms_champ_revision("$id_form-$champ",$c);
	}
	return $champ;
}

function Forms_decrit_donnee($id_donnee,$specifiant=true,$linkable=false){
	list($id_form,$titreform,$type_form,$t) = Forms_liste_decrit_donnee($id_donnee,$specifiant,$linkable);
	if (!count($t) && $specifiant)
		list($id_form,$titreform,$type_form,$t) = Forms_liste_decrit_donnee($id_donnee, false,$linkable);
	return $t;
}
function Forms_creer_donnee($id_form,$c = NULL){
	include_spip('inc/autoriser');
	if (!autoriser('creer','donnee',0,NULL,array('id_form'=>$id_form)))
		return array(0,_L("droits insuffisants pour creer une donnee dans table $id_form"));
	include_spip('inc/forms');
	$new = 0;
	$erreur = array();
	Forms_enregistrer_reponse_formulaire($id_form, $new, $erreur, $reponse, '', '' , $c);
	return array($new,$erreur);
}
function Forms_supprimer_donnee($id_form,$id_donnee){
	include_spip('inc/autoriser');
	if (!autoriser('supprimer','donnee',$id_donnee,NULL,array('id_form'=>$id_form)))
		return _L("droits insuffisants pour supprimer la donnee $id_donnee");
	spip_query("UPDATE spip_forms_donnees SET statut='poubelle' WHERE id_donnee="._q($id_donnee));
	return true;
}
/*function Forms_modifier_donnee($id_form,$id_donnee,$c = NULL){
	include_spip('inc/forms');
	$c = array('ligne_1'=>_L("Nouvelle ligne"),"select_1"=>$niveau);
	$new = 0;
	$erreur = array();
	Forms_enregistrer_reponse_formulaire($id_form, $new, $erreur, $reponse, '', '' , $c);
	return array($new,$erreur);
}*/

?>