<?php
/**
 * Utilisations de pipelines par le plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2016-2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Inserer des contenus dans le <head> public
 *
 * Ajout des metas open graph, dublin core et twitter.
 * On ajoute directement du php pour éviter le cache commun, cf. https://contrib.spip.net/Dublin-Core#forum493303
 *
 * @param $flux
 * @return mixed
 */
function metasplus_insert_head($flux) {

	$flux .= '<' . '?php if (function_exists("metasplus_generer_head")) { metasplus_generer_head(); } ?' . '>';

	return $flux;
}
