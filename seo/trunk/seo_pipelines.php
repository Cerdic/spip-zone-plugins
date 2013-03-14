<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP_SEO
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_autoriser(){
}

function autoriser_seo_bouton_dist($faire, $type, $id, $qui, $opt){
	global $connect_statut, $connect_toutes_rubriques;

	// seul les administrateurs globaux ont acces au bouton de configuration
	return $connect_statut && $connect_toutes_rubriques;
}


/**
 * Insertion dans le pipeline affichage_final (SPIP)
 * Remplacement des métas et title dans le <head>
 * Remplace les métas et title du squelette et celles insérées via insert_head également
 *
 * @param string $flux
 *     Le contenu de la page
 * @return string
 *     Le contenu de la page modifié
 */
function seo_affichage_final($flux){
	preg_match('/<head>(.*)<\/head>/mis', $flux, $head);
	$head = isset($head[1]) ? $head[1] : false;

	/**
	 * On n'agit que si on a un head
	 * sinon c'est tout et n'importe quoi
	 */
	if ($head){
		/**
		 * Pour lire_config
		 */
		include_spip('inc/config');

		$forcer_squelette = lire_config('seo/forcer_squelette', 'no');
		if ($forcer_squelette!='yes')
			return $flux;

		include_spip('seo_fonctions');

		$meta_tags = calculer_meta_tags();

		foreach ($meta_tags as $key => $value){
			$meta = generer_meta_tags(array($key => $value));
			$flux_meta = '';
			/**
			 * Si le tag est <title>
			 */
			if ($key=='title')
				$flux_meta = preg_replace("/(<\s*$key.*?>.*?<\/\s*$key.*?>)/mi", $meta, $flux, 1);
			/**
			 * Le tag est une <meta>
			 */
			else
				$flux_meta = preg_replace("/(<\s*meta\s*name=\"$key\"\s*content=\".*?\".*?>)/mi", $meta, $flux, 1);

			/**
			 * Si $flux == $flux_meta
			 * C'est que _SEO_FORCER_SQUELETTE est placé
			 * On ajoute les metas juste avant </head>
			 */
			if ($flux==$flux_meta)
				$flux_meta = str_replace('</head>', "\n" . $meta . "</head>", $flux);

			$flux = $flux_meta;
		}
	}
	return $flux;
}


/**
 * Inserer les meta dans le head
 *
 * @param string $flux
 * @return string
 */
function seo_insert_head($flux){
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);
	if (isset($config['insert_head']) && $config['insert_head']['activate']=='yes'){
		$contexte = $GLOBALS['contexte'];
		unset($contexte['lang']);
		unset($contexte['date']);
		unset($contexte['date_default']);
		unset($contexte['date_redac']);
		unset($contexte['date_redac_default']);
		unset($contexte['lang']);
		if (count($contexte)==0){
			$objet = 'sommaire';
		} elseif (isset($contexte['id_article'])) {
			$id_objet = $contexte['id_article'];
			$objet = 'article';
		} elseif (isset($contexte['id_rubrique'])) {
			$id_objet = $contexte['id_rubrique'];
			$objet = 'rubrique';
		}
		/* META TAGS */
		if (isset($config['meta_tags']) && $config['meta_tags']['activate']=='yes'){
			if (!defined('_SEO_FORCER_SQUELETTE')){
				$meta_tags = calculer_meta_tags();
				$flux .= generer_meta_tags($meta_tags);
			}
		}
		/* META GOOGLE WEBMASTER TOOLS */
		if (isset($config['webmaster_tools']) && $config['webmaster_tools']['activate']=='yes' && $objet=='sommaire'){
			$flux .= generer_webmaster_tools();
		}

		if (isset($config['bing']) && $config['bing']['activate']=='yes' && $objet=='sommaire'){
			$flux .= generer_bing();
		}

		/* CANONICAL URL */
		if (isset($config['canonical_url']) && $config['canonical_url']['activate']=='yes'){
			$flux .= generer_urls_canoniques();
		}

		/* GOOGLE ANALYTICS */
		if (isset($config['analytics']) && $config['analytics']['activate']=='yes'){
			$flux .= generer_google_analytics();
		}

		/* ALEXA */
		if (isset($config['alexa']) && $config['alexa']['activate']=='yes' && $objet=='sommaire'){
			$flux .= generer_alexa();
		}
	}

	return $flux;
}

