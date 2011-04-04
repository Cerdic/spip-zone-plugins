<?php
/**
 * Plugin Coordonnees pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function coordonnees_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');
	
	
	/**
	 *
	 *  11/01/2009 : ajout table spip_emails, version 1.0.1
	 *
	 */
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		include_spip('base/create');
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	
	// On utilise plus le champ "numero" qui sera inclu dans la "voie"
	if (version_compare($current_version, "1.1", "<")) { 
		// on ajoute le contenu du champ "numero" au champ "voie"
		sql_update("spip_adresses",
			array("voie" => "CONCAT(numero, ' ', voie)"),
			array("numero IS NOT NULL", "numero <> ''"));
		// on supprime le champ "numero"
		sql_alter("TABLE spip_adresses DROP COLUMN numero");
		spip_log('Tables coordonnées correctement passsées en version 1.1','coordonnees');
		ecrire_meta($nom_meta_base_version, $current_version="1.1");
	}
	
	// On supprime les "type" en les transformant en vrai "titre" libres
	if (version_compare($current_version, "1.2", "<")) { 
		$ok = true;
		
		// On renomme les champs "type_truc" en "titre" tout simplement + on les allonge
		$ok &= sql_alter('TABLE spip_adresses CHANGE type_adresse titre varchar(255) not null default ""');
		$ok &= sql_alter('TABLE spip_numeros CHANGE type_numero titre varchar(255) not null default ""');
		$ok &= sql_alter('TABLE spip_emails CHANGE type_email titre varchar(255) not null default ""');
		
		if ($ok){
			spip_log('Tables coordonnées correctement passsées en version 1.2','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.2");
		}
		else return false;
	}

}

function coordonnees_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");

	effacer_meta($nom_meta_base_version);
}

?>
