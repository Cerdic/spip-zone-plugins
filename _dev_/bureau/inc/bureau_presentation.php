<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function inc_bureau_charge_dist() {
	global $connect_id_auteur;

	include_spip('inc/headers');
	include_spip('inc/filtres');
	include_spip('inc/texte');
	include_spip('inc/gadgets');
	http_no_cache();

	if (!$nom_site_spip = textebrut(typo($GLOBALS['meta']["nom_site"])))
		$nom_site_spip=  _T('info_mon_site_spip');

	$head = _DOCTYPE_ECRIRE
		. html_lang_attributes()
		."<title>[". $nom_site_spip. "] " . textebrut(typo($titre)) . "</title>\n"
		. "<meta http-equiv='Content-Type' content='text/html"
		. (($c = $GLOBALS['meta']['charset']) ?
			"; charset=$c" : '')
		. "' />\n"

		.'<script src="../prive/javascript/jquery.js" type="text/javascript"></script>'
		.'<script src="../prive/javascript/jquery.form.js" type="text/javascript"></script>'
		.'<script src="../prive/javascript/ajaxCallback.js" type="text/javascript"></script>'
		.'<script src="../prive/javascript/layer.js" type="text/javascript"></script>'
		.'<script type="text/javascript" src="'._DIR_PLUGIN_BUREAU.'javascript/guid.js"></script>'
		.'<script type="text/javascript" src="'._DIR_PLUGIN_BUREAU.'javascript/bureau.js"></script>'
		.'<script type="text/javascript" src="'._DIR_PLUGIN_BUREAU.'javascript/jclock.js"></script>'

//		.'<script type="text/javascript" src="'._DIR_PLUGIN_BUREAU.'javascript/arbre.js"></script>'

		.'<link rel="stylesheet" type="text/css" href="../prive/style_prive_defaut.css" id="cssprivee" />'
//		.'<link rel="stylesheet" type="text/css" href="../dist/agenda.css" />'
		.'<link rel="stylesheet" type="text/css" href="../prive/spip_style.css" media="all" />'
//		.'<link rel="stylesheet" type="text/css" href="../prive/spip_style_print.css" media="print" />'
//		.'<link rel="stylesheet" type="text/css" href="../prive/spip_style_invisible.css" />'


		.'<link  type="text/css" rel="stylesheet" href="'._DIR_PLUGIN_BUREAU.'css/bureau.css"/>'
		.envoi_link($nom_site_spip,$minipres)
		. '</head>';
	$body = '<body><div id="page">';

	return $head.$body;
}

function bureau_debut_menu() {
	return '<div id="barre">'
		.'<div id="barre-menus">';
}
function bureau_fin_menu() {
	return	'</div>'
		.'<hr class="gauche" />'
		.'<div id="barre-taches">'
		.'</div>';
}

function bureau_debut_infos() {
	return '<div id="barre-infos">';
}

function bureau_fin_infos() {
	return '</div>'
		.'<hr class="droite" />'
		.'</div>';
}

function bureau_debut() {
	return '<div id="bureau">';
}

function bureau_fin() {
	return 	'</div></div><div id="aspirateur"></div>'
		.'</body></html>';
}

function id_unique() {
	return  md5(uniqid(rand(), true));
}

function generer_url_bureau($nom) {
	return _DIR_PLUGIN_BUREAU.'bureau/?fenetre='.$nom;
}

function bureau_barre_menu($toogle,$contenu) {

	$texte = '';
	$uid = id_unique();

	foreach ($contenu as $l => $url) {
		$t = explode('/',$l);
		if ($t[1] == "fenetre"){
			$texte .= '<div><a class="ouvre" href="'.$url.'"/>'.$t[0].'</a></div>';
		}
		else {
			$texte .= '<div><a href="'.$url.'" />'.$t[0].'</a></div>';
		}
	}

	return '<div id="menu-'.$uid.'" class="menu">'
		.'<div class="toogle">'.$toogle.'</div>'
		.'<div class="contenu">'.$texte.'</div>'
		.'</div>';
}

function bureau_fenetre($titre, $contenu, $menu='',$style='') {

	$uid = id_unique();

	$fenetre = '<div class="fenetre" id="fenetre-'.$uid.'" style="'.$style.'">'
		.'<div class="fenetre-titre ferme drag"><span>'.$titre.'</span>'
		.'<a class="ferme" href="#"><img src="'.find_in_path('images/ferme.png').'" /></a>'
		.'<a class="maximise" href="#"><img src="'.find_in_path('images/maximise.png').'" /></a>'
		.'<a class="minimise" href="#"><img src="'.find_in_path('images/minimise.png').'" /></a>'
		.'</div>';

	if ($menu != '')
		$fenetre .= '<div class="fenetre-menu"><div class="contenu-menu"><div><a class="ferme-menu">Fermer >></a></div>'.$menu.'</div></div>';

	$fenetre .= '<div class="contenu">'.$contenu.'</div>'
		.'<div class="resize"></div>'
		.'</div>';

	return $fenetre;
}

?>
