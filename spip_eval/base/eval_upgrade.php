<?php
/*
 * Plugin pour SPIP 2.0
 * Auteur Cyril MARION
 * (c) 2010 Ateliers CYM - Paris
 * Distribue sous licence GPL
 */
include_spip('inc/meta');
include_spip('base/create');
include_spip('inc/vieilles_defs');

function eval_upgrade($nom_meta_base_version,$version_cible){

	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/eval_tables');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			
			// pour la gestion des evolutions, prendre modele sur le fichier "notation_upgrade.php"

			// ajout des champs commentaire et id_mot a la table spip_notations
			sql_alter("TABLE spip_notations ADD COLUMN id_mot BIGINT(21) NOT NULL DEFAULT '0' AFTER id_objet");
			sql_alter("TABLE spip_notations ADD COLUMN commentaire TEXT NOT NULL DEFAULT '' AFTER note");
			// ajout de l'index sur id_mot
			sql_alter("TABLE spip_notations ADD INDEX (id_mot)");
			
			// ajout du champ id_mot a la table spip_notations_objets
			sql_alter("TABLE spip_notations_objets ADD COLUMN id_mot BIGINT(21) NOT NULL DEFAULT '0' AFTER id_objet");
			// ajout de l'index de cle primaire multiple avec objet et id_objet
			sql_alter("TABLE spip_notations_objets DROP INDEX objet");
			sql_alter("TABLE spip_notations_objets DROP INDEX id_objet");
			sql_alter("TABLE spip_notations_objets ADD PRIMARY KEY (objet, id_objet, id_mot)");
			
			// mise a jour des tables modifiees
			maj_tables("spip_notations");
			maj_tables("spip_notations_objets");
		}
	}
}


function eval_vider_tables($nom_meta_base_version) {
	// effacement de la nouvelle table spip_campagnes
	sql_drop_table("spip_campagnes");
	// effacement de la nouvelle table spip_mots_notations
	sql_drop_table("spip_mots_notations");
	// suppression du nouvel index de spip_notations
	sql_alter("TABLE spip_notations DROP INDEX (id_mot)");
	// suppression des 2 nouveaux champs sur spip_notations
	sql_alter("TABLE spip_notations DROP COLUMN id_mot");
	sql_alter("TABLE spip_notations DROP COLUMN commentaire");
	// suppression de la cle d'index sur spip_notations_objets
	sql_alter("TABLE spip_notations_objets DROP INDEX id_mot");
	// suppression du nouveau champ sur spip_notations_objets
	sql_alter("TABLE spip_notations_objets DROP COLUMN id_mot");
	
	effacer_meta($nom_meta_base_version);
}

?>