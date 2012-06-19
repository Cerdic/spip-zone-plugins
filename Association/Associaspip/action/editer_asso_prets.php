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

function action_editer_asso_prets_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_pret = $securiser_action();
    $erreur = '';
    $id_ressource = intval(_request('id_ressource'));
    $id_emprunteur = intval(_request('id_emprunteur'));
    $emprunteur = _request('emprunteur');
    if (!$emprunteur AND $id_emprunteur) {
	$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_emprunteur");
	$emprunteur = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
    }
    $date_sortie = association_recupere_date(_request('date_sortie'));
    $date_retour = association_recupere_date(_request('date_retour'));
    $date_caution1 = association_recupere_date(_request('date_caution1'));
    $date_caution0 = association_recupere_date(_request('date_caution0'));
    $duree = association_recupere_montant(_request('duree'));
    $montant = association_recupere_montant(_request('montant'));
    $caution = association_recupere_montant(_request('prix_caution'));
    $fiso_sortie = $date_sortie.'T'._request('heure_sortie').':00'; // si on n'indique que l'heure, on s'assure que ce sera bien compris hh:00 et non 00:mm sinon c'est hh:mm:00 qui est transmis...
    $fiso_retour = $date_retour.'T'._request('heure_retour').':00'; // idem...
    $modifs = array(
	'duree' => $duree,
	'date_sortie' => $fiso_sortie,
	'date_retour' => $fiso_retour,
	'date_caution1' => $date_caution1,
	'date_caution0' => $date_caution0,
	'id_ressource' => $id_ressource,
	'id_emprunteur' => $id_emprunteur,
	'prix_unitaire' => $montant,
	'prix_caution' => $caution,
	'commentaire_sortie' => _request('commentaire_sortie'),
	'commentaire_retour' => _request('commentaire_retour'),
    );
    include_spip('base/association');
    $id_compte = intval(_request('id_compte'));
    $journal = _request('journal');
    include_spip('inc/association_comptabilite');
    if ($id_pret) { // modification
	// on modifie les informations relatives au pret
	sql_updateq('spip_asso_prets', $modifs, "id_pret=$id_pret" );
	// on modifie l'operation comptable associe a la location meme
	association_modifier_operation_comptable(($fiso_retour>$fiso_sortie)?$date_retour:$date_sortie, $montant*($duree?$duree:1), 0, '['. _T('asso:titre_num', array('titre'=>_T('local:pret'),'num'=>$id_pret) ) ."->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur"."->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_prets'], $journal, $id_pret, $id_compte);
	// on modifie l'operation comptable associe au depot de caution
	association_modifier_operation_comptable(($date_caution1, $caution, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) ."->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur"."->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution1'), $id_pret, _request('id_caution1'));
	// on modifie l'operation comptable associe au retour de caution
	association_modifier_operation_comptable(($date_caution0, 0, $caution, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) ."->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur"."->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution0'), $id_pret, _request('id_caution0'));
	// on met a jour le statut de la ressource
	$statut_old = sql_getfetsel('statut', 'spip_asso_ressources', "id_ressource=$id_ressource");
	if (is_numeric($statut_old)) { /* nouveaux statuts numeriques */
	    if ($statut_old<0)
		$statut_new = $statut_old-1;
	    else
		$statut_new = $statut_old+1;
	} else { /* anciens statuts textuels */
	    $statut_new = 'ok';
	}
	if ($fiso_retour>$fiso_sortie)
	    sql_updateq('spip_asso_ressources',
		array('statut' => $statut_new),
	    "id_ressource=$id_ressource" );
    } else { // ajout
	// on ajoute les informations relatives au pret
	$id_pret = sql_insertq('spip_asso_prets', $modifs);
	if ($id_pret) { // on ajoute les informations connexes
	    // on ajoute l'operation comptable associe au pret en lui-meme
	    association_ajouter_operation_comptable($date_sortie, $montant*($duree?$duree:1), 0, '['. _T('asso:titre_num', array('titre'=>_T('local:pret'),'num'=>$id_pret) ) ."->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur"."->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_prets'], $journal, $id_pret);
	    // on encaisse la caution
	    if ( $caution>0 AND $GLOBALS['association_metas']['pc_cautions'] AND $date_caution1 )
		association_ajouter_operation_comptable($date_caution1, $caution, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) ."->pret$id_pret] - ". ($id_emprunteur?"[$emprunteur"."->membre$id_emprunteur]":$emprunteur), $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution1'), $id_pret);
	    // on met a jour le statut de la ressource
	    $statut_old = sql_getfetsel('statut', 'spip_asso_ressources', "id_ressource=$id_ressource");
	    if (is_numeric($statut_old)) { // nouveaux statuts numeriques
		if ($statut_old<0)
		    $statut_new = $statut_old+1;
		else
		    $statut_new = $statut_old-1;
	    } else { // anciens statuts textuels
		$statut_new = 'reserve';
	    }
	    sql_updateq('spip_asso_ressources',
		array('statut' => $statut_new),
	    "id_ressource=$id_ressource" );
	} else
	    $erreur = _T('asso:erreur_sgbdr');
    }
    return array($id_pret, $erreur);
}

?>