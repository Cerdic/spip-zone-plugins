<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_produit_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

		if (!$id_produit = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_produit = insert_produit();
	}

	if ($id_produit) $err = revisions_produits($id_produit);
	return array($id_produit,$err);
}


function insert_produit() {
	$champs = array(
		'nom' => _T('produits:item_nouveau_produit')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_produits',
		),
		'data' => $champs
	));
	
	$id_produit = sql_insertq("spip_produits", $champs);
	return $id_produit;
}

function revisions_produits($id_produit, $c=false) {

	if ($c === false) {
		$c = array();
		foreach (array('nom', 'descriptif', 'texte', 'prix', 'reference', 'rubrique') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('produit', $id_produit, array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_produit/$id_produit'"
		),
		$c);
}
?>
