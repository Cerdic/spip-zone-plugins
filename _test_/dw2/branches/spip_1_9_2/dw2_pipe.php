<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Declare pipeline
| +
| define repertoire img_pack spec DW2
+--------------------------------------------+
*/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_DW2',(_DIR_PLUGINS.end($p)));
 
	#
	# boutons dw2 dans sousmenus de stats,naviguer
	#
	function dw2_ajouterBoutons($boutons_admin) {
		// si on est admin principal
		if ($GLOBALS['connect_statut'] == "0minirezo" AND $GLOBALS["connect_toutes_rubriques"]) {
			$boutons_admin['statistiques_visites']->sousmenu["dw2_admin"]= new Bouton(
			"../"._DIR_PLUGIN_DW2."img_pack/telech.gif",  // icone
			_T('dw:titre_page_admin')	// titre
			);
			$boutons_admin['naviguer']->sousmenu["dw2_cata_img"]= new Bouton(
			"../"._DIR_PLUGIN_DW2."img_pack/cata_img.gif",  // icone
			_T('dw:cata_images')	// titre
			);
		}
		//
		else {
			// redacteur et admin restreint, 2 boutons : catalogue images ; stats doc redacteur
			$boutons_admin['statistiques_visites']->sousmenu["dw2_redacteur"]= new Bouton(
			"../"._DIR_PLUGIN_DW2."img_pack/cata_redact.gif",  // icone
			_T('dw:titre_page_admin')	// titre
			);
			
			$boutons_admin['naviguer']->sousmenu["dw2_cata_img"]= new Bouton(
			"../"._DIR_PLUGIN_DW2."img_pack/cata_img.gif",  // icone
			_T('dw:cata_images')	// titre
			);
		}
		return $boutons_admin;
	}

	#
	# js + css prive
	#
	function dw2_header_prive($flux) {
		$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_DW2.'dw2_styles.css" />'."\n";
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_DW2.'dw2_back.js"></script>'."\n";
		return $flux;
	}
	
	#
	# js + css public
	#
	function dw2_insert_head($flux) {
		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_DW2.'dw2_fermepop.js"></script>'."\n";
		$flux.= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_DW2.'dw2_public_styles.css" />'."\n";
		return $flux;
	}
	
	#
	# cron verif new version dw2
	#
	function dw2_taches_generales_cron($taches_generales){
		$taches_generales['dw2_verif_maj'] = 3600*48; // tous les 2 jours
		return $taches_generales;
	}


	# repertoire icones DW2 .. dw2/img_pack/
	if (!defined("_DIR_IMG_DW2")) {
		define('_DIR_IMG_DW2', _DIR_PLUGIN_DW2.'img_pack/');
	}


?>
