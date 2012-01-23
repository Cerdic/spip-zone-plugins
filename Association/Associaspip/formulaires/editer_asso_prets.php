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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_prets_charger_dist($id_pret,  $id='')
{
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_prets associes a l'id_pret passe en param */
	$contexte = formulaires_editer_objet_charger('asso_prets', $id_pret, '', '',  generer_url_ecrire('prets'), '');

	if (!$id_pret OR $contexte['agir']=='ajouter') { /* si c'est une nouvelle operation, on charge la date d'aujourd'hui, charge un id_compte et journal null, le statut et le prix de location de base */
		$contexte['date_sortie'] = $contexte['date_retour'] = date('Y-m-d');
		$id_compte = $journal = '';
		$id_ressource = _request('id');
		$ressource = sql_fetsel("pu,statut", "spip_asso_ressources", "id_ressource=$id_ressource");
		$contexte['statut']=$ressource['statut'];
		$contexte['montant']=$ressource['pu'];
	} else { /* sinon on recupere l'id_compte correspondant et le journal dans la table des comptes */
//		$contexte['date_retour'] = date('Y-m-d');
		$comptes = sql_fetsel("id_compte,journal", "spip_asso_comptes", "imputation=".$GLOBALS['association_metas']['pc_prets']." AND id_journal=$id_pret");
		$id_compte = $comptes['id_compte'];
		$journal = $comptes['journal'];
	}

	/* ajout du journal qui ne se trouve pas dans la table asso_prets mais asso_comptes et n'est donc pas charge par editer_objet_charger */
	$contexte['journal'] = $journal;

	/* on concatene au _hidden inserer dans $contexte par l'appel a formulaire_editer_objet l'id_compte qui sera utilise dans l'action editer_asso_dons */
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />";

	/* si id_emprunteur est egal a 0, c'est que le champ est vide, on ne prerempli rien */
	if (!$contexte['id_emprunteur'])
		$contexte['id_emprunteur']='';

	/* paufiner la presentation des valeurs  */
	if ($contexte['montant'])
		$contexte['montant'] = association_nbrefr($contexte['montant']);

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
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_prets'];; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */
	}

	return $contexte;
}

function formulaires_editer_asso_ressources_verifier_dist($id_pret='')
{
	$erreurs = array();

	/* on verifie que montant et duree ne soient pas negatifs */
	if (association_recupere_montant(_request('montant')<0))
		$erreurs['montant'] = _T('asso:erreur_montant');
	if (association_recupere_montant(_request('duree')<0))
		$erreurs['duree'] = _T('asso:erreur_montant');

	/* verifier si on a un numero d'adherent qu'il existe dans la base */
	$id_emprunteur = _request('id_emprunteur');
	if ($id_emprunteur != '') {
		$id_emprunteur = intval($id_emprunteur);
		if (sql_countsel('spip_asso_membres', "id_auteur=$id_emprunteur")==0) {
			$erreurs['id_emprunteur'] = _T('asso:erreur_id_adherent');
		}
	}

	/* verifier les dates */
	if ($erreur_date = association_verifier_date(_request('date_sortie'))) {
		$erreurs['date_sortie'] = _request('date_sortie')."&nbsp;:&nbsp;".$erreur_date; /* on ajoute la date eronee entree au debut du message d'erreur car le filtre affdate corrige de lui meme et ne reaffiche plus les valeurs eronees */
	}
	if ($erreur_date = association_verifier_date(_request('date_retour'))) {
		$erreurs['date_retour'] = _request('date_retour')."&nbsp;:&nbsp;".$erreur_date; /* on ajoute la date eronee entree au debut du message d'erreur car le filtre affdate corrige de lui meme et ne reaffiche plus les valeurs eronees */
	}

	/* verifier les destinations comptables */

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	return $erreurs;
}

function formulaires_editer_asso_ressources_traiter($id_pret='')
{
	$action = _request('agir');
	return formulaires_editer_objet_traiter('asso_prets', $id_pret, '', '',  generer_url_ecrire('prets'), '');
}
?>
