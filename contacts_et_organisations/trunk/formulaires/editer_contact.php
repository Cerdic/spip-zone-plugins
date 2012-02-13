<?php

include_spip('inc/editer');


function formulaires_editer_contact_charger_dist($id_contact='new', $id_organisation=0, $redirect='', $associer_objet=''){
	$contexte = formulaires_editer_objet_charger('contact', $id_contact, $id_organisation, 0, $redirect, '');
	return $contexte;
}


function formulaires_editer_contact_verifier_dist($id_contact='new', $id_organisation=0, $redirect='', $associer_objet=''){
	$erreurs = formulaires_editer_objet_verifier('contact', $id_contact);
	return $erreurs;
}


function formulaires_editer_contact_traiter_dist($id_contact='new', $id_organisation=0, $redirect='', $associer_objet=''){
	$res = formulaires_editer_objet_traiter('contact', $id_contact, $id_organisation, 0, $redirect);

	// Un lien organisation ou autre a prendre en compte ?
	if ($associer_objet AND $id_contact=$res['id_contact']){
		$objet = '';
		if (intval($associer_objet)){
			$objet='organisation';
			$id_objet = intval($associer_objet);
		}
		elseif(preg_match(',^\w+\|[0-9]+$,',$associer_objet)){
			list($objet,$id_objet) = explode('|',$associer_objet);
		}
		if ($objet AND $id_objet AND autoriser('modifier',$objet,$id_objet)) {
			// organisation sur spip_organisations_contacts
			if ($objet == 'organisation') {
				include_spip('action/editer_liens_simples');
				objet_associer_simples(array($objet => $id_objet), array('contact' => $id_contact));
			} else {
				include_spip('action/editer_liens');
				objet_associer(array('contact' => $id_contact), array($objet => $id_objet));
			}
			if (isset($res['redirect']))
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_contact, '&');
		}
	}
	
	return $res;
}

?>
