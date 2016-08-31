<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('lib/iCalcreator.class'); /*pour la librairie icalcreator incluse dans le plugin icalendar*/
include_spip('action/editer_objet');
function action_appliquer_decalage_dist() {

//vérification de l'auteur en cours//
$securiser_action = charger_fonction('securiser_action', 'inc');
$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_appliquer_decalage_dist $arg pas compris");
		 return;
	}

	$id_almanach = $r[1];
	$liens = sql_allfetsel('E.uid, E.id_evenement',
		                        'spip_evenements AS E
		                        INNER JOIN spip_almanachs_liens AS L
		                        ON E.id_evenement = L.id_objet AND L.id_almanach='.intval($id_almanach),"E.horaire!=".sql_quote("non"));	

	$decalage = intval(sql_fetsel("decalage","spip_almanachs","id_almanach=$id_almanach"));
	
	$champs_sql = array(
		"date_debut" => "DATE_ADD(date_debut, INTERVAL  $decalage HOUR)",
		"date_fin" => "DATE_ADD(date_fin, INTERVAL  $decalage HOUR)",
	);
	foreach ($liens as $l){
		$id_evenement = intval($l["id_evenement"]);
		autoriser_exception('evenement','modifier',$id_evenement);
		objet_modifier('evenement',$id_evenement,$champs_sql);
		autoriser_exception('evenement','modifier',$id_evenement,false);
	}

}

?>