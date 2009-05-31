<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function exec_calendrier(){
	$mode = _request('mode');
	if ($mode=='editorial'){
	  include_spip('exec/calendrier');
	  exec_calendrier_dist();
	}
	else {
		$var_f = charger_fonction('agenda_evenements');
		$var_f();
	}
}
if (test_espace_prive()
  AND _request('exec')=='admin_plugin'){
  include_spip('spip_bonux_fonctions');
  // verifier qu'on a bien le bon bonux, avec la css qui marche
  if (!file_exists($f=(_DIR_PLUGIN_SPIP_BONUX.'style_prive_formulaires.html'))){
  	include_spip('inc/plugin');
  	ecrire_plugin_actifs(array('AGENDA'=>substr(rtrim(_DIR_PLUGIN_AGENDA,'/'),strlen(_DIR_PLUGINS))),false,'enleve');
  	ecrire_meta('plugin_erreur_activation','Agenda necessite le vrai SPIP-Bonux');
  	//die('echec:'.md5($contenu).":"._BONUX_CSS_MD5_FORMULAIRES);	
  }
}
?>