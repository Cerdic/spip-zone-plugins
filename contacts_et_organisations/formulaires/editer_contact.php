<?php

include_spip('inc/editer');


function formulaires_editer_contact_charger_dist($id_contact='new', $id_organisation=0, $retour=''){
	$contexte = formulaires_editer_objet_charger('contact', $id_contact, $id_organisation, 0, $retour, '');
	return $contexte;
}


function formulaires_editer_contact_verifier_dist($id_contact='new', $id_organisation=0, $retour=''){
	$erreurs = formulaires_editer_objet_verifier('contact', $id_contact);
	return $erreurs;
}


function formulaires_editer_contact_traiter_dist($id_contact='new', $id_organisation=0, $retour=''){
	if ($retour) refuser_traiter_formulaire_ajax();
	$res = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $retour);
	if ($retour) {
		if (!parametre_url($retour, 'id_contact')) {
			$retour = parametre_url($retour, 'id_contact', $res['id_contact']);
		}
		$res['redirect'] = $retour;
	}
	return $res;
}

?>
