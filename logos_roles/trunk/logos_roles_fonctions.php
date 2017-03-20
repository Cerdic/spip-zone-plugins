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


/**
 * Traitement automatique sur les logos. Permet de compléter le résultat des
 * balises #LOGO_* pour trouver les logos définis par rôles de documents.
 */
function trouver_logo_par_role($logo, $objet, $id_objet, $role) {

	if (! $logo) {
		$chercher_logo = charger_fonction('chercher_logo', 'inc/');
		$balise_img = charger_filtre('balise_img');

		$logo = $chercher_logo($id_objet, id_table_objet($objet), $role);
		$logo = $balise_img($logo[0]);
	}

	return $logo;
}