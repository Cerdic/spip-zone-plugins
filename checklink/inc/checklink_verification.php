<?php

function checklink_verifie_lien($url, $date_verif, $maj_statut){
	include_spip('inc/distant');
	$datas="";
	$boundary="";
	$statut = recuperer_page($url, false, false, 1048576, $datas, $boundary, false, $date_verif);
	// inchangee
	if ($statut==200){
		spip_query("UPDATE spip_liens SET statut='oui',date_verif=NOW() WHERE url=".spip_abstract_quote($url));
		return;
	}
	// absente
	if (in_array($statut,array())){
		spip_query("UPDATE spip_liens SET statut='$maj_statut',date_verif=NOW() WHERE url=".spip_abstract_quote($url));
		return;
	}
	// presente
	// extraire le titre et la langue

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