<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 * 
 * @package SPIP\SPIPmotion\Options
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Charger les metas de spipmotion
 */
if(!isset($GLOBALS['spipmotion_metas']) OR !is_array($GLOBALS['spipmotion_metas'])){
	$inc_meta = charger_fonction('meta', 'inc');
	$inc_meta('spipmotion_metas');
}
?>
