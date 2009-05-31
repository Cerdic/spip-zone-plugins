<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/config');

function  configuration_bloc_champs_supp()
{
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);

	$requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
	$definis =array();
	foreach($GLOBALS['champs_sap_spipbb'] as $k => $v) { $definis[]=$k; }
	$montre = array_diff($definis,$requis);

	$res.= "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='verdana2'>\n";

	foreach($montre as $chp) {
		# champs X
		$chp_low=strtolower($chp); // on passe en minuscules pour que #CONFIG puisse y avoir acces
		
		$res.= "<tr><td style='padding-bottom:1em;'>"._T('spipbb:config_affiche_champ_extra',array('nom_champ'=>$chp)).'<br />'
			. $GLOBALS['champs_sap_spipbb'][$chp]['info']
			. "</td><td width='25%' style='padding-bottom:1em;'>\n"
		
			. afficher_choix('affiche_'.$chp_low,
			($GLOBALS['spipbb']['affiche_'.$chp_low])? $GLOBALS['spipbb']['affiche_'.$chp_low]:'oui',
			array(
				'oui' => _T('item_oui'),
				'non' => _T('item_non')
			), " &nbsp; " )

			. "</td></tr>\n";
	}

	$res.= "</table>\n";
	
	return $res;
}

function configuration_spipbb_champs_supp_dist()
{
	$res = configuration_bloc_champs_supp();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:config_affiche_extra'))
	. ajax_action_post('spipbb_configurer', 'spipbb_champs_supp', 'configuration','',$res)
	. fin_cadre_relief(true);

	return ajax_action_greffe('spipbb_configurer-spipbb_champs_supp','', $res);
}
?>