<?php

// retourne la liste des onglets trouves pour un groupe donnee
// #ENV{cfg}|lister_config_onglets
function lister_config_onglets($nom) {
	$onglets = 
		array_unique(
		array_values(
		array_flip(
		find_all_in_path('formulaires/','/config_'.$nom.'_'))));
	foreach ($onglets as $i=>$o)
		$onglets[$i] = substr(basename($o, '.html'), 8+strlen($nom)); 
	return $onglets;
}


// calcule un element de liste (li) du menu de navigation
// #ITEM_CONFIG{sarka,layout}
function balise_ITEM_CONFIG($p) {
	
	$cfg_actif = "\$Pile[0]['cfg']";
	$onglet_actif = "\$Pile[0]['onglet']";
	
	$cfg = interprete_argument_balise(1,$p);
	$onglet = interprete_argument_balise(2,$p);

	if ($onglet) {
		$p->code = 'calculer_item_config('.$cfg.','.$cfg_actif.','.$onglet.','.$onglet_actif.')';
	}
	else {
		$p->code = 'calculer_item_config('.$cfg.','.$cfg_actif.', false,'.$onglet_actif.')';
	}
	return $p;
}

// $onglet : false, ou "onglet" ou array("onglet")
function calculer_ITEM_CONFIG($cfg, $cfg_actif, $onglet=false, $onglet_actif='') {

	$actif = ($cfg === $cfg_actif);

	$retour = "";
	if ($onglet !== false) {
		if (is_array($onglet)) {
			foreach ($onglet as $ong) {
				$lien = calculer_lien_config($cfg, $ong);
				$on = $actif & ($ong == $onglet_actif);
				$retour .= "<li" . ($on ? ' class = "on"' : '') . ">$lien</li>";
			}
		} else {
			$lien = calculer_lien_config($cfg, $onglet);
			$on = $actif & ($onglet == $onglet_actif);
			$retour .= "<li" . ($actif ? ' class = "on"' : '') . ">$lien</li>";
		}
	} else {
		$lien = calculer_lien_config($cfg);
		if ($onglet_actif) $actif = false;
		$retour .= "<li" . ($actif ? ' class = "on"' : '') . ">$lien</li>";
	}
	return $retour;
}


// calcule un lien vers la config
// #LIEN_CONFIG{sarka}
// #LIEN_CONFIG{sarka,layout}
function balise_LIEN_CONFIG($p) {
	$cfg = interprete_argument_balise(1,$p);
	$onglet = interprete_argument_balise(2,$p);
	$texte = interprete_argument_balise(3,$p);
	if ($texte)
		$p->code = 'calculer_lien_config('.$cfg.','.$onglet.','.$texte.')';
	elseif ($onglet)
		$p->code = 'calculer_lien_config('.$cfg.','.$onglet.')';
	else
		$p->code = 'calculer_lien_config('.$cfg.')';
	return $p;
		$p->code = 'parametre_url(parametre_url(generer_url_ecrire(\'cfg\'),\'cfg\','.$cfg.'),\'onglet\','.$onglet.')';
}

function calculer_LIEN_CONFIG($cfg, $onglet='', $texte='') {
	$url = generer_url_ecrire('cfg');
	$url = parametre_url($url, 'cfg', $cfg);
	if ($onglet)
		$url = parametre_url($url, 'onglet', $onglet);
	if (!$texte) {
		if ($onglet)
			$texte = calculer_cfg_trad('configuration', $cfg, $onglet);
		else
			$texte = calculer_cfg_trad('configuration', $cfg);

		if (!$texte) $texte = $cfg.' '.$onglet;
	}
	return "<a href='$url'>$texte</a>";
}


// regarde si <:$nom:$quoi_$nom_$onglet:> existe
// et le retourne si c'est le cas
// sinon ne retourne rien.
// #TRAD{configurer,sarka,layout}
function balise_TRAD_dist($p) {
	$quoi = interprete_argument_balise(1,$p);
	$nom = interprete_argument_balise(2,$p);
	$onglet = interprete_argument_balise(3,$p);
	if ($onglet)
		$p->code = 'calculer_cfg_trad('.$quoi.','.$nom.','.$onglet.')';
	else
		$p->code = 'calculer_cfg_trad('.$quoi.','.$nom.')';
	return $p;
}

function calculer_cfg_trad($quoi, $nom, $onglet = '') {
	// on teste si une traduction existe
	static $traduire=false ;
 	if (!$traduire) {
		$traduire = charger_fonction('traduire', 'inc');
		include_spip('inc/lang');
	}

	$x = "$nom:$quoi"."_$nom";
	if ($onglet) { $x .= "_$onglet"; }
		
	$text = $traduire($x,$GLOBALS['spip_lang']);
	
	return ($text) ? _T($x) : '';	
}

?>
