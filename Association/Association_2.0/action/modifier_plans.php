<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_modifier_plans() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_plan=$securiser_action();

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
	/* on verifie que le code n'est pas deja attribue a une ligne du plan ou si il l'est que c'est a celle qu'on edite*/
	if ($r = sql_fetsel('code,id_plan', 'spip_asso_plan', "code=$code"))	{
		if ($r['id_plan']!=$id_plan)
		{
			include_spip('inc/minipres');
			$url_retour = generer_url_ecrire('edit_plan', "id=$id_plan");
			echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_code_plan').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
			exit;
		}
	}

	include_spip('base/association');
	sql_updateq('spip_asso_plan', array(
				'date_anterieure' => $date_anterieure,
				'actif' => $actif,
				'code' => $code,
				'intitule' => $intitule,
				'classe' => $classe,
				'reference' => $reference,
				'solde_anterieur' => $solde_anterieur,
				'commentaire' => $commentaire,
				'direction' => $direction),
		    "id_plan=$id_plan");
}
?>
