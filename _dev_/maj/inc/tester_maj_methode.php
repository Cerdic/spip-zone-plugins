<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tester_maj_methode() {
	global $maj_methode;
	$boite = '';
	$invite = "<span class='verdana1'><b>"
	. _T('maj:bouton_maj_methode')
	. "</b></span>";

	foreach($maj_methode as $methode => $schema) {
		if(function_exists($test = 'tester_'.$methode) AND $test()) {
			$boite .= config_methode($methode);
		}
		else {
			$boite .= '<p>'._T('maj:'.$methode.'_pas_ok').'</p>';
		}
	}
	
	return debut_cadre_relief(find_in_path('images/actions.png'), true) .
		block_parfois_visible('methode',
		$invite,
		$boite,
		'text-align: center;',
		_request('verif')!='') .
		fin_cadre_relief(true);
}

function config_methode($methode) {
	if($methode == 'svn')
		return '<p>'._T('svn_ok').'</p>';
	if($methode == 'chargeur')
		return '<p>'._T('chargeur_ok').'</p>';
	if($methode != 'spip_loader')
		return '<p>'.$methode.'&nbsp;: OK</p>';
	return verifier_spip_loader();
}

?>