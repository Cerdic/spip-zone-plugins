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

function formulaires_editer_activite_inscription_charger_dist($id_activite='')
{
	/* charger dans $contexte tous les champs de la table spip_asso_activites associes a l'id_activite passe en param */
	$contexte = formulaires_editer_objet_charger('asso_activites', $id_activite, '', '',  generer_url_ecrire('inscrits_activite','id='.intval(_request('id_evenement'))), '');
	if (!$id_activite) { /* si c'est un ajout */
		$contexte['id_evenement'] = intval(_request('id_evenement'));
		if ( !sql_countsel('spip_evenements', 'id_evenement='. $contexte['id_evenement']) )
			exit; // sortir sans proces si evenement inexistant
		$contexte['date_inscription'] = date('Y-m-d');
		$contexte['date_paiement'] = '0000-00-00';
	}
	/* transmettre des parametres (verification et traitement) via champs caches */
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$contexte[id_evenement]' />";
	$contexte['_hidden'] .= "<input type='hidden' name='date_inscription' value='$contexte[date_inscription]' />";
	$contexte['_hidden'] .= "<input type='hidden' name='date_paiement' value='$contexte[date_paiement]' />";
	/* si date_paiement est indeterminee, c'est que le champ est vide : on ne preremplit rien  */
	if ($contexte['date_paiement']=='0000-00-00')
		$contexte['date_paiement'] = '';
	/* si id_adherent est egal a 0, c'est que le champ est vide, on ne prerempli rien */
	if (!$contexte['id_adherent'])
		$contexte['id_adherent']='';
	/* paufiner la presentation des valeurs  */
	if ($contexte['inscrits'])
		$contexte['inscrits'] = association_nbrefr($contexte['inscrits']);
	/* pour passer securiser action */
	$contexte['_action'] = array('editer_activite_inscription',$id_activite);

	/* renvoyer le contexte pour (p)re-remplir le formulaire  */
	return $contexte;
}

function formulaires_editer_activite_inscription_verifier_dist($id_activite='')
{
	$erreurs = array();

	/* on verifie la validite des dates */
	if ($erreur = association_verifier_date(_request('date_inscription')) )
		$erreurs['date_inscription'] = $erreur;
	/* on verifie la validite des nombres */
	if ($erreur = association_verifier_montant(_request('inscrits')) )
		$erreurs['inscrits'] = $erreur;
	if ($erreur = association_verifier_montant(_request('montant')) )
		$erreurs['montant'] = $erreur;
	/* verifier si on a un numero d'adherent qu'il existe dans la base */
	if ($erreur = association_verifier_membre(_request('id_adherent')) )
		$erreurs['id_adherent'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_activite_inscription_traiter_dist($id_activite='')
{
	return formulaires_editer_objet_traiter('asso_activites', $id_activite, '', '',  generer_url_ecrire('inscrits_activite','id='.intval(_request('id_evenement'))), '');
}

?>