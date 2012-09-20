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

function action_editer_asso_dons()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don = $securiser_action();
	$erreur = '';
	$date_don = association_recuperer_date('date_don');
	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = association_recuperer_entier('id_adherent');
	if (!$bienfaiteur) {
		$bienfaiteur = association_formater_idnom($id_adherent, array('spip_asso_membres'), '');
	}
	$argent = association_recuperer_montant('argent');
	$valeur = association_recuperer_montant('valeur');
	$modifs = array(
		'date_don' => $date_don,
		'bienfaiteur' => _request('bienfaiteur'),
		'id_adherent' => $id_adherent,
		'argent' => $argent,
		'colis' => _request('colis'),
		'valeur' => $valeur,
		'contrepartie' => _request('contrepartie'),
		'commentaire' => _request('commentaire'),
	);
    include_spip('base/association');
	$id_compte = association_recuperer_entier('id_compte');
	$journal = _request('journal');
	$ref_don = "->don$id_don] &mdash; ". ($id_adherent?"[$bienfaiteur"."->membre$id_adherent]":$bienfaiteur);
	include_spip('inc/association_comptabilite');
	if ($id_don) { // c'est une modification
		// on modifie les operations comptables associees au don
		if ($GLOBALS['association_metas']['pc_dons']==$GLOBALS['association_metas']['pc_colis']) { // si dons et colis sont associes a la meme reference, on modifie une seule operation
			$erreur = association_modifier_operation_comptable($date_don, $argent+$valeur, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:don'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_dons'], $journal, $id_don, $id_compte);
		} else { // sinon on en modifie deux
			// modification du don en argent
			$err1 = association_modifier_operation_comptable($date_don, $argent, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:don'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_dons'], $journal, $id_don, $id_compte);
			// modification du don en nature
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_colis');
			$critere .= ($critere?' AND ':'') ."id_journal=$id_don";
			$err2 = association_modifier_operation_comptable($date_don, $valeur, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:colis'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_colis'], '', $id_don, sql_getfetsel('id_compte', 'spip_asso_comptes', $critere) );
			$erreur = ($err1?:$err1:$err2);
		}
		if (!$erreur) // on modifie les informations relatives au don
			sql_updateq('spip_asso_dons', $modifs, "id_don=$id_don");
	} else { // c'est un ajout
		// on ajoute les informations relatives au don
		$id_don = sql_insertq('spip_asso_dons', $modifs);
		if (!$id_don) { // la suite serait aleatoire sans cette cle...
			$erreur = _T('asso:erreur_sgbdr');
		} else { // on ajoute les operations comptables associees au don
			if ($GLOBALS['association_metas']['pc_dons']==$GLOBALS['association_metas']['pc_colis']) { // si argent et colis sont associes a la meme reference, on ajoute une seule operation : la somme des montants ?
				association_ajouter_operation_comptable($date_don, $argent+$valeur, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:don'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_dons'], $journal, $id_don);
			} else { // sinon on en insere deux  (meme si les colis de valeur inconnue sont inseres afin de pouvoir les modifier ulterieurement)
				association_ajouter_operation_comptable($date_don, $argent, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:don'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_dons'], $journal, $id_don); // argent
				association_ajouter_operation_comptable($date_don, $valeur, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:colis'),'num'=>$id_don) ) .$ref_don, $GLOBALS['association_metas']['pc_colis'], '', $id_don); // colis
			}
		}
	}
	return array($id_don, $erreur);
}

?>