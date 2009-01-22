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



// Moderer le forum ?
// = modifier l'objet correspondant (si forum attache a un objet)
// = droits par defaut sinon (admin complet pour moderation complete)
// http://doc.spip.org/@autoriser_modererforum_dist
function autoriser_modererforum_dist($faire, $type, $id, $qui, $opt) {
	return
		autoriser('modifier', $type, $id, $qui, $opt);
}


// Modifier un forum ?
// = jamais !
// http://doc.spip.org/@autoriser_forum_modifier_dist
function autoriser_forum_modifier_dist($faire, $type, $id, $qui, $opt) {
	return
		false;
}


function autoriser_bouton_forum_dist($faire, $type, $id, $qui, $opt){
	return 	($GLOBALS['meta']['forum_prive'] != 'non' OR sql_countsel('spip_forum'));
}

function autoriser_bouton_forum_admin_dist($faire, $type, $id, $qui, $opt){
	return 	($GLOBALS['meta']['forum_prive_admin'] == 'oui');
}

function autoriser_bouton_controle_forum_dist($faire, $type, $id, $qui, $opt){
	return 	(sql_countsel('spip_forum'));
}

?>