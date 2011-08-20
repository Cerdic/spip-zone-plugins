<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_livrable_charger_dist($id_livrable='new', $url='', $titre='', $descriptif=''){
	$valeurs = formulaires_editer_objet_charger('livrable', $id_livrable, '', '', $retour, '');
	$valeurs['url'] = $url;
	$valeurs['titre'] = $titre;
	$valeurs['descriptif'] = $descriptif;
	return $valeurs;
}

function formulaires_editer_livrable_verifier_dist($id_livrable='new', $url='', $titre='', $descriptif=''){
	$erreurs = formulaires_editer_objet_verifier('livrable', $id_livrable);
	return $erreurs;
}

function formulaires_editer_livrable_traiter_dist($id_livrable='new', $url='', $titre='', $descriptif=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('livrable', $id_livrable, '', '', $retour, '');
}

?>
