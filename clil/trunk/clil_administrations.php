<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Thèmes CLIL
 *
 * @plugin     Thèmes CLIL
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Clil\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
defined('_DIR_PLUGINS_AUTO') || define('_DIR_PLUGINS_AUTO', _DIR_PLUGINS.'auto/');

/**
 * Fonction d'installation et de mise à jour du plugin Thèmes CLIL.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function clil_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
							array('maj_tables', array('spip_clil_themes')),
							array('remplir_table_clil_themes', array()),
							array('sql_alter',"TABLE `spip_articles` ADD `code_clil` int(11) NOT NULL DEFAULT '0' AFTER `url_site`" ),
							);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Thèmes CLIL.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function clil_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_clil_themes");
	sql_alter("TABLE spip_articles DROP code_clil");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('clil_theme')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('clil_theme')));

	effacer_meta($nom_meta_base_version);
}

function remplir_table_clil_themes(){

	if ($f = find_in_path(_DIR_PLUGINS.'/clil/data/classification.csv'))
		$donnees_clil = $f;
	elseif  ($f = find_in_path(_DIR_PLUGINS_AUTO.'/clil/data/classification.csv'))
		$donnees_clil = $f;
	$import_csv = charger_fonction('importer_csv','inc');
	$csv = $import_csv($donnees_clil);
	foreach ($csv as $key => $sous_tab) {
		for ($i=0; $i < 4; $i++) {
			if (!empty($sous_tab[0])) $code_parent = $sous_tab[0];
			if (!empty($sous_tab[$i])) {
				sql_insertq('spip_clil_themes', array('code' => $sous_tab[$i], 'code_parent'=> $code_parent, 'niveau' => $i, 'libelle' =>  $sous_tab[4]));
			}
		}
	}
}

?>