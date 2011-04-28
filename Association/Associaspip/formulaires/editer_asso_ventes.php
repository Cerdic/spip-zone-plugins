<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');

/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_ventes_charger_dist($id_vente='') {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_ventes associes a l'id_vente passe en param */
	$contexte = formulaires_editer_objet_charger('asso_ventes', $id_vente, '', '',  generer_url_ecrire('ventes'), '');

	/* si c'est une nouvelle operation, on charge la date d'aujourd'hui et charge un id_compte et journal null */
	if (!$id_vente) {
		$contexte['date_vente'] = $contexte['date_envoi'] = date('Y-m-d');
		$id_compte = '';
		$journal = '';
	} else { /* sinon on recupere l'id_compte correspondant et le journal dans la table des comptes */
		$comptes = sql_fetsel("id_compte,journal", "spip_asso_comptes", "imputation=".$GLOBALS['association_metas']['pc_ventes']." AND id_journal=$id_vente");
		$id_compte = $comptes['id_compte'];
		$journal = $comptes['journal'];
	}

	/* ajout du journal qui ne se trouve pas dans la table asso_dons mais asso_comptes et n'est donc pas charge par editer_objet_charger */
	$contexte['journal'] = $journal;

	/* on concatene au _hidden inserer dans $contexte par l'appel a formulaire_editer_objet l'id_compte qui sera utilise dans l'action editer_asso_dons */
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />";

	/* si id_acheteur est egal a 0, c'est que le champ est vide, on ne prerempli rien */
	if (!$contexte['id_acheteur']) $contexte['id_acheteur']='';
	
	/* paufiner la presentation des valeurs  */
	if ($contexte['prix_vente']) $contexte['prix_vente'] = association_nbrefr($contexte['prix_vente']);
	if ($contexte['frais_envoi']) $contexte['frais_envoi'] = association_nbrefr($contexte['frais_envoi']);


	// on ajoute les metas de classe_banques et destinations
	$contexte['classe_banques'] = $GLOBALS['association_metas']['classe_banques'];
	if ($GLOBALS['association_metas']['destinations']) {
		include_spip('inc/association_comptabilite');
		$contexte['destinations_on'] = true;

		/* on recupere les destinations associes a id_compte */
		$dest_id_montant = association_liste_destinations_associees($id_compte);		
		if (is_array($dest_id_montant)) {
			$contexte['id_dest'] = array_keys($dest_id_montant);
			$contexte['montant_dest'] = array_values($dest_id_montant);
		} else {
			$contexte['id_dest'] = '';
			$contexte['montant_dest'] = '';	
		}
		$contexte['unique_dest'] = true;
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_ventes'];; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */

	}
	
	return $contexte;
}

function formulaires_editer_asso_ventes_verifier_dist($id_vente) {
	$erreurs = array();
	/* on verifie que quantite, prix_vente et frais_envoi ne soient pas negatifs */
	$prix_vente = association_recupere_montant(_request('prix_vente'));
	$frais_envoi = association_recupere_montant(_request('frais_envoi'));
	$quantite = association_recupere_montant(_request('quantite'));

	if ($prix_vente<0) $erreurs['prix_vente'] = _T('asso:erreur_montant');
	if ($frais_envoi<0) $erreurs['frais_envoi'] = _T('asso:erreur_montant');
	if ($quantite<0) $erreurs['quantite'] = _T('asso:erreur_montant');

	/* verifier si on a un numero d'adherent qu'il existe dans la base */
	$id_acheteur = _request('id_acheteur');
	if ($id_acheteur != '') {
		$id_acheteur = intval($id_acheteur);
		if (sql_countsel('spip_asso_membres', "id_auteur=$id_acheteur")==0) {
			$erreurs['id_acheteur'] = _T('asso:erreur_id_adherent');
		}
		
	}

	/* verifier les dates */
	if ($erreur_date = association_verifier_date(_request('date_vente'))) {
		$erreurs['date_vente'] = _request('date_vente')."&nbsp;:&nbsp;".$erreur_date; /* on ajoute la date eronee entree au debut du message d'erreur car le filtre affdate corrige de lui meme et ne reaffiche plus les valeurs eronees */
	}
	if ($erreur_date = association_verifier_date(_request('date_envoi'))) {
		$erreurs['date_envoi'] = _request('date_envoi')."&nbsp;:&nbsp;".$erreur_date; /* on ajoute la date eronee entree au debut du message d'erreur car le filtre affdate corrige de lui meme et ne reaffiche plus les valeurs eronees */
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	return $erreurs;
}

function formulaires_editer_asso_ventes_traiter($id_vente) {
	return formulaires_editer_objet_traiter('asso_ventes', $id_vente, '', '',  generer_url_ecrire('ventes'), '');
}
?>
