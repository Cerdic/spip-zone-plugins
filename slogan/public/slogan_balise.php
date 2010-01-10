<?php
/*
 * Plugin Slogan
 * (c) 2009 C.Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@balise_NOM_SITE_SPIP_dist
function balise_SLOGAN_SITE_SPIP_dist($p) {
	$p->code = "\$GLOBALS['meta']['slogan_site']";
	#$p->interdire_scripts = true;
	return $p;
}

function slogan_aleatoire($titre,$lang=null){
	static $slogan = array();
	if (is_null($lang)){
		$lang = $GLOBALS['spip_lang'];
	}
	if (isset($slogan[$lang]))
		return $slogan[$lang];
	if (!$slogan_aleatoire = charger_fonction("slogan_aleatoire_".$lang,"public",true)){
		$slogan_aleatoire = charger_fonction("slogan_aleatoire_fr","public");
		return $slogan[$lang] = "<span lang='$lang'>".$slogan_aleatoire($titre)."</span>";
	}
	return $slogan[$lang] = $slogan_aleatoire($titre);
}

function public_slogan_aleatoire_fr_dist($titre){
	define('_URL_SLOGAN_ALEATOIRE_FR',"http://hellday.free.fr/slogans/sloganfr.php?chaine=%s");
	$slogan = "";

	$url = str_replace("%s", urlencode($titre), _URL_SLOGAN_ALEATOIRE_FR);
	include_spip('inc/distant');
	if ($res = recuperer_page($url)){
		$res = extraire_balises($res,'body');
		$res = reset($res);
		$res = charset2unicode($res,"iso-8859-1");
		$res = preg_replace(",<style[^>]*>.*</style>,Uims","",$res);
		$res = preg_replace(",<h1[^>]*>.*</h1>,Uims","",$res);
		$res = preg_replace(",<form[^>]*>.*</form>,Uims","",$res);
		$slogan = trim(textebrut($res));
	}
	return $slogan;
}

function public_slogan_aleatoire_en_dist($titre){
	define('_URL_SLOGAN_ALEATOIRE_EN',"http://www.sloganizer.net/en/outbound.php?slogan=%s");
	$slogan = "";

	$url = str_replace("%s", urlencode($titre), _URL_SLOGAN_ALEATOIRE_EN);
	include_spip('inc/distant');
	if ($res = recuperer_page($url)){
		$slogan = trim(textebrut($res));
	}
	return $slogan;
}

function public_slogan_aleatoire_de_dist($titre){
	define('_URL_SLOGAN_ALEATOIRE_DE',"http://www.sloganizer.net/outbound.php?slogan=%s");
	$slogan = "";

	$url = str_replace("%s", urlencode($titre), _URL_SLOGAN_ALEATOIRE_DE);
	include_spip('inc/distant');
	if ($res = recuperer_page($url)){
		$slogan = trim(textebrut($res));
	}
	return $slogan;
}


?>