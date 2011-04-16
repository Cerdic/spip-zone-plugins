<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin changestatut
 */

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function changestatut_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/abstract_sql');
			if(sql_alter("TABLE spip_auteurs ADD statut_orig varchar(255)  DEFAULT '' NOT NULL")) {
			
				$config = lire_config('changestatut');
				if (!is_array($config)) {
					$config = array();
				}
				$config = array_merge(array(
						'statut' => 'webmestre'
				), $config);
				ecrire_meta('changestatut', serialize($config));
	
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			} else {
				echo  sql_error()."<br/>";
			}
		}
	}
}


/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function changestatut_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	// On remet le webmestre d'equerre au cas ou
	sql_updateq("spip_auteurs", array("statut" => "0minirezo","webmestre" => "oui"), "id_auteur=".intval($GLOBALS['visiteur_session']['id_auteur']));
	sql_alter("TABLE spip_auteurs DROP statut_orig");
	effacer_meta('changestatut');
	effacer_meta($nom_meta_base_version);
}

function changestatut_declarer_tables_principales($tables_principales){
	$tables_principales['spip_auteurs']['field']['statut_orig'] = "varchar(255)  DEFAULT '' NOT NULL";
	return $tables_principales;
}
?>