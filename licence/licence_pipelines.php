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

function licence_formulaire_traiter($flux){
	// si creation d'un nouvel article lui attribuer la licence par defaut de la config
	if ($flux['args']['form']=='editer_article' AND $flux['args']['args'][0]=='new') {
		$id_article = $flux['data']['id_article'];
		$licence_defaut = lire_config('licence/licence_defaut');
		sql_updateq('spip_articles',array('id_licence'=>$licence_defaut),'id_article='.intval($id_article));
	}
	return $flux;
}

?>
