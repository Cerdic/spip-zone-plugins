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
	$p->code = "typo(\$GLOBALS['meta']['slogan_site'])";
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

	// si un fichier cache slogans_xx.txt existe, on l'utilise
	if ($f = find_in_path("slogans/slogans_$lang.txt")
			AND lire_fichier($f, $slogans)
			AND trim($slogans)
			AND ($n = count($slogans = explode("\n",$slogans)))>1){
		return $slogan[$lang] = str_replace("@slogan@",$titre,$slogans[rand(0, $n)]);
	}

	// sinon on se rabat sur la fonction
	if (!$slogan_aleatoire = charger_fonction("slogan_aleatoire_".$lang,"public",true)
		AND $lang!='fr'){
		// sinon on se rabat sur le slogan en francais
		return $slogan[$lang] = "<span lang='$lang'>".slogan_aleatoire($titre,'fr')."</span>";
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

function slogan_cache($lang,$fichier,$n=100){
	lire_fichier($fichier, $contenu);
	$slogans = explode("\n",trim($contenu));

	var_dump(count($slogans));

	$slogan_aleatoire = charger_fonction("slogan_aleatoire_$lang","public");
	while ($n-->0){
		$slogans[] = $slogan_aleatoire("@slogan@");
	}
	$slogans = array_unique($slogans);
	var_dump(count($slogans));
	ecrire_fichier($fichier, implode("\n",$slogans));
}


?>