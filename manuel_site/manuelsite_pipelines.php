<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function manuelsite_pre_boucle($boucle) {
	$conf_manuelsite = lire_config('manuelsite');
	if (!test_espace_prive() && $conf_manuelsite["cacher_public"] && ($boucle->type_requete == 'articles') && $id=$conf_manuelsite["id_article"]) {
		$article = $boucle->id_table . '.id_article';
		$boucle->where[] = array("'!='", "'$article'", "$id");
	}
	return $boucle;
}

?>