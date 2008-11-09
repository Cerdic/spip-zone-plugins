<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	include_spip('inc/meta');
	include_spip('base/create');
	include_spip('inc/vieilles_defs');

	function guestbook_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				include_spip('base/guestbook');
				creer_base();
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			}
			if (version_compare($current_version,"2.0","<")){
				//modifications de la table livre en guestbook
				sql_alter("TABLE spip_livre RENAME TO spip_guestbook");
				sql_alter("TABLE spip_guestbook CHANGE id_messages id_message BIGINT(21) NOT NULL AUTO_INCREMENT");
				sql_alter("TABLE spip_guestbook ADD COLUMN message TEXT AFTER id_message");
				sql_alter("TABLE spip_guestbook ADD COLUMN statut VARCHAR(8) NOT NULL AFTER ville");
				sql_alter("TABLE spip_guestbook ADD COLUMN ip VARCHAR(255) NOT NULL AFTER statut");
				sql_alter("TABLE spip_guestbook ADD COLUMN date DATE AFTER note");
				//Remplissage des nouveaux champs
				sql_updateq("spip_guestbook", array("statut" => "publie"));
				sql_updateq("spip_guestbook", array("message" => sql_quote("texte")));
				sql_updateq("spip_guestbook", array("date" => sql_quote("maj")));
				// Suppression des anciens
				sql_alter("TABLE spip_guestbook DROP COLUMN texte");
				sql_alter("TABLE spip_guestbook DROP COLUMN maj");
				
				//modifications de la table reponses_livre en guestbook_reponses
				sql_alter("TABLE spip_reponses_livre RENAME TO spip_guestbook_reponses");
				sql_alter("TABLE spip_guestbook_reponses CHANGE id_reponses id_reponse BIGINT(21) NOT NULL AUTO_INCREMENT");
				sql_alter("TABLE spip_guestbook_reponses CHANGE id_messages id_message BIGINT(21) NOT NULL");
				sql_alter("TABLE spip_guestbook_reponses ADD COLUMN id_auteur BIGINT(21) NOT NULL AFTER id_message");
				sql_alter("TABLE spip_guestbook_reponses ADD COLUMN message TEXT AFTER id_auteur");
				sql_alter("TABLE spip_guestbook_reponses ADD COLUMN statut VARCHAR(8) NOT NULL AFTER message");
				sql_alter("TABLE spip_guestbook_reponses CHANGE date maj DATE");
				sql_alter("TABLE spip_guestbook_reponses ADD COLUMN date DATETIME AFTER statut");
				//Remplissage des nouveaux champs
				sql_updateq("spip_guestbook_reponses", array("statut" => "publie"));
				sql_updateq("spip_guestbook_reponses", array("message" => sql_quote("texte")));
				sql_updateq("spip_guestbook_reponses", array("date" => sql_quote("maj")));
				// Suppression des anciens
				sql_alter("TABLE spip_guestbook_reponses DROP COLUMN texte");
				sql_alter("TABLE spip_guestbook_reponses DROP COLUMN maj");
				ecrire_meta($nom_meta_base_version,$current_version="0.5.0");
			}
		}
	}

	function guestbook_vider_tables($nom_meta_base_version) {
		sql_drop_table("spip_guestbook");
		sql_drop_table("spip_guestbook_reponses");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}
?>