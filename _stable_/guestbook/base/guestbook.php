<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
function guestbook_verifier_tables(){
	include_spip('inc/meta');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['guestbook_base_version'])) || (($current_version = $GLOBALS['meta']['guestbook_base_version'])!=$version_cible)){
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('base/guestbook_install');
			creer_base();
			ecrire_meta($nom_meta_plug,$version_cible,'non');
		}
		if (version_compare($current_version,'2.0','<')){
			sql_alter('TABLE spip_guestbook ADD prenom TEXT NOT NULL AFTER nom');
			sql_alter('TABLE spip_guestbook ADD pseudo TEXT NOT NULL AFTER prenom');
			sql_updateq("spip_guestbook", array("statut" => "off"), "statut='HL'");
			sql_alter("TABLE `spip_guestbook_reponses` CHANGE `id_reponse` `id_reponse` MEDIUMINT( 5 ) NOT NULL AUTO_INCREMENT");
			sql_alter("TABLE `spip_guestbook_reponses` CHANGE `id_message` `id_message` MEDIUMINT( 5 ) NOT NULL");
			sql_alter("TABLE `spip_guestbook_reponses` CHANGE `id_auteur` `id_auteur` MEDIUMINT( 5 ) NOT NULL");
			ecrire_meta($nom_meta_plug,$current_version='2.0');
		}
		ecrire_metas();
	}
}
function guestbook_vider_tables() {
	sql_drop_table("spip_guestbook");
	sql_drop_table("spip_guestbook_reponses");
	effacer_meta('guestbook_base_version');
	ecrire_metas();
}
function guestbook_install($action){
	switch ($action){
		case 'test':
			guestbook_verifier_tables();
		break;
		case 'install':
			guestbook_verifier_tables();
		break;
		case 'uninstall':
			guestbook_vider_tables();
		break;
	}
}
?>