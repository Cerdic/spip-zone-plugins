<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     urls_pages
 * @copyright  2013
 * @author     Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\urls_pages\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function urls_pages_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_traitements']['URL_PAGE'][]= 'url_perso(%s)';
	return $interfaces;
}

?>
