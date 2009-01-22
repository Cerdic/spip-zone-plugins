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

include_spip('inc/presentation');
include_spip('inc/statistiques');
include_spip('inc/statistiques_new');
function exec_flot_stats() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("Statistiques", "", "");
	echo '<div id="conteneur" style="width:600px;height:300px;border: 1px solid; margin: auto;"></div>';
	echo 'Total : '.$mtotal.'<br />';
	$select = sql_select("*", "spip_visites","","","`date` DESC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Aujourd\'hui : '.$ojr['visites'].'<br />';
					}
	$select = sql_select("*", "spip_visites","","","`visites` DESC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Maximum : '.$ojr['visites'].'<br />';
					}
	$select = sql_select("*", "spip_visites","visites!=0","","`visites` ASC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Minimum : '.$ojr['visites'].'<br />';
					}
	$mcompte = sql_countsel('spip_visites','visites!=0');
	$moyenne = $mtotal / $mcompte;
	$moyenne = round($moyenne);
	echo ' Moyenne : '.$moyenne;
	echo '<p><input id="clearSelection" type="button" value="Effacer la selection" />';
	echo '<p><input id="zoom" type="checkbox">Zoomer sur la selection</input></p>';
}
?>