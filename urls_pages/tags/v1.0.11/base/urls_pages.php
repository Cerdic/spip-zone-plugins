<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


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
	$interfaces['table_des_traitements']['URL_PAGE'][]= 'url_page_personnalisee(%s)';
	return $interfaces;
}

?>
