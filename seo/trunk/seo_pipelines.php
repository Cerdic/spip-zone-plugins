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


function seo_recuperer_fond($flux){
	if (strncmp($flux['args']['fond'],"head/",5)==0
	  AND strpos($flux['data']['texte'],"<!--seo_insere-->")===false
	  AND include_spip('inc/config')
	  AND lire_config('seo/insert_head/activate', 'no')=="yes"
	){
		$flux['data']['texte'] = recuperer_fond("inclure/seo-head",array("head"=>$flux['data']['texte'],"contexte"=>$flux['args']['contexte']));
	}
	return $flux;
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
		AND strpos($flux,"<!--seo_insere-->")===false
	  AND include_spip('inc/config')
		AND lire_config('seo/insert_head/activate', 'no')=="yes"
		AND preg_match('/<head>(.*)<\/head>/Uims', $flux, $head)){
		$head_new = recuperer_fond("inclure/seo-head",array("head"=>$head[1],"contexte"=>$GLOBALS['contexte']));
		$flux = str_replace($head[1],$head_new,$flux);
	}
	#var_dump(spip_timer());
	return $flux;
}