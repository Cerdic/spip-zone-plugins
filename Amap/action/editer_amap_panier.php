<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_amap_panier_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de amap_panier ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_amap_panier = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
	$id_amap_panier = insert_amap_panier();
	}

	if ($id_amap_panier) $err = revision_amap_panier($id_amap_panier);
	return array($id_amap_panier,$err);
}

function insert_amap_panier() {

	// toutes les dates
	$les_dates = _request('date_distribution');
	// l'auteur
	$id_auteur_adherent = _request('id_auteur');
	// pour chaque date, remplir un tableau de données correspondant à un amap_panier et l'enregistrer
	$id_amap_panier = 0;
	$i = 0;
	$data  = array();
	foreach ($les_dates as $date_distrib) {
		$data_insert = array();
		$data_insert['id_auteur'] = (int)$id_auteur_adherent;
		$data_insert['date_distribution'] = (string)$date_distrib;
		$data[] = $data_insert;

		$champs = pipeline('pre_insertion', array(
			'args' => array(
				'table' => 'spip_amap_paniers',
			),
			'data' => $data_insert
		));
		sql_insertq("spip_amap_paniers", $champs);
	}
	return $id_amap_panier;
}


// Enregistrer certaines modifications d'un amap_panier
function revision_amap_panier($id_amap_panier, $c=false) {
	
	include_spip('inc/modifier');
	$err = '';

	// l'auteur
	$id_auteur = _request('id_auteur');
	sql_updateq("spip_amap_paniers", array("id_auteur" => $id_auteur), "id_amap_panier=$id_amap_panier") ;
	spip_log("Le panier $id_amap_panier à l'auteur $id_auteur", "amap_installation");

	return $err;
}
?>
