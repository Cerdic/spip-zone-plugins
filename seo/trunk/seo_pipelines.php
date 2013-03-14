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

	#$t = spip_timer();
	/**
	 * On n'agit que si on a un head
	 * sinon c'est tout et n'importe quoi
	 */
	if ($GLOBALS['html']
		AND stripos($flux,'<head>')!==false
	  AND include_spip('inc/config')
		AND lire_config('seo/forcer_squelette', 'no')=="yes"
		AND preg_match('/<head>(.*)<\/head>/Uims', $flux, $head)){
		$head = $head[1];

		$head_new = recuperer_fond("inclure/seo-head",array("head"=>$head,"contexte"=>$GLOBALS['contexte']));
		$flux = str_replace($head,$head_new,$flux);
	}
	#var_dump(spip_timer());
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
				$flux .= implode("\n",generer_meta_tags($meta_tags));
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

