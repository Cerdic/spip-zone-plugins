<?php
/**
 * Fonctions utiles au lugin Déclarer parent
 *
 * @plugin     Déclarer parent
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Declarerparent\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cherche le contenu parent d'un contenu précis
 *
 * @param string $objet
 * @param int|string $id_objet
 * @return array
 */
function filtre_objet_trouver_parent_dist($objet, $id_objet) {
	include_spip('base/objets_parents');
	return objet_trouver_parent($objet, $id_objet);
}
