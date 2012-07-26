<?php
/**
 * Plugin Rôles d&#039;auteurs
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function roles_auteurs_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		// supprimer la clé primaire actuelle pour pouvoir en changer en ajoutant la colonne rôle
		array('sql_alter', "TABLE spip_auteurs_liens DROP PRIMARY KEY"),
		// ajout de la colonne role
		array('maj_tables', array('spip_auteurs_liens')),
		// la nouvelle colonne est la, mettre sa nouvelle clé primaire
		array('sql_alter', "TABLE spip_auteurs_liens ADD PRIMARY KEY (id_auteur,id_objet,objet,role)"),
		// on passe par défaut tous les liens auteurs/articles en rôle : redacteur
		array('sql_updateq', 'spip_auteurs_liens', array('role' => 'redacteur'), array(
			'role=' . sql_quote(''),
			'objet=' . sql_quote('article')
		))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function roles_auteurs_vider_tables($nom_meta_base_version) {

	// tant qu'il existe des doublons, on supprime une ligne doublonnée
	// sinon on ne pourra pas modifier la cle primaire ensuite
	// cet algo est certainement a optimiser
	while ($doublons = sql_allfetsel(
				array('id_auteur', 'id_objet', 'objet', 'role'),
				array('spip_auteurs_liens'),
				'', 'id_auteur,id_objet,objet', '', '', 'COUNT(*) > 1'))
	{
		foreach ($doublons as $d) {
			$where = array();
			foreach ($d as $cle=>$valeur) {
				$where[] = "$cle=".sql_quote($valeur);
			}
			sql_delete('spip_auteurs_liens', $where);
		}
	}

	// supprimer la clé primaire, la colonne rôle, et remettre l'ancienne clé primaire
	sql_alter("TABLE spip_auteurs_liens DROP PRIMARY KEY");
	sql_alter("TABLE spip_auteurs_liens DROP COLUMN role");
	sql_alter("TABLE spip_auteurs_liens ADD PRIMARY KEY (id_auteur,id_objet,objet)");

	effacer_meta($nom_meta_base_version);
}

?>
