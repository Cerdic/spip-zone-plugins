<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  as original founders of spip                                           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

// http://doc.spip.org/@recuperer_parametres_url
function recuperer_parametres_url(&$fond, $url) {
	global $contexte;
	$id_objet = 0;

	// Migration depuis anciennes URLs ?
	if ($GLOBALS['_SERVER']['REQUEST_METHOD'] != 'POST' AND
	(preg_match(
	',(^|/)(article|breve|rubrique|mot|auteur|site)(\.php3?|[0-9]+\.html)'
	.'([?&].*)?$,', $url, $regs)
	)) {
		$type = $regs[3];
		$id_objet = intval($GLOBALS[$id_table_objet = id_table_objet($type)]);
	}

	/* Compatibilite urls-page */
	else if (preg_match(
	',[?/&](article|breve|rubrique|mot|auteur|site)[=]?([0-9]+),',
	$url, $regs)) {
		$type = $regs[1];
		$id_objet = $regs[2];
	}

	if ($id_objet) {
		$func = "generer_url_$type";
		$url_propre = $func($id_objet);
		if (strlen($url_propre)
		AND !strstr($url,$url_propre)) {
			include_spip('inc/headers');
			http_status(301);
			// recuperer les arguments supplementaires (&debut_xxx=...)
			$reste = preg_replace('/^&/','?',
				preg_replace("/[?&]$id_table_objet=$id_objet/",'',$regs[5]));
			redirige_par_entete("$url_propre$reste");
		}
	}
	/* Fin compatibilite anciennes urls */


	// Chercher les valeurs d'environnement qui indiquent l'url-propre
	if (isset($GLOBALS['_SERVER']['REDIRECT_url_propre']))
		$url_propre = $GLOBALS['_SERVER']['REDIRECT_url_propre'];
	elseif (isset($GLOBALS['HTTP_ENV_VARS']['url_propre']))
		$url_propre = $GLOBALS['HTTP_ENV_VARS']['url_propre'];
	else {
		$url = substr($url, strrpos($url, '/') + 1);
		$url_propre = preg_replace(',[?].*,', '', $url);
	}
	// Mode Query-String ?
	$adapter_le_fond = false;
	if (!$url_propre
	AND preg_match(',([?])([^=/?&]+)(&.*)?$,', $GLOBALS['REQUEST_URI'], $r)) {
		$url_propre = $r[2];
		$adapter_le_fond = true;
	}
	if (!$url_propre) return;

	// Compatilibite avec propres2
	$url_propre = preg_replace(',\.html$,i', '', $url_propre);

	// Detecter les differents types d'objets demandes
	if (preg_match(',^\+-(.*?)-?\+?$,', $url_propre, $regs)) {
		$type = 'mot';
		$url_propre = $regs[1];
	}
	else if (preg_match(',^-(.*?)-?$,', $url_propre, $regs)) {
		$type = 'rubrique';
		$url_propre = $regs[1];
	}
	else if (preg_match(',^\+(.*?)\+?$,', $url_propre, $regs)) {
		$type = 'breve';
		$url_propre = $regs[1];
	}
	else if (preg_match(',^_(.*?)_?$,', $url_propre, $regs)) {
		$type = 'auteur';
		$url_propre = $regs[1];
	}
	else if (preg_match(',^@(.*?)@?$,', $url_propre, $regs)) {
		$type = 'syndic';
		$url_propre = $regs[1];
	}
	else {
		$type = 'article';
		preg_match(',^(.*)$,', $url_propre, $regs);
		$url_propre = $regs[1];
	}

	$table = "spip_".table_objet($type);
	$col_id = id_table_objet($type);
	$result = spip_query("SELECT $col_id FROM $table WHERE url_propre=" . _q($url_propre));

	if ($row = spip_fetch_array($result)) {
		$contexte[$col_id] = $row[$col_id];
	}

	// En mode Query-String, on fixe ici le $fond utilise
	if ($adapter_le_fond) {
		$fond = $type;
		if ($type == 'syndic') $fond = 'site';
	}

	return;
}
?>
