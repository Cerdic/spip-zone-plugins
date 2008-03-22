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

// http://doc.spip.org/@exec_dater_dist
function exec_evdater_dist()
{
	$type = _request('type');
	$id = intval(_request('id'));

   if ((($GLOBALS['auteur_session']['statut'] != '0minirezo') AND ($GLOBALS['auteur_session']['statut'] != '1comite'))
	OR (!preg_match('/^\w+$/',$type))) { // securite 
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	//$table = ($type=='syndic') ? 'syndic' : ($type . 's');
	$row = spip_fetch_array(spip_query("SELECT * FROM spip_breves WHERE id_$type=$id"));

	$statut = $row['statut'];
	$date = $row[($type!='breve')?"date":"evento"];

	$script = ($type=='article')? 'articles' : ($type == 'breve' ? 'breves_voir' : 'sites');
   //
   $evdater = charger_fonction('breves_voir', 'exec');
   //
   @header("Location: ./?exec=breves_voir&id_breve=$id");

   //ajax_retour($evdater($id, 'ajax', $statut, $type, $script, $date, $date_redac));
   
}
?>
