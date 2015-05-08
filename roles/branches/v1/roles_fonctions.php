<?php
/**
 * Plugin Rôles
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/roles');

/**
 * Retrouve la traduction d'un rôle dans un objet donné 
 *
 * @param string $role
 *     Le role dans la base de donnée
 * @param string $objet
 *     L'objet sur lequel est le rôle
 * @return string
 *     Le texte du rôle dans la langue en cours
 * 
**/
function filtre_role_dist($role, $objet) {
	if (!$role) return '';
	if (!$objet) return $role;
	$roles = roles_presents(table_objet($objet));
	if (isset($roles['titres'][$role])) {
		return _T($roles['titres'][$role]);
	}
	return $role;
}
