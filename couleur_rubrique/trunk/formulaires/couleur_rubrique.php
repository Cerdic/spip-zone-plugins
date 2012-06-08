<?php

function formulaires_couleur_rubrique_charger_dist($id_rubrique){
	// chargement des valeurs du formulaire
	$valeurs = array('pb_couleur_rubrique'=>'','supprimer'=>'');
	// autorisation : #ENV{editable} est evite car on veut toujours voir le formulaire meme apres validation
	return $valeurs;
}

function formulaires_couleur_rubrique_verifier_dist($id_rubrique){
        // rien de particulier a verifier
        $erreurs = array();
	if (!_request('pb_couleur_rubrique',$_POST) && !_request('supprimer',$_POST))
		$erreurs['message_erreur'] = _T('pb_couleur_rubrique:info_obligatoire');
	return $erreurs;
}

function formulaires_couleur_rubrique_traiter_dist($id_rubrique){
	// preparation des variables
	$cr = _request('pb_couleur_rubrique');
	$couleur = str_replace("#", "", $cr);
	// enregistrer/supprimer les valeurs
	ecrire_meta("pb_couleur_rubrique$id_rubrique",$couleur);
	if (_request('supprimer', $_POST))
		ecrire_meta("pb_couleur_rubrique$id_rubrique","");
		
	ecrire_metas();
	
}

?>
