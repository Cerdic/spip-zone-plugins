<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_selection_interface() {
	$id_rubrique = _request("id_rubrique");
	if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");



	// Bouton REMONTER
	if ($_GET["remonter_ordre"] > 0) {
	
		$remonter = _request("remonter_ordre");
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
		$descendre = _request("descendre_ordre");
	
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

	if ($_GET["ajouter_selection"] > 0) {
		$ajouter = _request("ajouter_selection");
		
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
		$supprimer = _request("supprimer_ordre");
		
		if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");
		sql_delete("spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$supprimer");
	
	}
	
	if ($_GET["nouvel_ordre"]) {
		$nouvel_ordre = explode(",", $_GET["nouvel_ordre"]);
		if (count($nouvel_ordre) > 0) {
			sql_delete("spip_pb_selection", "id_rubrique=$id_rubrique");
			$ordre = 0;
			foreach($nouvel_ordre AS $id_article) {
				$ordre++;
				$id_article = substr($id_article, 9, 1000);
					sql_insertq(
						"spip_pb_selection", 
						array(
							'id_rubrique' => $id_rubrique, 
							'id_article'=>$id_article, 
							'ordre'=>$ordre)
						);
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