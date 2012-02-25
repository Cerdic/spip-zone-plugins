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

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function action_editer_asso_prets_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_pret = $securiser_action();
    $erreur = '';
    include_spip('base/association');
    include_spip('inc/association_comptabilite');
    $id_compte = intval(_request('id_compte'));
    $id_ressource = intval(_request('id_ressource'));
    $id_emprunteur = intval(_request('id_emprunteur'));
    if (!$emprunteur AND $id_emprunteur) {
	$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_emprunteur");
	$emprunteur = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
    }
    $date_sortie = _request('date_sortie');
    $date_retour = _request('date_retour');
    $duree = association_recupere_montant(_request('duree'));
    $montant = association_recupere_montant(_request('montant'));
    $commentaire_sortie = _request('commentaire_sortie');
    $commentaire_retour = _request('commentaire_retour');
    $statut = _request('statut');
    $journal = _request('journal');
    if ($id_pret) { /* modification */
	// on modifie l'operation comptable associe au don
	association_modifier_operation_comptable($date_retour, $montant*($duree?$duree:1), 0, "[pret$id_pret->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_prets'], $journal, '', $id_compte);
	// on modifie les informations relatives au pret
	sql_updateq('spip_asso_prets', array(
	    'duree' => $duree,
	    'date_sortie' => $date_sortie,
	    'date_retour' => $date_retour,
	    'id_emprunteur' => $id_emprunteur,
	    'commentaire_sortie' => $commentaire_sortie,
	    'statut' => $statut,
	    'prix_unitaire' => $montant,
	), "id_pret=$id_pret" );
	// on met a jour le statut de la ressource
	sql_updateq('spip_asso_ressources',
	    array('statut' => $statut),
	    "id_ressource=$id_ressource"
	);
    } else { /* ajout */
	// on ajoute les informations relatives au pret
	$id_pret = sql_insertq('spip_asso_prets', array(
	    'id_ressource' => $id_ressource,
	    'date_sortie' => $date_sortie,
	    'duree' => $duree,
	    'date_retour' => $date_retour,
	    'id_emprunteur' => $id_emprunteur,
	    'commentaire_sortie' => $commentaire_sortie,
	    'commentaire_retour' => $commentaire_retour,
	    'prix_unitaire' => $montant,
	));
	if ($id_pret) {
	// on met a jour le statut de la ressource
	    sql_updateq('spip_asso_ressources',
		array('statut' => 'reserve'),
		"id_ressource=$id_ressource"
	    );
	} else
	    $erreur = _T('Erreur_BdD_ou_SQL');
	// on ajoute l'operation comptable associe au pret
	association_ajouter_operation_comptable($date_sortie, $montant*($duree?$duree:1), 0, "[pret$id_pret->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_prets'], $journal, $id_pret);
    }
    return array($id_pret, $erreur);
}

?>