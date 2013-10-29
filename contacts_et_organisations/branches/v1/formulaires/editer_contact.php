<?php

include_spip('inc/editer');


function formulaires_editer_contact_charger_dist($id_contact='new', $id_organisation=0, $redirect=''){
	$contexte = formulaires_editer_objet_charger('contact', $id_contact, $id_organisation, 0, $redirect, '');
	return $contexte;
}


function formulaires_editer_contact_verifier_dist($id_contact='new', $id_organisation=0, $redirect=''){
	$erreurs = formulaires_editer_objet_verifier('contact', $id_contact);
	return $erreurs;
}


function formulaires_editer_contact_traiter_dist($id_contact='new', $id_organisation=0, $redirect=''){
	if ($redirect) refuser_traiter_formulaire_ajax();
	$res = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $redirect);

	if ($redirect) {
		if (!parametre_url($redirect, 'id_contact')) {
			$redirect = parametre_url($redirect, 'id_contact', $res['id_contact']);
		}
		$res['redirect'] = $redirect;
	}
	return $res;
}

?>
