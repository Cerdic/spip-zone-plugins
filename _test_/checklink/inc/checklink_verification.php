<?php

function checklink_verifie_lien($url, $date_verif, $maj_statut){
	include_spip('inc/distant');
	$datas="";
	$boundary="";
	$url_abs = url_absolue($url);
	if (!preg_match(",^([\w]{3-6})://,",$url_abs) AND $url_abs{0}!='/'){
		$url_abs = suivre_lien(url_de_base(),$url_abs);
	}
	$statut = recuperer_page($url_abs, false, false, 1048576, $datas, $boundary, false, $date_verif);
	// inchangee : on met juste a jour les infos
	if ($statut==200){
		spip_query("UPDATE spip_liens SET statut='oui',date_verif=NOW() WHERE url=".spip_abstract_quote($url));
		spip_log("checklink : $url statut 200");
		return;
	}
	// absente
	if ($statut===false){
		spip_query("UPDATE spip_liens SET statut='$maj_statut',date_verif=NOW() WHERE url=".spip_abstract_quote($url));
		spip_log("checklink : $url introuvable, passage a $maj_statut");
		return;
	}
	// presente
	$texte = $statut;
	// extraire le charset si present
	$charset = false;
	if (preg_match(",charset=\s*([^\s\"']*),i",$texte,$reg))
		$charset = trim($reg[1]);
	if ($charset)
		$texte = charset2unicode($texte,$charset);
	else {
		// a l'aveugle
		$texte = charset2unicode($texte,'iso-8859-1');
		$texte = charset2unicode($texte,'utf-8');
	}

	// extraire le titre et la langue
	$titre = null;
	$lang = null;
	if (preg_match(',<title[^>]*>(.*)</title>,Uims',$texte,$reg))
		$titre = trim($reg[1]);
	else if (preg_match(',<(h[1-6])[^>]*>.*</$1>,Uims',$texte,$reg))
		$titre = trim($reg[2]);

	if (preg_match(',<html[^>]*>,Uims',$texte,$reg))
		$lang = extraire_attribut($reg[0],'lang');
	if (!$lang){
		// en depit on cherche un lang= quelque part ...
		if (preg_match('/lang\s*=\s*[\'"]?([a-z\-]{2,5})/Uims',$texte,$reg))
			$lang=$reg[1];
	}

	spip_query("UPDATE spip_liens SET statut='oui',date_verif=NOW() WHERE url=".spip_abstract_quote($url));
	if ($titre)
		spip_query("UPDATE spip_liens SET titre=".spip_abstract_quote($titre)." WHERE url=".spip_abstract_quote($url)." AND titre_auto='oui'");
	if ($lang)
		spip_query("UPDATE spip_liens SET lang=".spip_abstract_quote(strtolower($lang))." WHERE url=".spip_abstract_quote($url)." AND lang_auto='oui'");
	spip_log("checklink : $url mise a jour, passage a 'oui'");
}


function cron_checklink_verification($t){
	// Initialisations

	$url = "";

	## valeurs modifiables dans mes_options
	## attention il est tres mal vu de prendre une periode < 20 minutes
	define('_PERIODE_VERIFICATION', 2*60);
	define('_PERIODE_VERIFICATION_SUSPENDUE', 24*60);

	// On va tenter un lien 'sus' ou 'off' de plus de 24h, et le passer en 'off'
	// s'il echoue
	$where = "statut IN ('sus','off')
	AND date_verif < DATE_SUB(NOW(), INTERVAL
	"._PERIODE_VERIFICATION_SUSPENDUE." MINUTE)";
	$row = spip_fetch_array(spip_query("SELECT url,date_verif FROM spip_liens WHERE $where	ORDER BY date_verif LIMIT 1"));
	if ($row)
		checklink_verifie_lien($row["url"],$row["date_verif"], 'off');

	// Et un lien 'ind' (ou 'oui' si plus de 'ind') de plus de 2 heures, qui passe en 'sus' s'il echoue
	$where = "statut='ind'
	AND date_verif < DATE_SUB(NOW(), INTERVAL "._PERIODE_VERIFICATION." MINUTE)";
	$row = spip_fetch_array(spip_query("SELECT url FROM spip_liens WHERE $where	ORDER BY date_verif LIMIT 1"));
	if (!$row){
		$where = "statut='oui'
	AND date_verif < DATE_SUB(NOW(), INTERVAL "._PERIODE_VERIFICATION." MINUTE)";
		$row = spip_fetch_array(spip_query("SELECT url FROM spip_liens WHERE $where	ORDER BY date_verif LIMIT 1"));
	}
	if ($row)
		checklink_verifie_lien($row["url"],$row["date_verif"], 'sus');

	if ($url=="") return 1;
	
	spip_log("il reste des liens a verifier ...");
	return (0 - $t);

}
?>
