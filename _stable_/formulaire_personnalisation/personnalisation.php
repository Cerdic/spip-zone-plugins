<?php

function personnalisation_insert_head($flux) {

	// cas de changement du mode d'affichage

	if (!isset($_POST['use'])) {
	
		switch ($_COOKIE['spip_personnalisation_use']) {

		case "zoom":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoom.css" />'. "\n";
		break;

		case "inverse":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/inverse.css" />'. "\n";
		break;

		case "zoominverse":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoom.css" />'. "\n";
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/inverse.css" />'. "\n";
		break;
	
		default:
		//default from css
		}		
	} 
	else {
		switch ($_POST['use']) {

		case "zoom":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoom.css" />'. "\n";
		break;

		case "inverse":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/inverse.css" />'. "\n";
		break;

		case "zoominverse":
		$flux = '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoom.css" />'. "\n";
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/inverse.css" />'. "\n";
		break;
		}		
	}

	// cas de changement du mode de navigation
	
	if (!isset($_POST['navigationmode'])) {
	// read cookie
		switch ($_COOKIE['spip_personnalisation_navigationmode']) {

		case "autolink":
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autolink.js" type="text/javascript"></script>'. "\n";
		$flux .= '<script type="text/javascript">imgPath = "' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/img/";</script>'. "\n";
		break;

		case "autofocus":
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autofocus.js" type="text/javascript"></script>'. "\n";
		break;

		default:
		//empty no script added
		}		
	}
	else {
		switch ($_POST['navigationmode']) {

		case "autolink":
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autolink.js" type="text/javascript"></script>'. "\n";
		$flux .= '<script type="text/javascript">imgPath = "' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/img/";</script>'. "\n";
		break;

		case "autofocus":
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autofocus.js" type="text/javascript"></script>'. "\n";
		break;
		}		
	}
	
	return $flux;
	
}

?>