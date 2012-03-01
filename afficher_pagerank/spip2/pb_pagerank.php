<?php



	function pb_pagerank_interface ( $vars="" ) {
		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$ret = "";
		
		if ($exec == "accueil") {
			
			$url_site = lire_meta("adresse_site");
			
			$pagerank = afficher_pagerank($url_site);
			
				
				$ret .= debut_cadre_enfonce("", true);
				$ret .= "<div style='padding-left: 60px;'>";
				$ret .= "<div class='verdana1'>PageRank : <b style='color: red;'>$pagerank</b></div>";
				$ret .= "<div><img src='"._DIR_PLUGIN_PAGERANK."/images/pagerank$pagerank.gif' alt='pagerank $pagerank' /></div>";
				$ret .= "</div>";
				$ret .= fin_cadre_enfonce(true);
			
		} 
		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
	}

	function pb_pagerank_header($flux) {
		if ($_GET["exec"] == "statistiques_referers" || $_GET["exec"] == "statistiques_visites") {
			$flux .= "<script src='../?page=pagerank_prive' type='text/javascript'></script>";
		} 

		return $flux;


	}


?>