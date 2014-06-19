<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_couleur_rubrique_charger_dist($id_rubrique){
	$editable = true;
	if ($GLOBALS['visiteur_session']['statut']!=='0minirezo')
		$editable = false;
	else {
		include_spip("inc/config");
		if (lire_config("pb_couleur_rubrique/afficher")=="non")
			$editable = false;
	}

	// chargement des valeurs du formulaire
	$valeurs = array(
		'pb_couleur_rubrique' => "#".couleur_rubrique($id_rubrique),
		'supprimer' => '',
		'_site' => $id_rubrique?'':' ',
		"editable" => $editable,
	);
	// autorisation : #ENV{editable} est evite car on veut toujours voir le formulaire meme apres validation
	return $valeurs;
}

function formulaires_couleur_rubrique_verifier_dist($id_rubrique){
	// rien de particulier a verifier
	$erreurs = array();
	if (!_request('pb_couleur_rubrique'))
		$erreurs['pb_couleur_rubrique'] = _T('info_obligatoire');
	return $erreurs;
}

function formulaires_couleur_rubrique_traiter_dist($id_rubrique){
	if (_request('supprimer')){
		effacer_meta("pb_couleur_rubrique$id_rubrique");
	}
	else {
		// preparation des variables
		$cr = _request('pb_couleur_rubrique');
		$couleur = ltrim(trim($cr),"#");
		// enregistrer/supprimer les valeurs
		ecrire_meta("pb_couleur_rubrique$id_rubrique", $couleur);
	}
	set_request('pb_couleur_rubrique'); // repasser toujours par la lecture en base

	return array("message_ok" => _T('pb_couleur_rubrique:info_message_ok'),"editable"=>true);
}

?>