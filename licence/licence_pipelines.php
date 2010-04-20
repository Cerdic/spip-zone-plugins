<?php
/*
 * Plugin Licence
 * (c) 2007-2010 fanouch
 * Distribue sous licence GPL
 *
 */

/**
 * Insertion au centre des pages d'articles dans le privé
 * Affiche un formulaire d'édition de la licence de l'article
 *
 * @param array $flux Le contexte du pipeline
 */
function licence_affiche_milieu($flux) {

	if ($flux['args']['exec'] == 'articles'){
		$contexte['id_article'] = $flux["args"]["id_article"];
		$flux['data'] .= debut_cadre_relief(_DIR_PLUGIN_LICENCE."/img_pack/licence_logo24.png", true, "");
		$flux['data'] .= "<div id='bloc_licence' class='ajax'>";
		$flux['data'] .= recuperer_fond('prive/contenu/licence_article',$contexte,array('ajax'=>true));
		$flux['data'] .= "</div>";
		$flux['data'] .= fin_cadre_relief(true);
	}
	return $flux;
}

/**
 * Si création d'un nouvel article, on lui attribue la licence par défaut si
 * on utilise correctement les fonctions internes de SPIP pour créer des articles
 * cf : http://trac.rezo.net/trac/spip/browser/branches/spip-2.1/ecrire/action/editer_article.php#L214
 *
 * @param array $flux Le contexte du pipeline
 */
function licence_pre_insertion($flux){
	// si creation d'un nouvel article lui attribuer la licence par defaut de la config
	if ($flux['args']['table']=='spip_articles') {
		$licence_defaut = lire_config('licence/licence_defaut');
		$flux['data']['id_licence'] = $licence_defaut;
	}
	return $flux;
}

?>
