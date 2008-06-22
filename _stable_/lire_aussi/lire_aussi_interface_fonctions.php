<?php
	include_spip("inc/presentation");
	include_spip("inc/autoriser");


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_LIRE_AUSSI',(_DIR_PLUGINS.end($p)));
	
	
if ($_GET["nouvelle_ref_lire"] > 0) {
		$nouv = $_GET["nouvelle_ref_lire"];
		$id_article = $_GET["id_article"];
		$id_lire = $_GET["id_lire"];

	if (!autoriser('modifier','article', $id_lire)) die ("Interdit");

		sql_updateq("spip_articles", array("id_lire" => $nouv), "id_lire = $id_lire ");

}

if ($_GET["supprimer_lire"] > 0) {
		$supprimer = $_GET["supprimer_lire"];
		$id_article = $_GET["id_article"];
		$id_lire = $_GET["id_lire"];

	if (!autoriser('modifier','article', $id_article)) die ("Interdit");


		sql_updateq("spip_articles", array("id_lire" => 0), "id_article = $supprimer ");

		$n = sql_countsel('spip_articles', "id_lire=$id_lire");
		if ($n < 2) {
			sql_updateq("spip_articles", array("id_lire" => 0), "id_lire = $id_lire ");
		}

	}
if ($_GET["ajouter_lire"] > 0) {
		$ajouter= $_GET["ajouter_lire"];
		$id_article = $_GET["id_article"];
		$id_lire = $_GET["id_lire"];

	if (!autoriser('modifier','article', $id_article)) die ("Interdit");

//	echo "$ajouter / $id_article / $id_lire";

	$result = sql_select("id_lire", "spip_articles", "id_article=$ajouter");
	
	if ($row = sql_fetch($result)) {
		$id_lire_nouv = $row["id_lire"];
//		echo " [$id_lire_nouv]";
	}

	if ($id_lire_nouv > 0) {
		if ($id_lire > 0) {
			sql_updateq("spip_articles", array("id_lire" => $id_lire_nouv), "id_lire = '$id_lire'");
		} else {
			sql_updateq("spip_articles", array("id_lire" => $id_lire_nouv), "id_article = '$id_article'");
		}
	} else {
		if (!autoriser('modifier','article', $ajouter)) die ("Interdit");

		if ($id_lire > 0) {
			sql_updateq("spip_articles", array("id_lire" => $id_lire), "id_article = '$ajouter'");
		} else {
			sql_updateq("spip_articles", array("id_lire" => $id_article), "id_article = $ajouter");
			sql_updateq("spip_articles", array("id_lire" => $id_article), "id_article = $id_article");
		}
	}

	
}
?>