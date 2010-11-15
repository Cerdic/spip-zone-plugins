<?php


function exec_selection_interface() {

if ($_GET["remonter_ordre"] > 0) {

	$remonter = $_GET["remonter_ordre"];
	$id_rubrique = $_GET["id_rubrique"];

	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");
	
	$result = sql_select("*", "spip_pb_selection", "id_rubrique=$id_rubrique", "", "ordre");
	
	while ($row = sql_fetch($result)) {
		$article = $row["id_article"];
		$ordre = $row["ordre"];
		
		
		if ($article == $remonter) break;
		else {
			$ordre_prec = $ordre;
			$art_prec = $article;
		}
	}


	sql_updateq("spip_pb_selection", array("ordre" => $ordre_prec), "id_rubrique = '$id_rubrique' AND id_article='$remonter'");
	sql_updateq("spip_pb_selection", array("ordre" => $ordre), "id_rubrique = '$id_rubrique' AND id_article='$art_prec'");
	
}

if ($_GET["descendre_ordre"] > 0) {

	$descendre = $_GET["descendre_ordre"];
	$id_rubrique = $_GET["id_rubrique"];

	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");

	$result = sql_select("ordre", "spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$descendre", "", "ordre");
	
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



		include_spip("inc/utils");
		include_spip("public/assembler");
		$contexte = array('id_rubrique'=>$_GET["id_rubrique"]);

		$p = evaluer_fond("selection_interface", $contexte);
		$ret .= $p["texte"];
		
		
		echo $ret;
		
}		


?>