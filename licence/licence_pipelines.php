<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function licence_affiche_milieu($flux) {

	if ($flux['args']['exec'] == 'articles'){
		$contexte['id_article'] = $flux["args"]["id_article"];
		$flux['data'] .= debut_cadre_relief("../"._DIR_PLUGIN_LICENCE."/img_pack/licence_logo24.png", true, "");
		$flux['data'] .= "<div id='bloc_licence' class='ajax'>";
		$flux['data'] .= recuperer_fond('prive/contenu/licence_article',$contexte,array('ajax'=>true));
		$flux['data'] .= "</div>";
		$flux['data'] .= fin_cadre_relief(true);
	}
	return $flux;
}


?>
