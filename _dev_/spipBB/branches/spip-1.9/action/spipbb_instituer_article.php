<?php
/* base : 
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# h.30/11 .. ok !

// http://doc.spip.org/@action_instituer_article_dist
function action_spipbb_instituer_article_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_article, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_article = intval($id_article);

	include_spip('action/spipbb_editer_article');

	instituer_article($id_article, array('statut' => $statut));

}

?>
