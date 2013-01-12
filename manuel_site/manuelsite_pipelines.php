<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function manuelsite_pre_boucle($boucle) {
	if(!test_espace_prive() && ($boucle->type_requete == 'articles')){
		$article = $boucle->id_table . '.id_article';
		$boucle->where[] = array("'!='", "'$article'", "manuelsite_article_si_cacher()");
	}
	return $boucle;
}

?>