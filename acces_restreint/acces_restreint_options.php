<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// declarer le pipeline pour le core
$GLOBALS['spip_pipeline']['accesrestreint_liste_zones_autorisees']='';

// Si on n'est pas connecte, aucune autorisation n'est disponible
// pas la peine de sortir la grosse artillerie
if (!isset($GLOBALS['auteur_session']['id_auteur'])){
	$GLOBALS['accesrestreint_zones_autorisees'] = '';
}
else {
	// Pipeline : calculer les zones autorisees, sous la forme '1,2,3'
	// TODO : avec un petit cache pour eviter de solliciter la base de donnees
	$GLOBALS['accesrestreint_zones_autorisees'] =
		pipeline('accesrestreint_liste_zones_autorisees', '');
}

// Ajouter un marqueur de cache pour le differencier selon les autorisations
if (!isset($GLOBALS['marqueur'])) $GLOBALS['marqueur'] = '';
$GLOBALS['marqueur'] .= ":accesrestreint_zones_autorisees="
	.$GLOBALS['accesrestreint_zones_autorisees'];

if (test_espace_prive()
  AND _request('exec')=='admin_plugin'){
  include_spip('spip_bonux_fonctions');
  // verifier qu'on a bien le bon bonux, avec la css qui marche
  if (!file_exists($f=(_DIR_PLUGIN_SPIP_BONUX.'style_prive_formulaires.html'))){
  	include_spip('inc/plugin');
  	ecrire_plugin_actifs(array('ACCESRESTREINT'=>substr(rtrim(_DIR_PLUGIN_ACCESRESTREINT,'/'),strlen(_DIR_PLUGINS))),false,'enleve');
  	ecrire_meta('plugin_erreur_activation','Acces rezstreint 3.0 necessite le vrai SPIP-Bonux');
  	//die('echec:'.md5($contenu).":"._BONUX_CSS_MD5_FORMULAIRES);	
  }
}
?>