<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_panier_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de panier ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_panier = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_panier = insert_panier();
	}

	if ($id_panier) $err = revisions_paniers($id_panier);
	return array($id_panier,$err);
}


function insert_panier() {
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_paniers',
		),
		'data' => $champs
	));
	
	$id_panier = sql_insertq("spip_paniers", $champs);
	return $id_panier;
}


// Enregistrer certaines modifications d'un panier
function revisions_paniers($id_panier, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('nom', 'prenom', 'email', 'date_distribution', 'type_panier') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('panier', $id_panier, array(
			'invalideur' => "id='id_panier/$id_panier'"
		),
		$c);
}
?>
