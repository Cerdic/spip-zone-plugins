<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_plans() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$code = _request('code');
	$actif = _request('actif');
	$intitule = _request('intitule');
	$classe = _request('classe');
	$reference = _request('reference');
	$solde_anterieur = _request('solde_anterieur');
	$commentaire = _request('commentaire');
	$date_anterieure = _request('date_anterieure');
	if ($GLOBALS['association_metas']['comptes_stricts']=="on") {
		$direction = _request('direction');
	}
	else {
		$direction = '';
	}

	/* on verifie que le code n'est pas deja attribue a une ligne du plan */
	if (sql_fetsel('code', 'spip_asso_plan', "code=$code"))	{
		include_spip('inc/minipres');
		$url_retour = generer_url_ecrire('edit_plan');
		echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_code_plan').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
		exit;
	}

	plan_insert($actif, $intitule, $reference, $code, $solde_anterieur, $date_anterieure, $classe, $commentaire, $direction);

}


function plan_insert($actif, $intitule, $reference, $code, $solde_anterieur, $date_anterieure, $classe, $commentaire, $direction)
{
	include_spip('base/association');		

	$id_plan = sql_insertq('spip_asso_plan', array(
				'date_anterieure' => $date_anterieure,
				'actif' => $actif,
				'code' => $code,
				'intitule' => $intitule,
				'classe' => $classe,
				'reference' => $reference,
				'solde_anterieur' => $solde_anterieur,
				'commentaire' => $commentaire,
				'direction' => $direction));
}
?>
