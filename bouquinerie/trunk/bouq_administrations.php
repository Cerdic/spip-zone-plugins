<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Bouquinerie
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Bouquinerie.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function bouq_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_livres', 'spip_livres_liens', 'spip_livres_auteurs', 'spip_livres_auteurs_liens')));

	$maj['1.0.1'] = array(
		array('sql_alter',"TABLE spip_livres CHANGE  `hauteur` `hauteur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `largeur` `largeur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `prix` `prix` FLOAT(6,2) NOT NULL DEFAULT 0"),
	);

	/* pour avoir un ISBN avec une écriture avec segments */
	$maj['1.0.2'] = array(
		array('sql_alter',"TABLE spip_livres CHANGE  `ISBN` `ISBN` VARCHAR(20) NOT NULL DEFAULT ''")
	);

	/* revenir à une ecriture minuscule */
	$maj['1.0.3'] = array(
		array('sql_alter',"TABLE spip_livres CHANGE  `ISBN` `isbn` VARCHAR(20) NOT NULL DEFAULT ''")
	);

	/* ajout de deux champs : sommaire et collection */
	$maj['1.0.4'] = array(
		array('sql_alter',"TABLE spip_livres ADD  `editeur` TEXT NOT NULL DEFAULT '' AFTER soustitre"),
		array('sql_alter',"TABLE spip_livres ADD  `collection` TEXT NOT NULL DEFAULT '' AFTER editeur"),
		array('sql_alter',"TABLE spip_livres ADD  `sommaire` TEXT NOT NULL DEFAULT '' AFTER texte"),
		array('bouq_init_metas')
	);

	/* ajout de deux champs : sommaire et collection */
	$maj['1.0.5'] = array(
		array('sql_alter',"TABLE spip_livres CHANGE  `hauteur` `hauteur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `largeur` `largeur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `prix` `prix` DECIMAL(20,6) NOT NULL DEFAULT 0"),
		array('bouq_maj_largeur_hauteur')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Avec la version 1.2 du plugin, on rend certains champs déjà existant optionnels.
 * Par défaut, la méta correspondante n'est pas renseignée, donc considérée comme inactive.
 * Lors de la mise à jour, on vérifie si le rédacteur du site a déjà renseigné certains de ces champs. Si oui, le champ devient actif.
 * 
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function bouq_init_metas() {
	$champs_text = array('soustitre','volume', 'edition', 'traduction', 'texte', 'extrait', 'infos_sup', 'isbn', 'reliure');
	$champs_num  = array('largeur', 'hauteur', 'poids', 'prix');

	// Livre : traiter les champs textes
	foreach ($champs_text as $value) {
		if (sql_countsel('spip_livres', "$value != ''") > 0) {
			ecrire_config("bouq/livres/$value", 'on');
		}
	}
	// Livre : traiter les champs numeriques
	foreach ($champs_num as $value) {
		if (sql_countsel('spip_livres', "$value > 0") > 0) {
			ecrire_config("bouq/livres/$value", 'on');
		}
	}
	// Livre : spécial pages
	if (sql_countsel('spip_livres', "pages IS NOT NULL") > 0) {
		ecrire_config("bouq/livres/pages", 'on');
	}

	// Auteur de livre
	if (sql_countsel('spip_livres_auteurs', "biographie != ''") > 0) {
		ecrire_config("bouq/auteurs/bio", 'on');
	}
	if (sql_countsel('spip_livres_auteurs', "lien_titre != ''") > 0) {
		ecrire_config("bouq/auteurs/site_auteur", 'on');
	}
}

function bouq_maj_largeur_hauteur() {
	$lignes = sql_select('id_livre, largeur, hauteur', 'spip_livres');
	while ($res = sql_fetch($lignes)) {
		if ($res['largeur'] == '0' AND $res['hauteur'] == '0') {
			sql_update('spip_livres', array('largeur' => "''", 'hauteur' => "''"), 'id_livre = '.$res['id_livre']);
		}
	}
}


/**
 * Fonction de désinstallation du plugin Bouquinerie.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function bouq_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_livres');
	sql_drop_table('spip_livres_liens');
	sql_drop_table('spip_livres_auteurs');
	sql_drop_table('spip_livres_auteurs_liens');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_mots_liens', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('livre', 'livres_auteur')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_forum', sql_in('objet', array('livre', 'livres_auteur')));

	effacer_meta($nom_meta_base_version);
}
