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

function formulaires_editer_asso_ressources_charger_dist($id_ressource='')
{
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_dons associes a l'id_don passe en param */
	$contexte = formulaires_editer_objet_charger('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');

	/* si c'est une nouvelle operation, on charge la date d'aujourd'hui ainsi que un id_compte et journal nuls */
	if (!$id_ressource) {
		$contexte['date_acquisition'] = date('Y-m-d');
		$contexte['ud'] = 'D';
		$id_compte = '';
		$journal = '';
	} else { /* sinon on recupere l'id_compte correspondant et le journal dans la table des comptes */
		$compte = sql_fetsel('id_compte,journal', 'spip_asso_comptes', "imputation='".$GLOBALS['association_metas']['pc_ressources']."' AND id_journal='$id_ressource'");
		$journal = $compte['journal'];
		$id_compte = $compte['id_compte'];
	}
	/* ajout du journal qui ne se trouve pas dans la table asso_dons mais asso_comptes et n'est donc pas charge par editer_objet_charger */
	$contexte['journal'] = $journal;
	/* on concatene au _hidden inserer dans $contexte par l'appel a formulaire_editer_objet les id_compte qui seront utilises dans l'action editer_asso_dons */
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />";

	if (!$contexte['statut']) { // par defaut utiliser les nouveaux statuts numeriques (on ne garde la compatibilite qu'en edition --si l'entree n'est pas convertie)
		$contexte['quantite'] = 1;
		$contexte['suspendu'] = '';
	} elseif (is_numeric($contexte['statut'])) { // et pour les statuts numerique convertir les nombres negatifs...
		$contexte['quantite'] = abs($contexte['statut']);
		$contexte['suspendu'] = ($contexte['statut']<0?'on':'');
	}
	if (!$contexte['ud']) { // les anciens enregistrements (ou ceux inseres autrement que via ce formulaire) n'ont pas d'unite de precise : on assigne la valeur par defaut (le jour)
		$contexte['ud'] = 'D';
	}

	/* paufiner la presentation des valeurs  */
	if ($contexte['pu'])
		$contexte['pu'] = association_formater_nombre($contexte['pu']);
	if ($contexte['prix_acquisition'])
		$contexte['prix_acquisition'] = association_formater_nombre($contexte['prix_acquisition']);
	if ($contexte['prix_caution'])
		$contexte['prix_caution'] = association_formater_nombre($contexte['prix_caution']);
	if (is_numeric($contexte['statut']))
		$contexte['statut'] = association_formater_nombre($contexte['statut']);

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
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_ressources'];; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */
	}

	return $contexte;
}

function formulaires_editer_asso_ressources_verifier_dist($id_ressource='')
{
	$erreurs = array();
	/* on verifie que prix de location et d'achat ne soit pas negatifs */
	if ($erreur = association_verifier_montant('pu') )
		$erreurs['pu'] = $erreur;
	if ($erreur = association_verifier_montant('prix_caution') )
		$erreurs['prix_caution'] = $erreur;
	if ($erreur = association_verifier_montant('prix_acquisition') )
		$erreurs['prix_acquisition'] = $erreur;
	if ($erreur = association_verifier_montant('quantite') )
		$erreurs['statut'] = $erreur;
	/* verifier si besoin que le montant des destinations correspond bien au montant de l'opération */
	if (($GLOBALS['association_metas']['destinations']) && !array_key_exists('prix_acquisition', $erreurs)) {
		include_spip('inc/association_comptabilite');
		if ($err_dest = association_verifier_montant_destinations('prix_acquisition') ) {
			$erreurs['destinations'] = $err_dest;
		}
	}
	/* verifier la date */
	if ($erreur = association_verifier_date('date_acquisition') )
		$erreurs['date_acquisition'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_ressources_traiter($id_ressource='')
{
	return formulaires_editer_objet_traiter('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');
}

?>