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


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function action_editer_asso_prets()
{

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_pret=$securiser_action();

    include_spip('base/association');

    $id_compte = intval(_request('id_compte'));
    $id_ressource = intval(_request('id_ressource'));
    $id_emprunteur = intval(_request('id_emprunteur'));
    $date_sortie = _request('date_sortie');
    $date_retour = _request('date_retour'));
    $duree = association_recupere_montant(_request('duree'));
    $montant = association_recupere_montant(_request('montant'));
    $commentaire_sortie = _request('commentaire_sortie');
    $commentaire_retour = _request('commentaire_retour');
    $statut = _request('statut');
    $journal = _request('journal');
	$justification='[pret n&deg; '.$id_pret.'->pret'.$id_pret.'] - '.$id_emprunteur;
	$recette=$quantite*$prix_vente;

    if ($id_pret) { /* modification */
	prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant);
    } else { /* ajout */
	$id_vente = prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $commentaire_sortie,$commentaire_retour);

    }

    return array($id_vente, '');
}

function prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant)
{
    sql_updateq('spip_asso_prets', array(
	'duree' => $duree,
	'date_sortie' => $date_sortie,
	'date_retour' => $date_retour,
	'id_emprunteur' => $id_emprunteur,
	'commentaire_sortie' => $commentaire_sortie
    ), "id_pret=$id_pret" );
    sql_updateq('spip_asso_comptes', array(
	'journal' => $journal,
	'recette' => $montant,
	'date' => $date_sortie
    ), "id_journal=$id_pret");
    // mettre a jour les destinations comptables
}

function prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $commentaire_sortie,$commentaire_retour)
{
    $id_pret = sql_insertq('spip_asso_prets', array(
	'id_ressource' => $id_ressource,
	'date_sortie' => $date_sortie,
	'duree' => $duree,
	'date_retour' => $date_retour,
	'id_emprunteur' => $id_emprunteur,
	'commentaire_sortie' => $commentaire_sortie,
	'commentaire_retour' => $commentaire_retour
    ));
    if ($id_pret) {
	$id_pret = sql_insertq('spip_asso_comptes', array(
	    'date' => $date_sortie,
	    'journal' => $journal,
	    'recette' => $montant,
	    'justification' => _T('asso:pret_nd').$id_ressource.'/'.$id_pret,
	    'imputation' => $GLOBALS['association_metas']['pc_prets'],
	    'id_journal' => $id_pret
	));
	sql_updateq('spip_asso_ressources',
	    array('statut' => 'reserve'),
	    "id_ressource=$id_ressource"
	);
    }
    // ajouter destinations comptables
}

?>
