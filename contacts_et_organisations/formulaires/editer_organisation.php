<?php

include_spip('inc/editer');


function formulaires_editer_organisation_charger_dist($id_organisation='new', $id_parent=0, $redirect=''){
	$contexte = formulaires_editer_objet_charger('organisation', $id_organisation, $id_parent, 0, $redirect, '');
	return $contexte;
}


function formulaires_editer_organisation_verifier_dist($id_organisation='new', $id_parent=0, $redirect=''){
	$erreurs = formulaires_editer_objet_verifier('organisation', $id_organisation);
	return $erreurs;
}


function formulaires_editer_organisation_traiter_dist($id_organisation='new', $id_parent=0, $redirect=''){
	if ($redirect) refuser_traiter_formulaire_ajax();
	$res = formulaires_editer_objet_traiter('organisation',$id_organisation,$id_parent,0,$redirect);
	if ($redirect) {
		if (!parametre_url($redirect, 'id_organisation')) {
			$redirect = parametre_url($redirect, 'id_organisation', $res['id_organisation']);
		}
		$res['redirect'] = $redirect;
	}
	return $res;
}

?>
