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
			$result_num = sql_select("ordre", "spip_pb_selection", "id_rubrique=$id_rubrique", "ordre DESC LIMIT 0,1");
			$ordre = 0;
			if ($row_num = sql_fetch($result_num)) {
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

if ($_GET["remonter_ordre"] > 0) {

	$remonter = $_GET["remonter_ordre"];
	$id_rubrique = $_GET["id_rubrique"];

	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");
	
	$result = sql_select("*", "spip_pb_selection", "id_rubrique=$id_rubrique", "ordre");
	
	while ($row = sql_fetch($result)) {
		$article = $row["id_article"];
		$ordre = $row["ordre"];
		
		if ($article == $remonter) break;
		$ordre_prec = $ordre;
		$art_prec = $article;
	}
	
	sql_updateq("spip_pb_selection", array("ordre" => $ordre_prec), "id_rubrique = '$id_rubrique' AND id_article='$remonter'");
	sql_updateq("spip_pb_selection", array("ordre" => $ordre), "id_rubrique = '$id_rubrique' AND id_article='$art_prec'");
	
}

if ($_GET["descendre_ordre"] > 0) {

	$descendre = $_GET["descendre_ordre"];
	$id_rubrique = $_GET["id_rubrique"];

	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");

	$result = sql_select("ordre", "spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$descendre", "ordre");
	
	if ($row = sql_fetch($result)) {
		$ordre = $row["ordre"];
		
		$result2 = sql_select("*", "spip_pb_selection", "id_rubrique=$id_rubrique AND ordre>$ordre", "ordre LIMIT 0,1");
		if ($row2 = sql_fetch($result2)) {
			$ordre_suiv = $row2["ordre"];
			$art_suiv = $row2["id_article"];

			sql_updateq("spip_pb_selection", array("ordre" => $ordre_suiv), "id_rubrique = '$id_rubrique' AND id_article='$descendre'");
			sql_updateq("spip_pb_selection", array("ordre" => $ordre), "id_rubrique = '$id_rubrique' AND id_article='$art_suiv'");

		}
	
	}

}	



?>
