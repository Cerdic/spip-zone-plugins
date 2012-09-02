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

function formulaires_editer_asso_activites_charger_dist($id_activite='')
{
	/* charger dans $contexte tous les champs de la table spip_asso_activites associes a l'id_activite passe en param */
	$contexte = formulaires_editer_objet_charger('asso_activites', $id_activite, '', '',  generer_url_ecrire('activites'), '');
	if (!$id_activite) { /* si c'est un ajout */
		$contexte['id_evenement'] = intval(_request('id_evenement'));
		if ( !sql_countsel('spip_evenements', 'id_evenement='. $contexte['id_evenement']) )
			exit; // sortir sans proces si evenement inexistant
		$contexte['date_inscription'] = date('Y-m-d');
		$contexte['date_paiement'] = '';
		$id_compte = $journal = '';
	} else { /* sinon on recupere l'id_compte correspondant et le journal dans la table des comptes */
		$compte = sql_fetsel('id_compte,journal', 'spip_asso_comptes', "imputation='".$GLOBALS['association_metas']['pc_activites']."' AND id_journal='$id_activite'");
		$journal = $compte['journal'];
		$id_compte = $compte['id_compte'];
	}
	/* ajout du journal qui ne se trouve pas dans la table asso_dons mais asso_comptes et n'est donc pas charge par editer_objet_charger */
	$contexte['journal'] = $journal;

	/* on concatene au _hidden inserer dans $contexte par l'appel a formulaire_editer_objet l'id_compte qui sera utilise dans l'action editer_asso_activites */
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />";
	/* transmettre l'id_evenement via un champ cache */
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$contexte[id_evenement]' />";

	/* si date_paiement est indeterminee, c'est que le champ est vide : on ne preremplit rien  */
	if ($contexte['date_paiement']=='0000-00-00')
		$contexte['date_paiement'] = '';
	/* si id_adherent est egal a 0, c'est que le champ est vide : on ne prerempli rien */
	if (!$contexte['id_adherent'])
		$contexte['id_adherent']='';
	/* paufiner la presentation des valeurs  */
	if ($contexte['inscrits'])
		$contexte['inscrits'] = association_nbrefr($contexte['inscrits']);

	// on ajoute les metas de destinations
	if ($GLOBALS['association_metas']['destinations']) {
		include_spip('inc/association_comptabilite');
		/* on recupere les destinations associes a id_compte */
		$dest_id_montant = association_liste_destinations_associees($id_compte);
		if (is_array($dest_id_montant)) {
			$contexte['id_dest'] = array_keys($dest_id_montant);
			$contexte['montant_dest'] = array_values($dest_id_montant);
		} else {
			$contexte['id_dest'] = '';
			$contexte['montant_dest'] = '';
		}
		$contexte['unique_dest'] = '';
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_activites'];; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */
	}

	/* renvoyer le contexte pour (p)re-remplir le formulaire  */
	return $contexte;
}

function formulaires_editer_asso_activites_verifier_dist($id_activite='')
{
	$erreurs = array();

	/* on verifie la validite des dates */
	if ($erreur = association_verifier_date('date_inscription') )
		$erreurs['date_inscription'] = $erreur;
	if ($erreur = association_verifier_date('date_paiement', true) )
		$erreurs['date_paiement'] = $erreur;
	/* on verifie la validite des nombres */
	if ($erreur = association_verifier_montant('inscrits') )
		$erreurs['inscrits'] = $erreur;
	if ($erreur = association_verifier_montant('montant') )
		$erreurs['montant'] = $erreur;
	/* verifier si on a un numero d'adherent qu'il existe dans la base */
	if ($erreur = association_verifier_membre('id_adherent') )
		$erreurs['id_adherent'] = $erreur;
	/* verifier si besoin que le montant des destinations correspond bien au montant de l'opération */
	if (($GLOBALS['association_metas']['destinations']) && !array_key_exists('montant', $erreurs)) {
		include_spip('inc/association_comptabilite');
		if ($err_dest = association_verifier_montant_destinations('argent') ) {
			$erreurs['destinations'] = $err_dest;
		}
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_activites_traiter_dist($id_activite='')
{
	return formulaires_editer_objet_traiter('asso_activites', $id_activite, '', '',  generer_url_ecrire('voir_activites','id='.intval(_request('id_evenement'))), '');
}

?>