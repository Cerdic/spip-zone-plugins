<?php
/**
 * Gestion du formulaire de d'édition d'une organisation
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 * 
 * @package SPIP\Contacts\Formulaires
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

/**
 * Chargement du formulaire d'édition d'une organisation
 *
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Environnement du formulaire
**/
function formulaires_editer_organisation_charger_dist($id_organisation='new', $id_parent=0, $redirect='', $associer_objet=''){
	$contexte = formulaires_editer_objet_charger('organisation', $id_organisation, $id_parent, 0, $redirect, '');
	if (!intval($id_organisation) and $id_annuaire = _request('id_annuaire')){
		$contexte['id_annuaire'] = $id_annuaire;
	}
	return $contexte;
}

/**
 * Vérifications du formulaire d'édition d'une organisation
 *
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Tableau des erreurs
**/
function formulaires_editer_organisation_verifier_dist($id_organisation='new', $id_parent=0, $redirect='', $associer_objet=''){
	$erreurs = formulaires_editer_objet_verifier('organisation', $id_organisation);
	return $erreurs;
}

/**
 * Traitements du formulaire d'édition d'une organisation
 *
 * Crée l'enregistrement et l'association éventuelle avec un objet
 * indiqué
 * 
 * @param int|string $id_organisation
 *     Identifiant de l'organisation. 'new' pour une nouvelle organisation.
 * @param int $id_parent
 *     Identifiant de l'organisation parente, ou 0.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier l'organisation à cet objet,
 *     tel que 'article|3'   
 * @return array
 *     Retour des traitements
**/
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
