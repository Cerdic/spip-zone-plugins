<?php

function basefiltrageip_droptables() {
  spip_query("DROP TABLE IF EXISTS ".$GLOBALS['table_prefix']."_basefiltrageip_whitelist, ".$GLOBALS['table_prefix']."_basefiltrageip_log, ".$GLOBALS['table_prefix']."_basefiltrageip_reasons;");
}

function basefiltrageip_doupgrade() {
  		include_spip('base/basefiltrageip_db');
		$installe = unserialize($GLOBALS['meta']['basefiltrageip:installe']);
	  $uptodate = $installe && ($installe == $GLOBALS['basefiltrageip_version']);
	  if(!$uptodate) {
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('basefiltrageip:installe',$GLOBALS['basefiltrageip_version']);
	  }
	  ecrire_metas();
}

function basefiltrageip_install($action){
  switch ($action){
	case 'test':
	  include_spip('base/basefiltrageip_db');
	  //Contrôle du plugin à chaque chargement de la page d'administration
	  $installe = unserialize(lire_meta('basefiltrageip:installe'));
	  $uptodate = $installe && ($installe == $GLOBALS['basefiltrageip_version']);
	  return $uptodate;
	  break;
	case 'install':
	  basefiltrageip_doupgrade();
	  break;
	case 'uninstall':
	  //Appel de la fonction de suppression
	  basefiltrageip_droptables();
	  effacer_meta('basefiltrageip:installe');
	  ecrire_metas();
	  break;
  }
}

?>
