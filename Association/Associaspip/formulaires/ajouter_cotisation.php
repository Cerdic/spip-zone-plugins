<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_ajouter_cotisation_charger_dist($id_auteur, $nom_prenom, $id_categorie, $validite)
{
	// la validite et le montant de la cotisation
	if ($id_categorie) { // si le membre a une categorie
		$categorie = sql_fetsel('duree, cotisation', 'spip_asso_categories', "id_categorie=". intval($id_categorie));
		list($annee, $mois, $jour) = explode('-', $validite);
		if ($jour==0 OR $mois==0 OR $annee==0)
			list($annee, $mois, $jour) = explode('-',date('Y-m-d'));
		$mois += $categorie['duree'];
		$contexte['validite'] = date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
		$contexte['montant'] = $categorie['cotisation'];
	} else {
		$contexte['validite'] = date('Y-m-d');
		$contexte['montant'] = 0;
	}
	// la justification
	$contexte['justification'] = _T('asso:nouvelle_cotisation') ." [$nom_prenom"."->membre$id_auteur]";
	// pour passer securiser action
	$contexte['_action'] = array('editer_cotisations',$id_auteur);
	// on passe aussi les destinations si besoin
	if ($GLOBALS['association_metas']['destinations']) {
		$contexte['id_dest'] = '';
		$contexte['montant_dest'] = '';
		$contexte['unique_dest'] = '';
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_cotisations']; // ces variables sont recuperees par la balise dynamique directement dans l'environnement
	}
	return $contexte;
}

function formulaires_ajouter_cotisation_verifier_dist($id_auteur, $nom_prenom, $categorie, $validite) {
	$erreurs = array();
	if ($GLOBALS['association_metas']['comptes']) {
		// verifier que le montant est bien positif ou nul
		if ($erreur = association_verifier_montant(_request('montant')) )
			$erreurs['montant'] = $erreur;
		// verifier validite de la date
		if ($erreur = association_verifier_date(_request('date')) )
			$erreurs['date'] = $erreur;
		// verifier si besoin que le montant des destinations correspond bien au montant de l'opération, sauf si on a deja une erreur de montant
		if (($GLOBALS['association_metas']['destinations']) && !array_key_exists('montant', $erreurs)) {
			include_spip('inc/association_comptabilite');
			if ($err_dest = association_verifier_montant_destinations(_request('montant'))) {
				$erreurs['destinations'] = $err_dest;
			}
		}
	}
	if ($erreur = association_verifier_date(_request('validite'))) {
		$erreurs['validite'] = $erreur;
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_ajouter_cotisation_traiter_dist($id_auteur, $nom_prenom, $categorie, $validite) {
	// partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php
	$res = array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	$action_cotisation = charger_fonction('ajouter_cotisation','action');
	list($id_auteur,$err) = $action_cotisation($id_auteur);
	if ($err OR !$id_auteur) {
		$res['message_erreur'] = ($err?$err:_T('erreur_traite'));
	} else {
		$res['message_ok'] = '';
		$res['redirect'] = generer_url_ecrire('adherents'); // on renvoit sur la page adherents mais on perd a l'occasion d'eventuel filtres inseres avant d'arriver au formulaire de cotisation...
	}
	return $res;
}

?>