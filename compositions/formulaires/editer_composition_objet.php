<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */


include_spip('inc/compositions');
/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_composition_objet_charger($type,$id,$hide_form=false){
	$valeurs = array();
	$table_objet_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	$valeurs[$id_table_objet] = intval($id);

	$valeurs['editable'] = true;
	$valeurs['id'] = "$type-$id";
	$valeurs['id_objet'] = $id;
	$valeurs['objet'] = $type;
	$valeurs['_hide'] = (($hide_form AND is_null(_request('composition')))?' ':'');

	$row = sql_fetsel('composition,composition_lock',$table_objet_sql,"$id_table_objet=".intval($id));
	$valeurs['composition'] = $row['composition'];
	$valeurs['composition_lock'] = $row['composition_lock'];

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_objet_sql);
	if (isset($desc['field']['id_rubrique'])) {
		$_id_rubrique = ($type == 'rubrique') ? 'id_parent' : 'id_rubrique';
		$id_rubrique = sql_getfetsel($_id_rubrique,$table_objet_sql,"$id_table_objet=".intval($id),'','','','',$serveur);
		$valeurs['composition_defaut'] = compositions_heriter($type, $id_rubrique);
	} else
		$valeurs['composition_defaut'] = '';
	$valeurs['composition_verrouillee'] = compositions_verrouiller($type, $id);

	$valeurs['compositions'] = compositions_lister_disponibles($type);
	$valeurs['_compositions'] = reset($valeurs['compositions']); // on ne regarde qu'un seul type
	if (is_array($valeurs['_compositions']) AND !isset($valeurs['_compositions'][''])){
		$valeurs['_compositions'] = array_merge(
			array(''=>array('nom'=>_T('compositions:composition_de_base'),'description'=>'','icon'=>'','configuration'=>'')),
			$valeurs['_compositions']
		);
	}
	
	// Si on hérite d'une composition
	// On modifie le tableau des compositions
	if ($valeurs['composition_defaut'] AND $valeurs['composition_defaut'] != '-') {
		$compo_defaut = $valeurs['_compositions'][$valeurs['composition_defaut']];
		$compo_vide = $valeurs['_compositions'][''];
		unset($valeurs['_compositions'][$valeurs['composition_defaut']]);
		unset($valeurs['_compositions']['']);
		$valeurs['_compositions'] = array_merge(
			array('' => $compo_defaut,'-' => $compo_vide),
			$valeurs['_compositions']
		);
	}
	
	$valeurs['_hidden'] = "<input type='hidden' name='$id_table_objet' value='$id' />";

	if (!is_array($valeurs['_compositions']) AND !isset($valeurs['id_article_accueil']))
		$valeurs['editable'] = false;
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

	sql_updateq($table_objet_sql,$update,"$id_table_objet=".intval($id));

	// mettre a jour la liste des types de compo en cache
	compositions_cacher();
	return array('message_ok'=>'','editable'=>true);
}