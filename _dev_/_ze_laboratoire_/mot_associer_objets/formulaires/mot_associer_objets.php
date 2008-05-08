<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_mot_associer_objets_charger_dist($id_mot,$id_groupe){
	$valeurs = array();
	if ($GLOBALS['visiteur_session']['statut'] == "0minirezo")
		$statuts = array('prepa','prop','publie','refuse');
	else
		$statuts = array('prop','publie');

	$valeurs = array(
	'id' => $id_mot,
	'id_groupe' => $id_groupe,
	'statuts' => $statuts,
	'recherche'=> '',
	'_ajax'=>true,
	);
	return $valeurs;
}


function formulaires_mot_associer_objets_traiter_dist($id_mot,$id_groupe){
	if (true #autoriser
	){
		include_spip('action/editer_mot');
		if ($associer = _request('associer') 
		 AND is_array($associer))
			foreach($associer as $type=>$ids){
				$table = table_objet($type);
				$table_id = id_table_objet($type);
				foreach($ids as $id){
					ajouter_nouveau_mot($id_groupe, $table, $table_id, $id_mot, $id);
				}
			}
	}
	return array(true,'');
}

?>