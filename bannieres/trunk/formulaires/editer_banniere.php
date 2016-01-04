<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_banniere_charger_dist($id_banniere='new', $retour='', $choix_diffusion){
	$valeurs = formulaires_editer_objet_charger('banniere', $id_banniere, '', '', $retour, '');

	// si c'est une nouvelle banniere, on recupere son choix_diffusion
	if ($choix_diffusion != ''){
			foreach($choix_diffusion as $clef => $valeur) {
			$valeurs[$clef] = $valeur;
			}
	}
	return $valeurs;
}

function formulaires_editer_banniere_verifier_dist($id_banniere='new', $retour='', $choix_diffusion){
	$erreurs = formulaires_editer_objet_verifier('banniere', $id_banniere, array('nom'));
	return $erreurs;
}

function formulaires_editer_banniere_traiter_dist($id_banniere='new', $retour='', $choix_diffusion){
	return formulaires_editer_objet_traiter('banniere', $id_banniere, '', '', $retour, '');
}

?>
