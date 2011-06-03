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

function action_editer_asso_plan() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_plan = $securiser_action();

	$code = _request('code');

	$active = _request('active');
	if ($active) { /* active est un booleen dans la base, et la request recupere l'etat de la checkbox */
		$active = true;
	} else { $active = false;}

	$intitule = _request('intitule');
	$classe = _request('classe');
	$reference = _request('reference');
	$solde_anterieur = _request('solde_anterieur');
	$commentaire = _request('commentaire');
	$date_anterieure = _request('date_anterieure');
	$type_op = _request('type_op');

	include_spip('base/association');

	if (!$id_plan) {/* c'est une insertion */
		$id_plan = sql_insertq('spip_asso_plan', array(
			'date_anterieure' => $date_anterieure,
			'active' => $active,
			'code' => $code,
			'intitule' => $intitule,
			'classe' => $classe,
			'solde_anterieur' => $solde_anterieur,
			'commentaire' => $commentaire,
			'type_op' => $type_op));
	} else { /* c'est un ajout */
		sql_updateq('spip_asso_plan', array(
			'date_anterieure' => $date_anterieure,
			'active' => $active,
			'code' => $code,
			'intitule' => $intitule,
			'classe' => $classe,
			'solde_anterieur' => $solde_anterieur,
			'commentaire' => $commentaire,
			'type_op' => $type_op),
		    "id_plan=$id_plan");

		$code_initial = _request('code_initial'); /* la fonction charger insere aussi le code initial dans une variable, on le recupere pour verifier si le code a ete modifie */
			
		if (($code_initial != '') && ($code_initial != $code)) {/* le code a ete modifie, il faut repercuter la modification dans la table des comptes */
			if ($code_initial[0]==$GLOBALS['association_metas']['classe_banques']) {
				$colonne = 'journal'; /* pour les comptes financiers, le code du compte est stocke dans la colonne journal */
			} else {
				$colonne = 'imputation'; /* sinon le code du compte est stocke dans la colonne imputation */
			}
			sql_updateq('spip_asso_comptes', array($colonne => $code), $colonne."=".$code_initial);
			/* il faut aussi verifier si ce code initial est utilise par un pc_XXX de la configuration afin de modifier la meta si besoin */
			switch ($code_initial) {
				case $GLOBALS['association_metas']['pc_cotisations']:
					ecrire_meta('pc_cotisations', $code, 'oui', 'association_metas');
					break;

				case $GLOBALS['association_metas']['pc_dons']:
					ecrire_meta('pc_dons', $code, 'oui', 'association_metas');
					break;

				case $GLOBALS['association_metas']['pc_ventes']:
					ecrire_meta('pc_ventes', $code, 'oui', 'association_metas');
					break;

				case $GLOBALS['association_metas']['pc_prets']:
					ecrire_meta('pc_prets', $code, 'oui', 'association_metas');
					break;

				case $GLOBALS['association_metas']['pc_activites']:
					ecrire_meta('pc_activites', $code, 'oui', 'association_metas');
					break;
			}
		}
	}

	/* si la gestion comptable est activee, on verifie que le plan comptable modifie est valide. Si il ne l'est pas, on desactive la gestion comptable */
	if ($GLOBALS['association_metas']['comptes']) {
		include_spip('inc/association_comptabilite');
		if (!association_valider_plan_comptable()) {
			ecrire_meta('comptes', '', 'oui', 'association_metas');
			/* on desactive les autres modules si ils sont actives */
			if ($GLOBALS['association_metas']['dons']) ecrire_meta('dons', '', 'oui', 'association_metas');
			if ($GLOBALS['association_metas']['ventes']) ecrire_meta('ventes', '', 'oui', 'association_metas');
			if ($GLOBALS['association_metas']['prets']) ecrire_meta('prets', '', 'oui', 'association_metas');
			if ($GLOBALS['association_metas']['activites']) ecrire_meta('activites', '', 'oui', 'association_metas');
		}
	}
	
	return array($id_plan, '');
}
?>
