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

// http://doc.spip.org/@exec_referencer_traduction_dist
function exec_referencer_traduction_dist()
{
	$type = _request('type');
	if (!in_array($type,array('article','rubrique','breve','site')))
		$type='article';
	$id = intval(_request('id_'.$type));

	if (!autoriser('modifier',$type,$id)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$row = spip_fetch_array(spip_query("SELECT id_trad, id_rubrique FROM spip_".$type."s WHERE id_".$type."=$id"));

	$referencer_traduction = charger_fonction('referencer_traduction', 'inc');
	ajax_retour($referencer_traduction($id, 'ajax', $row['id_rubrique'], $row['id_trad'],'',$type));
}
?>
