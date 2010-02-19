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

function bando_definir_barre_onglets($script) {
$onglets=array();

	// ajouter les onglets issus des plugin via plugin.xml
	if (function_exists('onglets_plugins')){
		$liste_onglets_plugins = onglets_plugins();

		foreach($liste_onglets_plugins as $id => $infos){
			if (($parent = $infos['parent'])
				&& $parent == $script
				&& autoriser('onglet',$id)) {
					$onglets[$id] = new Bouton(
					  find_in_theme($infos['icone']),  // icone
					  $infos['titre'],	// titre
					  $infos['url']?generer_url_ecrire($infos['url'],$infos['args']?$infos['args']:''):null
					  );
			}
		}
	}

	return pipeline('ajouter_onglets', array('data'=>$onglets,'args'=>$script));
}
// http://doc.spip.org/@barre_onglets
function bando_barre_onglets($rubrique, $ongletCourant){

	$res = '';

	foreach(bando_definir_barre_onglets($rubrique) as $exec => $onglet) {
		$url= $onglet->url ? $onglet->url : generer_url_ecrire($exec);
		$res .= onglet(_T($onglet->libelle), $url, $exec, $ongletCourant, $onglet->icone);
	}

	return  !$res ? '' : (debut_onglet() . $res . fin_onglet());
}
?>