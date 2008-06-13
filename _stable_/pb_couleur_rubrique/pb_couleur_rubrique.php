<?php


function pb_couleur_rubrique_gauche($vars){



		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$ret = "";
		
		if ($exec == "naviguer" && $id_rubrique > 0 && $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

			$deplier = true;
			$pb_couleur_rubrique = pb_couleur_rubrique($id_rubrique);
			if (!$pb_couleur_rubrique) {
				$pb_couleur_rubrique = "999999";
				$deplier = false;
			}
			

			
		
			$ret .= debut_cadre_enfonce(_DIR_PLUGIN_PB_COULEUR_RUBRIQUE."img_pack/icon.png", true, "rien.gif", bouton_block_depliable("Couleur de la rubrique", $deplier, "selection_couleur"));

			$ret .= debut_block_depliable($deplier, "selection_couleur");
		    $ret .= "<form method='post' action='index.php?exec=naviguer&id_rubrique=$id_rubrique'>";

			$ret .= "<div id='picker' style='margin-left: -5px;'></div>";

			$ret .= "<div style='float: right;'><input class='fondo' type='submit' value='"._L("Enregistrer")."' /></div>";
			$ret .= "<input type='text' id='pb_couleur_rubrique' name='pb_couleur_rubrique' value='#$pb_couleur_rubrique'  class='colorwell' /><br />\n"; 
			if ($deplier) {
				$ret .= "<input type='submit' class='fondl' name='supprimer' value='Supprimer la couleur' />";
			}
			
			$ret .= "</form>";
			$ret .= fin_block();
			$ret .= fin_cadre_enfonce(true);



    
    
    

			
		}


		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
}



function pb_couleur_rubrique_header($flux){
	$flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('farbtastic/farbtastic.css'))."' />\n";     
	$flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic.js'))."' type=\"text/javascript\"></script>\n";
	$flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic_go.js'))."' type=\"text/javascript\"></script>\n";
	return $flux;

}

?>