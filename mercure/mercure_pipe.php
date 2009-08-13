<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.20 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| Declare pipeline                           |
+--------------------------------------------+
*/

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MERCURE',(_DIR_PLUGINS.end($p)));
$p = realpath(dirname(__FILE__));
$p = substr($p,0,strrpos($p,'/')-1);
$p = substr($p,0,strrpos($p,'/'));
define('_DIR_REMOVE_MERCURE',$p.'/plugins/mercure');
 
	# bouton interface spip
	function mercure_ajouterBoutons($boutons_admin) {
		  if(isset($GLOBALS['mercure']['menu'])){
        $mercure_menu = $GLOBALS['mercure']['menu'];
      }else{
        $mercure_menu = 'configuration';
      }
		  // on voit le bouton dans la barre "configuration" par défaut... juste pour les admins !
		  $boutons_admin[$mercure_menu]->sousmenu["mercure_pg"]= new Bouton(
			_DIR_PLUGIN_MERCURE."img_pack/mercure.png",  // icone
			_T('mercure:mercure_titre')	// titre  
			);
		  return $boutons_admin;
	}

	# style
	function mercure_header_prive($flux) {
		$exec = _request('exec');
//		if(ereg('^(mercure_).*',$exec)) { // version 0.10 : EREG est déprécié en PHP 5.3.x
		if(preg_match('^(mercure_).*^',$exec) == 1) {
		$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_MERCURE.'mercure_styles.css" />'."\n";
		}
		return $flux;
	}
	
	# repertoire icones MERCURE
	if (!defined('_DIR_IMG_MERCURE')) {
		define('_DIR_IMG_MERCURE', _DIR_PLUGIN_MERCURE.'img_pack/');
	}

	# repertoire sons MERCURE
	if (!defined('_DIR_SOUND_MERCURE')) {
		define('_DIR_SOUND_MERCURE', _DIR_PLUGIN_MERCURE.'sound/');
	}

	# URL sound MERCURE
	if (!defined('_URL_SOUND_MERCURE')) {
    $pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/')-1);
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/'));
    define('_URL_SOUND_MERCURE',$pageURL.'/plugins/mercure/sound/');
	}

	# URL ajax MERCURE
	if (!defined('_URL_AJAX_MERCURE')) {
    $pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/')-1);
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/'));
    define('_URL_AJAX_MERCURE',$pageURL.'/plugins/mercure/ajax/');
	}

	# repertoire local MERCURE
	if (!defined('_DIR_LOCAL_MERCURE')) {
    define('_DIR_LOCAL_MERCURE', _DIR_REMOVE_MERCURE.'/local/');
		# S'il n'existe pas... on le crée !
		# Comme cela on a les bons droits dessus (0755)
    # et on peut y créer des fichiers (logs, ...)
    if (!file_exists(_DIR_LOCAL_MERCURE)) {
      mkdir(_DIR_LOCAL_MERCURE, 0755);
    } 		
	}
?>
