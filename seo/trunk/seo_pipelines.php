<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP\SEO\Pipelines
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_autoriser(){
}

function autoriser_seo_bouton_dist($faire, $type, $id, $qui, $opt){
	// seul les administrateurs globaux ont acces au bouton de configuration
	return autoriser('configurer','_seo');
}

/**
 * Insertion dans le squelette head/truc de Z si dispo
 * Remplace/ajoute des metas et title a celles du squelette
 *
 * @param string $flux
 *     Le contenu de la page
 * @return string
 *     Le contenu de la page modifiée
 */
function seo_recuperer_fond($flux){
	if (strncmp($flux['args']['fond'],"head/",5)==0
		AND strpos($flux['data']['texte'],"<!--seo_insere-->")===false
		AND strpos($flux['data']['texte'],"<title")!==false
		AND include_spip('inc/config')
		AND lire_config('seo/insert_head/activate', 'no')=="yes"
	){
		$flux['data']['texte'] = recuperer_fond("inclure/seo-head",array("head"=>$flux['data']['texte'],"contexte"=>$flux['args']['contexte']));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline affichage_final (SPIP)
 * Remplace/ajoute des metas et title a celles du <head>
 *
 * @param string $flux
 *     Le contenu de la page
 * @return string
 *     Le contenu de la page modifiée
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
		AND ($ps = stripos($flux,"<head>"))!==false
		AND ($pe = stripos($flux,"</head>"))!==false
		AND $pe>$ps
		AND $head = substr($flux,$ps+6,$pe-$ps-6)
		){
		$head_new = recuperer_fond("inclure/seo-head",array("head"=>$head,"contexte"=>$GLOBALS['contexte']));
		$flux = str_replace($head,$head_new,$flux);
	}
	#var_dump(spip_timer());
	return $flux;
}