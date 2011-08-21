<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/editer_select');

function inc_chat_select_dist($id_chat, $id_rubrique=0, $lier_trad=0) {
	return select_objet('chat', $id_chat, $id_rubrique, $lier_trad, 'nom');
}

// fonction facultative si pas de changement dans les traitements
function inc_chat_select_trad_dist($id_chat, $id_rubrique=0, $lier_trad=0) {
	return select_objet_trad('chat', $id_chat, $id_rubrique, $lier_trad, 'nom');
}



?>
