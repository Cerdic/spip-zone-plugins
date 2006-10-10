<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_FORMULAIRE_PERSONNALISATION ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_PERSONNALISATION', array());
}

function balise_FORMULAIRE_PERSONNALISATION_stat($args, $filtres) {
	$script = self(); # sur soi-meme
	$normal = 'normal';
	$zoom = 'zoom';	
	$inverse = 'inverse';
	$zoominverse = 'zoominverse';
	$classique = 'classique';
	$autolink = 'autolink';	
	$autofocus = 'autofocus';
	$one = '1000';
	$two = '2000';
	$tree = '3000';
	$checkeduse = 'normal';
	$checkednavmode = 'classique';
	$checkedduree = '1000';	
	return
		array($script,$normal,$zoom,$inverse,$zoominverse,$classique,$autolink,$autofocus,$one,$two,$tree,$checkeduse,$checkednavmode,$checkedduree);
}
 
function balise_FORMULAIRE_PERSONNALISATION_dyn($script,$normal,$zoom,$inverse,$zoominverse,$classique,$autolink,$autofocus,$one,$two,$tree,$checkeduse,$checkednavmode,$checkedduree) {
	// Recuperer les variables
	$use = _request('use');
	$navigationmode = _request('navigationmode');
	$duree = _request('duree');	
	
	// Poser un cookie pour ne pas retaper les infos invariables
	include_spip('inc/cookie');
	if($use!='') {
		spip_setcookie('spip_personnalisation_use',$use,time()+60*60*24*365);
	}
	if($navigationmode!='') {
		spip_setcookie('spip_personnalisation_navigationmode',$navigationmode,time()+60*60*24*365);
	}
	if($duree!='') {
		spip_setcookie('spip_personnalisation_duree',$duree,time()+60*60*24*365);
	}
	
	
	if (!isset($_POST['use'])) {
	// read cookie
		if (isset($_COOKIE['spip_personnalisation_use'])) {
			$checkeduse = $_COOKIE['spip_personnalisation_use'] ;
		}
		else {
			$checkeduse = $checkeduse ;
		}
	}
	else {
		$checkeduse = $use;
	}
	
	if (!isset($_POST['navigationmode'])) {
	// read cookie
		if (isset($_COOKIE['spip_personnalisation_navigationmode'])) {
			$checkednavmode = $_COOKIE['spip_personnalisation_navigationmode'] ;
		}
		else {
			$checkednavmode = $checkednavmode ;
		}
	}
	else {
		$checkednavmode = $navigationmode;
	}
	
	if (!isset($_POST['duree'])) {
	// read cookie
		if (isset($_COOKIE['spip_personnalisation_duree'])) {
			$checkedduree = $_COOKIE['spip_personnalisation_duree'] ;
		}
		else {
			$checkedduree = $checkedduree ;
		}
	}
	else {
		$checkedduree = $duree;
	}	
	
	return array('formulaires/formulaire_personnalisation', 0,
	array(
		'url' => $script, # ce sur quoi on fait le action='...'
		'normal' => $normal,
		'zoom' => $zoom,
		'inverse' => $inverse,		
		'zoominverse' => $zoominverse,
		'classique' => $classique,
		'autolink' => $autolink,	
		'autofocus' => $autofocus,
		'1000' => $one,
		'2000' => $two,
		'3000' => $tree,		
		'checkeduse' => $checkeduse,		
		'checkednavmode' => $checkednavmode,		
		'checkedduree' => $checkedduree,
		));
}

?>
