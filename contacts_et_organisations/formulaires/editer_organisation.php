<?php

include_spip('inc/editer');


function formulaires_editer_organisation_charger_dist($id_organisation='new', $id_parent=0, $retour=''){
	$contexte = formulaires_editer_objet_charger('organisation', $id_organisation, $id_parent, 0, $retour, '');
	return $contexte;
}


function formulaires_editer_organisation_verifier_dist($id_organisation='new', $id_parent=0, $retour=''){
	$erreurs = formulaires_editer_objet_verifier('organisation', $id_organisation);
	return $erreurs;
}


function formulaires_editer_organisation_traiter_dist($id_organisation='new', $id_parent=0, $retour=''){
	if ($retour) refuser_traiter_formulaire_ajax();
	$res = formulaires_editer_objet_traiter('organisation',$id_organisation,$id_parent,0,$retour);
	if ($retour) {
		if (!parametre_url($retour, 'id_organisation')) {
			$retour = parametre_url($retour, 'id_organisation', $res['id_organisation']);
		}
		$res['redirect'] = $retour;
	}
	return $res;
}

?>
