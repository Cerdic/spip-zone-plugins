<?php


function debut_fin_affiche_milieu($flux) {
	$exec = $flux["args"]["exec"];
	$id_article = $flux["args"]["id_article"];
	
	if ($exec == "articles" && autoriser('modifier', 'article', $id_article)) {
		$data = $flux["data"];
		
		
		$ret = recuperer_fond("prive/interface_debut_fin", array("id_article"=>$id_article));
		$ret = "<div class='ajax'>$ret</div>";
		
		$flux["data"] = $data.$ret;
		
		
		
	}
	
	return $flux;
}



?>