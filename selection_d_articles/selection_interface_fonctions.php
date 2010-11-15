<?php

	include_spip("inc/presentation");
	include_spip("inc/autoriser");
	include_spip("inc/puce_statut");
	
	define('_DIR_PB_REL', _DIR_RESTREINT ? '../' : '');

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SELECTION',(_DIR_PLUGINS.end($p)));



if ($_GET["ajouter_selection"] > 0) {
	$ajouter = $_GET["ajouter_selection"];
	$id_rubrique = $_GET["id_rubrique"];
	
	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");

	$result = sql_select("id_article", "spip_articles", "id_article=$ajouter");
	if ($row = sql_fetch($result)) {
		$result_test = sql_select("id_article", "spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$ajouter");
		if ($row_test = sql_fetch($result_test)) {
			echo "Cet article est déjà sélectionné.";
		} else {
			// Pas moyen de faire fonctionner le LIMIT 0,1 et l'ordre inverse avec sqlite
			$result_num = sql_select("ordre", "spip_pb_selection", "id_rubrique=$id_rubrique", "ordre");
			$ordre = 0;
			while ($row_num = sql_fetch($result_num)) {
				$ordre = $row_num["ordre"];
			}
			$ordre ++;
			sql_insertq("spip_pb_selection", array('id_rubrique' => $id_rubrique, 'id_article'=>$ajouter, 'ordre'=>$ordre));
			
		}

	} else {
		echo "Cet article n'existe pas.";
	}


}

if ($_GET["supprimer_ordre"] > 0) {
	$supprimer = $_GET["supprimer_ordre"];
	$id_rubrique = $_GET["id_rubrique"];
	
	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");
	sql_delete("spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$supprimer");

}




?>
