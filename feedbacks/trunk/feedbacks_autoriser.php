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

// pour le pipeline d'autorisation
function feedbacks_autoriser(){}


// bouton du bandeau
function autoriser_feedbacks_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return 	($GLOBALS['meta']["activer_feedbacks"] != "non");
}
function autoriser_feedbackcreer_menu_dist($faire, $type, $id, $qui, $opt){
	return 	($GLOBALS['meta']["activer_feedbacks"] != "non");
}



// Autoriser a creer une feedback dans la rubrique $id
// http://doc.spip.org/@autoriser_rubrique_creerfeedbackdans_dist
function autoriser_rubrique_creerfeedbackdans_dist($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=".intval($id));
	return
		$id
		AND ($r['id_parent']==0)
		AND ($GLOBALS['meta']["activer_feedbacks"]!="non")
		AND autoriser('voir','rubrique',$id);
}


// Autoriser a modifier la feedback $id
// = admins & redac si la feedback n'est pas publiee
// = admins de rubrique parente si publiee
// http://doc.spip.org/@autoriser_feedback_modifier_dist
function autoriser_feedback_modifier_dist($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel("id_rubrique,statut", "spip_feedbacks", "id_feedback=".intval($id));
	return
		$r AND (
		($r['statut'] == 'publie' OR (isset($opt['statut']) AND $opt['statut']=='publie'))
			? autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
			: in_array($qui['statut'], array('0minirezo', '1comite'))
		);
}


?>