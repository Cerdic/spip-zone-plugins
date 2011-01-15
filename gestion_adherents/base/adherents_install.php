<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

// Installation et mise à jour
function adherents_upgrade($nom_meta_version_base, $version_cible){
	$version_actuelle = '0.0';
echo '	$nom_meta_version_base: '.$nom_meta_version_base;
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/adherents_base');
			
			creer_base();
			if (mysql_error() == '') {
				echo "Installation du plugin Gestion adherents<br/>";
				ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
			}
			else echo "Erreur MySQL a l'installation du plugin Gestion adherents: ".mysql_error()."<br/>";
		}

/* on se garde sous le coude un exemple de code pour mise a jour de version...

		if (version_compare($version_actuelle,'0.5','<')){
			include_spip('base/abstract_sql');
			
			// exemple de modif d'une table existante
			sql_alter("TABLE spip_fichiers ADD COLUMN css tinytext DEFAULT '' NOT NULL");
		}
		
		// On change la version
		echo "Mise à jour du plugin menus en version $version_cible<br/>";
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
*/
	}
}

// Désinstallation
function fichier_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_adherents');
		
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
}
?>
