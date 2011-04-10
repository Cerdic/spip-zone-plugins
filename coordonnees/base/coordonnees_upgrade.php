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
	
	// On passe les pays en code ISO, beaucoup plus génériques que les ids SQL
	if (version_compare($current_version, "1.3", "<")) { 
		$ok = true;
		
		// On ajoute un champ pour le code car il faut les deux champs pour la transistion
		$ok &= sql_alter('TABLE spip_adresses ADD pays_code varchar(2) not null default ""');
		
		// On parcourt les adresses pour remplir le code du pays
		$adresses = sql_allfetsel('id_adresse,pays', 'spip_adresses');
		if ($adresses and is_array($adresses)){
			foreach ($adresses as $adresse){
				$ok &= sql_update(
					'spip_adresses',
					array('pays_code' => '(SELECT code FROM spip_pays WHERE id_pays='.intval($adresse['pays']).')'),
					'id_adresse='.intval($adresse['id_adresse'])
				);
			}
		}
		
		// On supprime l'ancien
		$ok &= sql_alter('TABLE spip_adresses DROP pays');
		
		// On change le nom du nouveau
		$ok &= sql_alter('TABLE spip_adresses CHANGE pays_code pays varchar(2) not null default ""');
		
		if ($ok){
			spip_log('Tables coordonnées correctement passsées en version 1.3','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.3");
		}
		else return false;
	}
	
	// On avait supprimer les types, mais ils reviennent en force mais dans les LIENS
	if (version_compare($current_version, "1.4", "<")) { 
		$ok = true;
		
		// On ajoute un champ "type" plus petit que l'ancien (car vrai type donc généralement juste un mot)
		$ok &= sql_alter('TABLE spip_adresses_liens ADD type varchar(25) not null default ""');
		$ok &= sql_alter('TABLE spip_numeros_liens ADD type varchar(25) not null default ""');
		$ok &= sql_alter('TABLE spip_emails_liens ADD type varchar(25) not null default ""');
		
		if ($ok){
			spip_log('Tables coordonnées correctement passsées en version 1.4','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.4");
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
