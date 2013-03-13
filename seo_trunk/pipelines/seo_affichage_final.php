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

