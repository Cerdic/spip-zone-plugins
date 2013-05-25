<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_editer_asso_pret_dist() {
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_pret = $securiser_action();
    $erreur = '';
    $id_ressource = association_recuperer_entier('id_ressource');
    $id_auteur = association_recuperer_entier('id_auteur');
    $emprunteur = _request('emprunteur');
    if (!$emprunteur) {
	$emprunteur = association_formater_idnom($id_auteur, array('spip_asso_membres'), '');
    }
    $date_sortie = association_recuperer_date('date_sortie');
    $date_retour = association_recuperer_date('date_retour');
    $date_caution1 = association_recuperer_date('date_caution1');
    $date_caution1 = ($date_caution1?$date_caution1:$date_sortie);
    $date_caution0 = association_recuperer_date('date_caution0');
    $date_caution0 = ($date_caution0?$date_caution0:$date_retour);
    $duree = association_recuperer_montant('duree');
    $montant = association_recuperer_montant('montant');
    $caution = association_recuperer_montant('prix_caution');
    $caution = ($caution>0?$caution:'');
    $fiso_sortie = $date_sortie.'T'._request('heure_sortie').':00'; // si on n'indique que l'heure, on s'assure que ce sera bien compris hh:00 et non 00:mm sinon c'est hh:mm:00 qui est transmis...
    $fiso_retour = $date_retour.'T'._request('heure_retour').':00'; // idem...
    $modifs = array(
	'duree' => $duree,
	'date_sortie' => $fiso_sortie,
	'date_retour' => $fiso_retour,
	'date_caution1' => $date_caution1,
	'date_caution0' => $date_caution0,
	'id_ressource' => $id_ressource,
	'id_auteur' => $id_auteur,
	'prix_unitaire' => $montant,
	'prix_caution' => $caution,
	'commentaire_sortie' => _request('commentaire_sortie'),
	'commentaire_retour' => _request('commentaire_retour'),
    );
    include_spip('base/association');
    $id_compte = association_recuperer_entier('id_compte');
    $journal = _request('journal');
    $ref_pret = "->pret$id_pret] - ". ($id_auteur?"[$emprunteur"."->membre$id_auteur]":$emprunteur);
    include_spip('inc/association_comptabilite');
    if ($id_pret) { // modification
	// on modifie l'operation comptable associee a la location meme
	$erreur = comptabilite_operation_modifier(($fiso_retour>$fiso_sortie)?$date_retour:$date_sortie, $montant*($duree?$duree:1), 0, '['. _T('asso:titre_num', array('titre'=>_T('local:pret'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_prets'], $journal, $id_pret, $id_compte);
	// on modifie l'opertation comptable associee a la caution
	if ( !$erreur && $caution && $GLOBALS['association_metas']['pc_cautions'] ) { // les cautions sont encaissees
	    $association_imputation = charger_fonction('association_imputation', 'inc');
	    $critere = $association_imputation('pc_cautions', $id_pret);
	    $err2 = comptabilite_operation_modifier($date_caution1, $caution, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution1'), $id_pret, sql_getfetsel('id_compte', 'spip_asso_comptes', "$critere AND recette>0") ); // depot
	    $err3 = comptabilite_operation_modifier($date_caution0, 0, $caution, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution0'), $id_pret, sql_getfetsel('id_compte', 'spip_asso_comptes', "$critere AND depense>0") ); // restitution
	    $erreur = ($err2?$err2:$err3);
	}
	if (!$erreur) {
	    // on modifie les informations relatives au pret
	    sql_updateq('spip_asso_prets', $modifs, "id_pret=$id_pret" );
	    // on met a jour le statut de la ressource
	    $statut_old = sql_getfetsel('statut', 'spip_asso_ressources', "id_ressource=$id_ressource");
	    if (is_numeric($statut_old)) { // nouveaux statuts numeriques
		if ($statut_old<0)
		    $statut_new = $statut_old-1;
		else
		    $statut_new = $statut_old+1;
	    } else { // anciens statuts textuels
		$statut_new = 'ok';
	    }
	    if ($fiso_retour>$fiso_sortie)
		sql_updateq('spip_asso_ressources',
		    array('statut' => $statut_new),
		"id_ressource=$id_ressource" );
	}
    } else { // ajout
	// on ajoute les informations relatives au pret
	$id_pret = sql_insertq('spip_asso_prets', $modifs);
	if ($id_pret) { // on ajoute les informations connexes
	    // on ajoute l'operation comptable associe au pret en lui-meme
	    comptabilite_operation_ajouter($date_sortie, $montant*($duree?$duree:1), 0, '['. _T('asso:titre_num', array('titre'=>_T('local:pret'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_prets'], $journal, $id_pret);
	    // on ajoute l'operation comptable associe au cautionnement
	    if ( $caution AND $GLOBALS['association_metas']['pc_cautions'] ) { // gestion du cautionnement
		comptabilite_operation_ajouter($date_caution1, $caution, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution1'), $id_pret); // on encaisse la caution
		comptabilite_operation_ajouter($date_caution0, 0, $caution, '['. _T('asso:titre_num', array('titre'=>_T('local:caution'),'num'=>$id_pret) ) .$ref_pret, $GLOBALS['association_metas']['pc_cautions'], _request('mode_caution1'), $id_pret); // on prevoit sa restitution
	    }
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