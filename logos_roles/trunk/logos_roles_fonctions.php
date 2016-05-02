<?php
/**
 * Fonctions utiles au plugin Logos par rôles
 *
 * @plugin     logos_roles
 * @copyright  2016
 * @author     bystrano
 * @licence    GNU/GPL
 */

/**
 * Lister les rôles de logos
 *
 * @return array : Un tableau décrivant les rôles de logos
 */
function lister_logos_roles() {

	$table_documents = lister_tables_objets_sql('spip_documents');

	$roles_logos = array();
	foreach ($table_documents['roles_titres'] as $role => $titre_role) {
		if (strpos($role, 'logo') === 0) {
			$roles_logos[$role] = $titre_role;
		}
	}

	return $roles_logos;
}

/**
 * Trouve l'identifiant du document associé à un fichier
 *
 * @param string $fichier : le nom du fichier
 *
 * @return integer : l'identifiant du document
 */
function trouver_document_fichier($fichier) {

	$fichier = str_replace(_DIR_IMG, '', $fichier);

	include_spip('base/abstract_sql');

	return sql_getfetsel('id_document', 'spip_documents', 'fichier='.sql_quote($fichier));
}
