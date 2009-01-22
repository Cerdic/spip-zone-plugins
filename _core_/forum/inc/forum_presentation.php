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

// http://doc.spip.org/@debut_cadre_forum
function debut_cadre_forum($icone='', $return = false, $fonction='', $titre = '', $id="", $class=""){
	$retour_aff = debut_cadre('forum', $icone, $fonction, $titre, $id, $class);

	if ($return) return $retour_aff; else echo_log('debut_cadre_forum',$retour_aff);
}

// http://doc.spip.org/@fin_cadre_forum
function fin_cadre_forum($return = false){
	$retour_aff = fin_cadre('forum');

	if ($return) return $retour_aff; else echo_log('fin_cadre_forum',$retour_aff);
}

// http://doc.spip.org/@debut_cadre_thread_forum
function debut_cadre_thread_forum($icone='', $return = false, $fonction='', $titre = '', $id="", $class=""){
	$retour_aff = debut_cadre('thread-forum', $icone, $fonction, $titre, $id, $class);

	if ($return) return $retour_aff; else echo_log('debut_cadre_thread_forum',$retour_aff);
}

// http://doc.spip.org/@fin_cadre_thread_forum
function fin_cadre_thread_forum($return = false){
	$retour_aff = fin_cadre('thread-forum');

	if ($return) return $retour_aff; else echo_log('fin_cadre_thread_forum',$retour_aff);
}



// http://doc.spip.org/@forum_logo
function forum_logo($statut)
{
	if ($statut == "prive") return "forum-interne-24.gif";
	else if ($statut == "privadm") return "forum-admin-24.gif";
	else if ($statut == "privrac") return "forum-interne-24.gif";
	else return "forum-public-24.gif";
}
