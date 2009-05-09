<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

/**
 * Definir l'article d'accueil d'une rubrique
 * depuis un article
 *
 * $arg au format id_rubrique-id_article
 */
function action_accueillir_rubrique_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match(",^([0-9]+)-([0-9]+)$,",$arg,$regs)){
		$id_rubrique = intval($regs[1]);
		$id_article = intval($regs[2]);
		if (autoriser('styliser','rubrique',$id_rubrique))
			sql_updateq("spip_rubriques",array("id_article_accueil"=>$id_article),"id_rubrique=".intval($id_rubrique));
	}

}

?>