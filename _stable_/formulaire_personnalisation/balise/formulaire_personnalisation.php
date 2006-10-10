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
	$checked = 'normal';
	return
		array($script,$normal,$zoom,$inverse,$zoominverse,$classique,$autolink,$autofocus,$one,$two,$tree,$checked);
}
 
function balise_FORMULAIRE_PERSONNALISATION_dyn($script,$normal,$zoom,$inverse,$zoominverse,$classique,$autolink,$autofocus,$one,$two,$tree,$checked) {
	// Recuperer les variables
	$use = _request('use');
	$navigationmode = _request('navigationmode');
	$duree = _request('duree');

	// Poser un cookie pour ne pas retaper les infos invariables
	include_spip('inc/cookie');
	if($use!=='') {
		spip_setcookie('spip_personnalisation_use',$use,time()+60*60*24*365);
	}
	if($navigationmode!==''){ 
		spip_setcookie('spip_personnalisation_navigationmode',$navigationmode,time()+60*60*24*365);
	}
	if($duree!=='') {
		spip_setcookie('spip_personnalisation_duree',$duree,time()+60*60*24*365);
	}
	
	if (isset($_COOKIE['spip_personnalisation_use'])){
		$checked = $_COOKIE['spip_personnalisation_use'];
	} else{
		$checked = $checked ;
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
		'checked' => $checked,
		));
}

?>
