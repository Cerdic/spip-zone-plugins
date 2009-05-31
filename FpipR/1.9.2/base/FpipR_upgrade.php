<?php

function FpipR_droptemptables() {
  include_spip('base/FpipR_db');
  $drop_tables = '';
  foreach($GLOBALS['FpipR_versions'] as $table => $version) {
	if(strpos($table,'fpripr') > 0) {
	  $drop_tables .= ','.str_replace('spip_',$GLOBALS['table_prefix'].'_',$table);
	  effacer_meta('FpipR_'.$table);
	}
  }
  $drop_tables = substr($drop_tables,1);
  spip_query("DROP TABLE IF EXISTS $drop_tables;");
}

function FpipR_doupgrade() {
	  //Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
	  //on cree la colonne pour stoquer les frobs
	  spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` ADD (`flickr_token` TINYTEXT NULL, `flickr_nsid` TINYTEXT NULL);");
	  FpipR_droptemptables();
	  ecrire_meta('FpipR:installe',serialize($GLOBALS['FpipR_versions']['spip_auteurs'])); //histoire de pas faire une recherche dans la base a chaque coup
	  ecrire_metas();
}

function FpipR_install($action){
  switch ($action){
	case 'test':
	  include_spip('base/FpipR_db');
	  //Contrôle du plugin à chaque chargement de la page d'administration
	  $installe = unserialize(lire_meta('FpipR:installe'));
	  $uptodate = $installe && ($installe == $GLOBALS['FpipR_versions']['spip_auteurs']);
	  foreach($GLOBALS['FpipR_versions'] as $table => $version) {
		$version_table = lire_meta("FpipR_$table");
		$uptodate = $uptodate && (!$version_table || $version_table == $version);
		if(!$uptodate) return false;
	  }
	  return $uptodate;
	  break;
	case 'install':
	  FpipR_doupgrade();
	  break;
	case 'uninstall':
	  //Appel de la fonction de suppression
	  FpipR_droptemptables();
	  spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` DROP COLUMN `flickr_token`;");
	  spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` DROP COLUMN `flickr_nsid`;");
	  effacer_meta('FpipR:installe');
	  ecrire_metas();
	  break;
  }
}

?>