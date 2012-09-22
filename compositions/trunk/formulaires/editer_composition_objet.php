<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/compositions');
/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_composition_objet_charger($type,$id){
	$valeurs = array();
	$table_objet_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	$valeurs[$id_table_objet] = intval($id);

	$valeurs['editable'] = true;
	$valeurs['id'] = "$type-$id";
	$valeurs['id_objet'] = $id;
	$valeurs['objet'] = $type;

	$row = sql_fetsel('composition,composition_lock',$table_objet_sql,"$id_table_objet=".intval($id));
	$valeurs['composition'] = $row['composition'];
	$valeurs['composition_lock'] = $row['composition_lock'];
	
	if ($type=='rubrique')
		$valeurs['composition_branche_lock'] = sql_getfetsel('composition_branche_lock',$table_objet_sql,"$id_table_objet=".intval($id));
	
	$valeurs['composition_heritee'] = compositions_heriter($type, $id);
	$valeurs['verrou_branche'] = compositions_verrou_branche($type, $id);
	$valeurs['verrou_branche'] = false;
	$valeurs['composition_verrouillee'] = compositions_verrouiller($type, $id);

	$valeurs['compositions'] = compositions_lister_disponibles($type);
	$valeurs['_compositions'] = reset($valeurs['compositions']); // on ne regarde qu'un seul type
	if (is_array($valeurs['_compositions']) AND !isset($valeurs['_compositions'][''])){
		$valeurs['_compositions'] = array_merge(
			array(''=>array('nom'=>_T('compositions:composition_defaut'),'description'=>'','icon'=>'','configuration'=>'')),
			$valeurs['_compositions']
		);
	}
	
	// Si on herite d'une composition
	// On modifie le tableau des compositions
	if ($valeurs['composition_heritee'] AND $valeurs['composition_heritee'] != '-') {
		$compo_defaut = $valeurs['_compositions'][$valeurs['composition_heritee']];
		$compo_vide = $valeurs['_compositions'][''];
		unset($valeurs['_compositions'][$valeurs['composition_heritee']]);
		unset($valeurs['_compositions']['']);
		$valeurs['_compositions'] = array_merge(
			array('' => $compo_defaut,'-' => $compo_vide),
			$valeurs['_compositions']
		);
	}
	
	$valeurs['_hidden'] = "<input type='hidden' name='$id_table_objet' value='$id' />";

	if (!autoriser('styliser',$type,$id))
		$valeurs['editable'] = false;

	return $valeurs;
}

/**
 * Traitement
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_composition_objet_traiter($type,$id){
	$valeurs = array();
	$table_objet_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	$update = array();

	if (!is_null($p = _request('composition')))
		$update['composition'] = $p;

	if (autoriser('webmestre'))
		$update['composition_lock'] = _request('composition_lock')?1:0;
		
	if (autoriser('webmestre') AND $type == 'rubrique')
		$update['composition_branche_lock'] = _request('composition_branche_lock')?1:0;

	sql_updateq($table_objet_sql,$update,"$id_table_objet=".intval($id));

	// mettre a jour la liste des types de compo en cache
	compositions_cacher();
	return array('message_ok'=>'','editable'=>true);
}