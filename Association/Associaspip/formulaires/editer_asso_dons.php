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

function formulaires_editer_asso_dons_charger_dist($id_don='') {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_dons associes a l'id_don passe en param */
	$contexte = formulaires_editer_objet_charger('asso_dons', $id_don, '', '',  generer_url_ecrire('dons'), '');
	if (!$id_don) { // si c'est une nouvelle operation, on charge la date d'aujourd'hui ainsi que un id_compte et journal nuls
		$contexte['date_don'] = date('Y-m-d');
		$id_compte = '';
		$journal = '';
	} else { // sinon on recupere l'id_compte correspondant et le journal dans la table des comptes
		$compte = sql_fetsel('id_compte,journal', 'spip_asso_comptes', "imputation=". sql_quote($GLOBALS['association_metas']['pc_dons']) ." AND id_journal='$id_don'");
		$journal = $compte['journal'];
		$id_compte = $compte['id_compte'];
	}
	/* ajout du journal qui ne se trouve pas dans la table asso_dons mais asso_comptes et n'est donc pas charge par editer_objet_charger */
	$contexte['journal'] = $journal;
	/* on concatene au _hidden inserer dans $contexte par l'appel a formulaire_editer_objet l'id_compte qui serat utilise dans l'action editer_asso_dons */
	$contexte['_hidden'] .= "<input type='hidden' name='id_compte' value='$id_compte' />";
	/* si id_adherent est egal a 0, c'est que le champ est vide, on ne prerempli rien */
	if (!$contexte['id_adherent'])
		$contexte['id_adherent']='';
	/* paufiner la presentation des valeurs  */
	if ($contexte['argent'])
		$contexte['argent'] = association_formater_nombrer($contexte['argent']);
	if ($contexte['valeur'])
		$contexte['valeur'] = association_formater_nombre($contexte['valeur']);
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
		$contexte['defaut_dest'] = $GLOBALS['association_metas']['dc_dons'];; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */
	}

	return $contexte;
}

function formulaires_editer_asso_dons_verifier_dist($id_don) {
	$erreurs = array();
	/* on verifie que argent et valeur ne soient pas negatifs */
	if ($erreur = association_verifier_montant('argent') )
		$erreurs['argent'] = $erreur;
	if ($erreur = association_verifier_montant('valeur') )
		$erreurs['valeur'] = $erreur;
	/* verifier si on a un numero d'adherent qu'il existe dans la base */
	if ($erreur = association_verifier_membre('id_adherent') )
		$erreurs['id_adherent'] = $erreur;
	/* verifier si besoin que le montant des destinations correspond bien au montant de l'opération */
	if (($GLOBALS['association_metas']['destinations']) && !array_key_exists('argent', $erreurs)) {
		include_spip('inc/association_comptabilite');
		if ($err_dest = association_verifier_montant_destinations('argent') ) {
			$erreurs['destinations'] = $err_dest;
		}
	}
	/* verifier la date */
	if ($erreur = association_verifier_date('date_don') )
		$erreurs['date_don'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_dons_traiter($id_don) {
	return formulaires_editer_objet_traiter('asso_dons', $id_don, '', '',  generer_url_ecrire('dons'), '');
}

?>