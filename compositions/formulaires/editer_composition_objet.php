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
function formulaires_editer_composition_objet_charger($type,$id){
	$valeurs = array();
	$table_objet_sql = table_objet_sql($type);
	$id_table_objet = id_table_objet($type);
	$valeurs[$id_table_objet] = intval($id);

	$row = sql_fetsel('composition,composition_lock',$table_objet_sql,"$id_table_objet=".intval($id));
	if (!autoriser('styliser',$type,$id,NULL,array('row'=>$row))){
		$valeurs['editable'] = false;
	}
	else {

		if ($type=='rubrique'){
			$config_accueil = true;
			if (isset($GLOBALS['meta']['compositions'])){
				$config = unserialize($GLOBALS['meta']['compositions']);
				$config_accueil = $config['utiliser_article_accueil'] != 'non';
			}
			if ($config_accueil){
				$valeurs['id_article_accueil'] = sql_getfetsel('id_article_accueil',$table_objet_sql,"$id_table_objet=".intval($id));
				$valeurs['id_article_accueil'] = $valeurs['id_article_accueil'] ? $valeurs['id_article_accueil'] : '0';
			}
		}
		$valeurs['composition'] = $row['composition'];
		$valeurs['composition_lock'] = $row['composition_lock'];

		$valeurs['compositions'] = compositions_lister_disponibles($type);
		$valeurs['compositions'] = reset($valeurs['compositions']); // on ne regarde qu'un seul type
		if (is_array($valeurs['compositions']) AND !isset($valeurs['compositions'][''])){
			$valeurs['compositions'] = array_merge(
				array(''=>array('nom'=>_T('compositions:label_pas_de_composition'),'description'=>'','icon'=>'','configuration'=>'')),
				$valeurs['compositions']
			);
		}
		$valeurs['_hidden'] = "<input type='hidden' name='$id_table_objet' value='$id' />";

		if (!is_array($valeurs['compositions']) AND !isset($valeurs['id_article_accueil']))
			$valeurs['editable'] = false;
	}

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
	if ($type=='rubrique' AND !is_null($id_acceuil=_request('id_article_accueil'))){
		$update['id_article_accueil'] = $id_acceuil;
	}
	if (!is_null($p = _request('composition')))
		$update['composition'] = $p;

	if (autoriser('webmestre'))
		$update['composition_lock'] = _request('composition_lock')?1:0;

	sql_updateq($table_objet_sql,$update,"$id_table_objet=".intval($id));

	// mettre a jour la liste des types de compo en cache
	compositions_cacher();
	return array('message_ok'=>'','editable'=>true);
}