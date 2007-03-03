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
function Forms_creer_table($structure_xml,$type=NULL, $unique = true){
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
	$importer = charger_fonction('importer','snippets/forms');
	snippets_forms_importer(0,$xml);
	return;
}

function Forms_liste_tables($type){
	$liste = array();
	$res = spip_query("SELECT id_form FROM spip_forms WHERE type_form="._q($type));
	while ($row = spip_fetch_array($res)){
		$liste[] = $row['id_form'];
	}
	return $liste;
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
function Forms_les_valeurs($id_form, $id_donnee, $champ, $separateur=",",$etoile=false){
	return forms_calcule_les_valeurs('forms_donnees_champs', $id_donnee, $champ, $id_form, $separateur,$etoile);
}
function Forms_decrit_donnee($id_donnee,$specifiant=true,$linkable=false){
	list($id_form,$titreform,$type_form,$t) = Forms_liste_decrit_donnee($id_donnee,$specifiant,$linkable);
	if (!count($t) && $specifiant)
		list($id_form,$titreform,$type_form,$t) = Forms_liste_decrit_donnee($id_donnee, false,$linkable);
	return $t;
}

?>