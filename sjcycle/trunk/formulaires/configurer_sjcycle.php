<?php
// Securite
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_sjcycle_charger(){
	
	// verifier avant tout la config de SPIP
	$erreurs = array();
	// choix de la methode de traitement des images par le serveur
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $erreurs;
	}
	// Generation de miniatures des images inactive
	if (lire_config('creer_preview')!='oui') {
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $erreurs;
	}
	
	// la fonction ci-dessus necessite de contextualiser le chargement
	// (dans le formulaires/configurer_sjcycle.html on retrouve des valeurs par defaut lorsqu'elles sont initialisees dans sjcycle_administrations.php)
	$config = lire_config('sjcycle');
	$valeurs = array(
		'largeurmax' => $config['largeurmax'],
		'hauteurmax' => $config['hauteurmax'],
		'autoheight' => $config['autoheight'],
		'timeout' => $config['timeout'],
		'speed' => $config['speed'],
		'delay' => $config['delay'],
		'fx' => $config['fx'],
		'tilevertical' => $config['tilevertical'],
		'carouselvisible' => $config['carouselvisible'],
		'carouseloffset' => $config['carouseloffset'],
		'carouselslidedimension' => $config['carouselslidedimension'],
		'carouselvertical' => $config['carouselvertical'],
		'carouselfluid' => $config['carouselfluid'],
		'reverse' => $config['reverse'],
		'sync' => $config['sync'],
		'pauseonhover' => $config['pauseonhover'],
		'pauseonhovercontent' => $config['pauseonhovercontent'],
		'random' => $config['random'],
		'next' => $config['next'],
		'prev' => $config['prev'],
		'allowwrap' => $config['allowwrap'],
		'paused' => $config['paused'],
		'pager' => $config['pager'],
		'caption' => $config['caption'],
		'captiontemplate' => $config['captiontemplate'],
		'overlay' => $config['overlay'],
		'overlaytemplate' => $config['overlaytemplate'],
		'backgroundcolor' => $config['backgroundcolor'],
		'tooltip' => $config['tooltip'],
		'tooltip_carac' => $config['tooltip_carac'],
		'mediabox' => $config['mediabox'],
		'afficher_aide' => $config['afficher_aide']
	);
	return $valeurs;

}