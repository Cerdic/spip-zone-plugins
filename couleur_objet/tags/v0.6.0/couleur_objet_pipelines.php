<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function couleur_objet_affiche_droite($flux){

	include_spip('inc/config');
	$objets_config = lire_config('couleur_objet/objets',array());

	$e     = trouver_objet_exec($flux['args']['exec']);
	$table_objet_sql = $e['table_objet_sql'];

	if (
		in_array($table_objet_sql,$objets_config) // si configuration objets ok
		AND $e !== false // page d'un objet éditorial
		AND $e['edition'] === false // pas en mode édition
		AND $id_objet=$flux['args'][$e['id_table_objet']]
	){
		$objet = $e['type'];
		include_spip('inc/couleur_objet');
		$couleur_objet = objet_lire_couleur($objet, $id_objet);
		$contexte = array('objet' => $objet, 'id_objet' => $id_objet, 'couleur_objet' => $couleur_objet);
		$flux["data"] .= recuperer_fond("inclure/couleur_objet", $contexte);
	}
	return $flux;
}
