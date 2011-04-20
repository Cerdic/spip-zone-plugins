<?php

	/**
	* Plugin Bannières
	*
	* Copyright (c) 2009
	* François de Montlivault - Jeannot
	* Mise à jour Inspirée du plugin chats
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/


include_spip('inc/meta');
include_spip('base/create');

function bannieres_upgrade($nom_meta_base_version, $version_cible){


	if (isset($GLOBALS['meta']['ban_base_version'])){

		$current_version = $GLOBALS['meta']['ban_base_version'];
		
		} else {
			$current_version = "0.0";
		}

	// Nouvelle installation
	if ($current_version=="0.0") {
		creer_base();
		echo 'Bases du plugin banni&egrave;res install&eacute;es.';
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

	// Mise a jour depuis la 0.11
	if (version_compare($current_version,$version_cible,"<")){
	
		// on met a jour l'ancienne table
		maj_tables('spip_bannieres');
		
		// On cree les nouvelles
		creer_base();
		
		/*
		 * On renomme les anciennes bannieres --> attention, si il y avait
		 * plusieurs images avec le même nom mais pas la même extension
		 */
		renommer_bandeaux();
		
		// on n'a plus besoin de l'ancienne config CFG
		effacer_meta('bannieres');
		
		// effacer les ancien meta. Ils portent maintenant le nom du plugin (bannieres_version).
		effacer_meta('ban_version');
		effacer_meta('ban_base_version');
		
		// enregistrer la version actuelle
		ecrire_meta($nom_meta_base_version,$current_version = $version_cible);
		
		echo 'Plugin banni&egrave;res mis &agrave; jour : '.$current_version;
	}
}

function bannieres_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_bannieres");
	sql_drop_table("spip_bannieres_suivi");
	effacer_meta($nom_meta_base_version);
}

function renommer_bandeaux() {

	$dossier_images = _DIR_IMG;
	$dossier = opendir($dossier_images);
	
	// renommer les images
	while ($fichier = readdir($dossier)) {
			
			// On traite uniquement les anciennes bannieres  
			$debut_nom = substr($fichier,0,4);
			if ($debut_nom == 'ban_'){

			   if (file_exists($dossier_images.$fichier )) {

					$nouveau_nommage = 'banniereon';
					$resultat = substr_replace($fichier, $nouveau_nommage, 0,4);
					rename($dossier_images.$fichier , $dossier_images.$resultat);
			   
				   }
			
			}
	}

echo 'Les anciennes images ont &eacute;t&eacute;es renom&eacute;es<br />';

closedir($dossier);

return;
}
?>