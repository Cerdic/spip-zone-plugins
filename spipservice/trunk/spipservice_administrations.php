<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 3                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj des tables breves
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function spipservice_upgrade($nom_meta_base_version, $version_cible){
	spip_log("- Creation de la table 'spip_spipservice'", "spipservice");
	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_spipservice')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables breves
 *
 * @param string $nom_meta_base_version
 */
function spipservice_vider_tables($nom_meta_base_version) {
	spip_log("- Suppression de la table 'spip_spipservice'", "spipservice");
	sql_drop_table("spip_spipservice");
	effacer_meta($nom_meta_base_version);
}

?>
