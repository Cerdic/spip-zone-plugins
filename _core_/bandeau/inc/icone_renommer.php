<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/boutons');

function inc_icone_renommer_dist($fond,$fonction){
	$size = 24;
	if (preg_match("/-([0-9]{1,3})[.](gif|png)$/i",$fond,$match))
		$size = $match[1];
	$type = preg_replace("/(-[0-9]{1,3})?[.](gif|png)$/i","",$fond);

	$remplacement = array(
		'historique'=>'revisions',
		'secteur'=>'rubrique',
		'racine-site'=>'site',
		'mot-cle'=>'mot',
	);
	if (isset($remplacement[$type]))
		$type = $remplacement[$type];

	$dir = _DIR_PLUGIN_BANDO."images/v1/";
	$f = "$type-$size.png";
	if (file_exists($dir.$f)){
		$fond = $dir . $f;
		$action = "";
		if ($fonction=="supprimer.gif"){
			$action = "del";
		}
		if ($fonction=="creer.gif"){
			$action = "new";
		}
		if ($action
		  AND $fa = "$type-$action-$size.png"
		  AND file_exists($dir.$fa)){
			$fond = $dir . $fa;
			$fonction = "rien.gif";
		}
	}
	return array($fond,$fonction);
}
?>
