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

function inc_tester_maj_source() {
	global $maj_source;
	$boite = '';
	$invite = "<span class='verdana1'><b>"
	. _T('maj:bouton_maj_source')
	. "</b></span>";

	foreach($maj_source as $source) {
		if(function_exists($test = 'tester_'.$source) AND $test()) {
			$boite .= config_source($source);
		}
		else {
			$boite .= '<p>'._T('maj:'.$source.'_pas_ok').'</p>';
		}
	}
	
	return debut_cadre_relief(find_in_path('images/sources.png'), true) .
		block_parfois_visible('source',
		$invite,
		$boite,
		'text-align: center;',
		_request('source')!='') .
		fin_cadre_relief(true);
}

function config_source($source) {
	if($source == 'chargeur')
		return '<p>'._T('maj:chargeur_ok').'</p>';
	if($source == 'fichier')
		return '<p>'._T('maj:fichier_ok').'</p>';
	if($source != 'rss')
		return '<p>'.$source.'&nbsp;: OK</p>';
	return verifier_rss();
}

?>