<?php
/**
 * Fichier gérant l'installation et désinstallation par Rôles de sélctions éditoriales
 *
 * @plugin     Rôles de sélections éditoriales
 * @copyright  2018
 * @author     Mukt
 * @licence    GNU/GPL
 * @package    SPIP\Roles_selections\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Menus.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function roles_selections_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		// Supprimer la clé primaire actuelle pour pouvoir en changer en ajoutant la colonne rôle
		array('sql_alter', "TABLE spip_selections_liens DROP PRIMARY KEY"),
		// Ajout de la colonne role
		array('maj_tables', array('spip_selections_liens')),
		// La nouvelle colonne est la, mettre sa nouvelle clé primaire
		array('sql_alter', "TABLE spip_selections_liens ADD PRIMARY KEY (id_selection,id_objet,objet,role)"),
		// On passe tous les liens en rôle par défaut (selection)
		array('sql_updateq', 'spip_selections_liens', array('role' => 'selection'), array(
			'role=' . sql_quote('')
		))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Menus.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function roles_selections_vider_tables($nom_meta_base_version) {

	// Tant qu'il existe des doublons, on supprime une ligne doublonnée
	// sinon on ne pourra pas modifier la cle primaire ensuite
	// cet algo est certainement a optimiser
	while ($doublons = sql_allfetsel(
				array('id_selection', 'id_objet', 'objet', 'role'),
				array('spip_selections_liens'),
				'', 'id_selection,id_objet,objet', '', '', 'COUNT(*) > 1'))
	{
		foreach ($doublons as $d) {
			$where = array();
			foreach ($d as $cle=>$valeur) {
				$where[] = "$cle=".sql_quote($valeur);
			}
			sql_delete('spip_selections_liens', $where);
		}
	}

	// supprimer la clé primaire, la colonne rôle, et remettre l'ancienne clé primaire
	sql_alter("TABLE spip_selections_liens DROP PRIMARY KEY");
	sql_alter("TABLE spip_selections_liens DROP COLUMN role");
	sql_alter("TABLE spip_selections_liens ADD PRIMARY KEY (id_selection,id_objet,objet)");

	effacer_meta($nom_meta_base_version);
}

