<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_editer_asso_activites_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_activite = $securiser_action();
    $erreur = '';
    $date_paiement = association_recupere_date(_request('date_paiement'));
    $participant = _request('nom');
    $id_adherent = intval(_request('id_adherent'));
    if (!$participant AND $id_adherent) {
	$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_adherent");
	$participant = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
    }
    $evenement = intval(_request('id_evenement'));
    $montant = association_recupere_montant(_request('montant'));
    $inscrits = association_recupere_montant(_request('inscrits'));
    $modifs = array(
	'id_evenement' => $evenement,
	'nom' => _request('nom'),
	'id_adherent' => $id_adherent,
	'inscrits' => $inscrits,
	'montant' => $montant,
	'date_paiement' => $date_paiement,
	'date_inscription' => association_recupere_date(_request('date_inscription')),
	'commentaire' => _request('commentaire'),
    );
    include_spip('base/association');
    $id_compte = intval(_request('id_compte'));
    $journal = _request('journal');
    include_spip('inc/association_comptabilite');
    if ($id_activite) { /* c'est une modification */
	// on modifie les operations comptables associees a la participation
	association_modifier_operation_comptable($date_paiement, $montant, 0, '['. _T('asso:titre_num', array('titre'=>_T('evenement'),'num'=>$evenement) ) ."->activite$evenement] &mdash; ". ($id_adherent?"[$participant"."->membre$id_adherent]":$participant)." +$inscrits", $GLOBALS['association_metas']['pc_activites'], $journal, $id_activite, $id_compte);
	// on modifie les informations relatives a la participation
	sql_updateq('spip_asso_activites', $modifs,  "id_activite=$id_activite");
    } else { /* c'est un ajout */
	// on enregistre l'inscription/participation a l'activite
	$id_activite = sql_insertq('spip_asso_activites', $modifs);
	if (!$id_activite) { // la suite serait aleatoire sans cette cle...
	    $erreur = _T('asso:erreur_sgbdr');
	} else { // on ajoute l'operation comptable associee a la participation
	    association_ajouter_operation_comptable($date_paiement, $montant, 0, '['. _T('asso:titre_num', array('titre'=>_T('evenement'),'num'=>$evenement) ) ."->activite$evenement] &mdash; ". ($id_adherent?"[$participant"."->membre$id_adherent]":$participant), $GLOBALS['association_metas']['pc_activites'], $journal, $id_activite);
	}
    }

    return array($id_activite, $erreur);
}

?>