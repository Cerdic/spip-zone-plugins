<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin produits
 *
 * @plugin	   produits
 * @copyright  2014
 * @author	   Arterrien
 * @licence	   GNU/GPL
 * @package	   SPIP\Produits\Installation
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin produits.
 *
 * @param string $nom_meta_base_version
 *	   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *	   Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 * */
function produits_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_produits'))
	);

	$maj['1.1.0'] = array(
		// ajout des champs immateriel/poids/longueur/largeur/hauteur
		array('maj_tables', array('spip_produits')),
	);
	$maj['1.1.1'] = array(
		// ajout des champs immateriel/poids/longueur/largeur/hauteur
		array('maj_tables', array('spip_produits')),
		array('sql_alter', 'TABLE spip_produits CHANGE taxe taxe DECIMAL(4,4) NULL DEFAULT NULL')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin produits.
 *
 * @param string $nom_meta_base_version
 *	   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 * */
function produits_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_produits');

	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('produit')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('produit')));

	effacer_meta($nom_meta_base_version);
}
