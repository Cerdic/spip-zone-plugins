<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_instituer_syndic_dist
function action_auteur_publi() {
	include_spip('inc/publiHAL_gestion');
	// controles de sécurité semble-t-il 
	include_spip('inc/actions');
	$var_f = charger_fonction('controler_action_auteur', 'inc');
	$var_f();
	// récupère $arg dans redirige_action_auteur($action, $arg, $ret, $gra='', $mode=false, $atts='')
	$arg = _request('arg');
	// décode l'argument '$id_syndic_article-refuse'
	$id_mot = intval($arg);
	if(isset($GLOBALS['meta']['publiHAL_auteurs_publi'])){
		$id_groupe=$GLOBALS['meta']['publiHAL_auteurs_publi'];
		$row = spip_fetch_array(spip_query("SELECT descriptif FROM spip_mots WHERE id_groupe=$id_groupe AND id_mot=$id_mot"));
		if(!$row) {
			spip_log('--- pas de description !!! pour auteur publi ---');
		}
		$descriptif=$row['descriptif'];
		$req="SELECT id_syndic_article, lesauteurs FROM spip_syndic_articles";
		$result=spip_query($req);
		while($row=spip_fetch_array($result)){
			$id_syndic_article=$row['id_syndic_article'];
			$lesauteurs=$row['lesauteurs'];
			//function publiHAL_met_mot_si_auteur_publi($id_syndic_article,$descriptif,$auteurs,$id_mot)
			publiHAL_met_mot_si_auteur_publi($id_syndic_article,$descriptif,$lesauteurs,$id_mot);
		}	
	}
	spip_log("fin action_auteur_publi----------------------");
		
}
?>
