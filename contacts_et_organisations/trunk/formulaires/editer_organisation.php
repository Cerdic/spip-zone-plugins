<?php

include_spip('inc/editer');


function formulaires_editer_organisation_charger_dist($id_organisation='new', $id_parent=0, $redirect='', $associer_objet=''){
	$contexte = formulaires_editer_objet_charger('organisation', $id_organisation, $id_parent, 0, $redirect, '');
	return $contexte;
}


function formulaires_editer_organisation_verifier_dist($id_organisation='new', $id_parent=0, $redirect='', $associer_objet=''){
	$erreurs = formulaires_editer_objet_verifier('organisation', $id_organisation);
	return $erreurs;
}


function formulaires_editer_organisation_traiter_dist($id_organisation='new', $id_parent=0, $redirect='', $associer_objet=''){
	$res = formulaires_editer_objet_traiter('organisation',$id_organisation,$id_parent,0,$redirect);
	// eviter le changement de id_organisation si on veut rediriger sur le parent
	// au moment d'une creation d'une organisation fille.
	if (_request('id_parent')) {
		$res['redirect'] = $redirect;
	}
	
	// Un lien contact ou autre a prendre en compte ?
	if ($associer_objet AND $id_organisation=$res['id_organisation']){
		$objet = '';
		if (intval($associer_objet)){
			$objet='contact';
			$id_objet = intval($associer_objet);
		}
		elseif(preg_match(',^\w+\|[0-9]+$,',$associer_objet)){
			list($objet,$id_objet) = explode('|',$associer_objet);
		}
		if ($objet AND $id_objet AND autoriser('modifier',$objet,$id_objet)) {
			// contact sur spip_organisations_contacts
			if ($objet == 'contact') {
				include_spip('action/editer_liens_simples');
				objet_associer_simples(array('organisation' => $id_organisation), array($objet => $id_objet));
			} else {
				include_spip('action/editer_liens');
				objet_associer(array('organisation' => $id_organisation), array($objet => $id_objet));
			}
			if (isset($res['redirect']))
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_organisation, '&');
		}
	}

	return $res;
}

?>
