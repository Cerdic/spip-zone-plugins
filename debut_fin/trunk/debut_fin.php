<?php


function debut_fin_affiche_milieu($flux) {
	$exec = $flux["args"]["exec"];
	$id_article = $flux["args"]["id_article"];
	
	if ($exec == "article" && autoriser('modifier', 'article', $id_article)) {
		$ret = recuperer_fond("prive/interface_debut_fin", array("id_article"=>$id_article));
		$ret = "<div class='ajax'>$ret</div>";
        if ($p=strpos($flux['data'],'<!--affiche_milieu-->'))
			$flux['data'] = substr_replace($flux['data'],$ret,$p,0);
        else
			$flux['data'] .= $ret;
	}
	
	return $flux;
}



?>
