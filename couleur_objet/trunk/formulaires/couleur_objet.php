<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_couleur_objet_charger_dist($objet,$id_objet,$couleur_objet){
	// autorisation : #ENV{editable} est evite car on veut toujours voir le formulaire meme apres validation
	$editable = true;
	if ($GLOBALS['visiteur_session']['statut']!=='0minirezo')
		$editable = false;
	else {
		include_spip("inc/config");
		if (lire_config("couleur_objet/bloquer")=="oui")
			$editable = false;
	}
	// chargement des valeurs du formulaire
	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'couleur_objet' => $couleur_objet,
		'supprimer' => '',
		"editable" => $editable,
	);
	return $valeurs;
}

function formulaires_couleur_objet_traiter_dist($objet, $id_objet, $couleur_objet) {
	include_spip('inc/couleur_objet');
	if (_request('supprimer')){
		objet_supprimer_couleur($objet, $id_objet);
		set_request('couleur_objet','');
	}
	else {
		$couleur_objet = _request('couleur_objet');
		objet_modifier_couleur($objet, $id_objet, $couleur_objet);
	}
}