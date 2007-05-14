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
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		if (preg_match(
		',(^|/)(article|breve|rubrique|mot|auteur|site)(\.php3?|[0-9]+\.html)'
		.'([?&].*)?$,', $url, $regs)
		) {
			$type = $regs[3];
			$id_table_objet = id_table_objet($type);
			$id_objet = intval($GLOBALS[$id_table_objet]);
		}

		/* Compatibilite urls-page */
		else if (preg_match(
		',[?/&](article|breve|rubrique|mot|auteur|site)[=]?([0-9]+),',
		$url, $regs)) {
			$type = $regs[1];
			$id_objet = $regs[2];
		}
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
			$reste .= preg_replace('/&/','?',
				preg_replace('/[?&]'.$type.'[=]?'.$id_objet.'/','',
				substr($url, strpos($url,'?'))));
			redirige_par_entete("$url_propre$reste");
		}
	}
	/* Fin compatibilite anciennes urls */


	// Chercher les valeurs d'environnement qui indiquent l'url-propre
	if (isset($_SERVER['REDIRECT_url_propre']))
		$url_propre = $_SERVER['REDIRECT_url_propre'];
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

	// Compatilibite avec propres2 et constantes hors url
	$url_propre = preg_replace(
		array(	',\.html$,i',
				'/^' . preg_quote(_debut_urls_libres) . '/',
				'/' . preg_quote(_terminaison_urls_libres) . '$/'
			 ), '', $url_propre);

	// rechercher dans la table des urls
	$result = spip_query("SELECT * FROM spip_urls WHERE url=" . _q($url_propre));

	if ($row = spip_fetch_array($result)) {
		$col_id = id_table_objet($type = $row['type']);
		$contexte[$col_id] = $row['id_objet'];
	} else {
		// mode transitoire repris de propres ?
		// Detecter les differents types d'objets demandes d'apres "hieroglyphes"
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
			// on a trouve notre bebe
			$contexte[$col_id] = $row[$col_id];
			// se rappeler de cette version comme la plus ancienne ...
			// meme si semblable a generer_url_xxx qui suit qui en fera peut-etre un second
			_store_url($url_propre, $type, $row[$col_id], $prefix = '', $version = 0);
			// generer spip_urls pour la prochaine fois
			if (function_exists($fun = 'generer_url_' . $type)) {
				$gurl = $fun($row[$col_id]);
				spip_log('url libre generee sur demande: ' . $gurl);
			}
		}
	}

	// En mode Query-String, on fixe ici le $fond utilise
	if ($adapter_le_fond) {
		$fond = $type;
		if ($type == 'syndic') $fond = 'site';
	}

	return;
}
?>
