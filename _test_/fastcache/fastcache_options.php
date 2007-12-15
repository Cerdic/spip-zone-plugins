<?php

# pipeline affichage_final pour stocker les pages dans le cache rapide
if (!defined("_ECRIRE_INC_VERSION")) return;

function Fastcache_versionie($page) {
	if (!_FC_IE_PNGHACK
	OR strpos($page, 'BackgroundImageCache')
	OR !include_spip('inc/msie'))
		return $page;
	
	$msiefix = charger_fonction('msiefix', 'inc');
	return $msiefix($page);
}

function Fastcache_lancer_stats() {
	if ($GLOBALS['meta']["activer_statistiques"] == 'oui') {
		$stats = charger_fonction('stats', 'public');
		$stats();
	}
}

function Fastcache_affichage_final($texte) {
	global $page, $html; # dommage le pipeline ne connait pas les entetes...

	if ($page['duree']
	AND isset($page['entetes'])
	AND isset($page['entetes']['X-Fast-Cache'])) {

		// verifier que le lanceur est OK
		if (defined('_FC_LANCEUR')
		AND !file_exists(_FC_LANCEUR)) {
			include_spip('creer_fastcache');
			creer_fastcache();
		}

		if (defined('_FC_FILE')) {

			// preparer les entetes
			$head = '<'."?php\n"
			."// ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n"
			."header('Vary: Cookie, Accept-Encoding');\n";

			foreach ($page['entetes'] as $x => $v)
				if ($x !== 'X-Spip-Cache')
					$head .= "header('$x: ".addslashes($v)."');\n";

			// entetes pour les stats
			foreach(array('id_article', 'id_breve', 'id_rubrique') as $id)
				if (isset($GLOBALS[$id]))
					$head .= "\$GLOBALS['$id'] = "
					.var_export(intval($GLOBALS[$id]),true).";\n";

			// stocker les caches
			$ok = ecrire_fichier(_FC_FILE.'_head.inc', $head);
			$ok &= ecrire_fichier(_FC_FILE, $texte
			.(_FC_DEBUG?"\n<!-- read "._FC_FILE." -->\n":''));
			$ok &= ecrire_fichier(_FC_FILE.'.gz', $texte
			.(_FC_DEBUG?"\n<!-- read "._FC_FILE.".gz -->\n":''));

			// version MSIE
			if (_FC_IE_PNGHACK
			AND $html) {
				$textemsie = Fastcache_versionie($texte);
				$ok &= ecrire_fichier(_FC_FILE.'_ie', $textemsie
				.(_FC_DEBUG?"\n<!-- read "._FC_FILE."_ie -->\n":''));
				$ok &= ecrire_fichier(_FC_FILE.'_ie.gz', $textemsie
				.(_FC_DEBUG?"\n<!-- read "._FC_FILE."_ie.gz -->\n":''));
			}

			supprimer_fichier(_FC_FILE.'.lock');

			if (!$ok) {
				include_once 'ecrire/inc_version.php';
				sous_repertoire(dirname(_FC_DIR_CACHE), basename(_FC_DIR_CACHE), true);
			}

			return $texte
				. (_FC_DEBUG
					? ($ok
						? "\n<!-- stored "._FC_FILE." -->\n"
						: "\n<!-- error "._FC_FILE." -->\n"
						)
					: ''
				);
		}

	}

	// else ...
	return $texte;
}

# s'inserer au *debut* du pipeline affichage_final pour etre avant f_surligne etc
$GLOBALS['spip_pipeline']['affichage_final'] = '|Fastcache_affichage_final'.$GLOBALS['spip_pipeline']['affichage_final'];


# supprimer les fc_cache trop vieux ?
if (isset($_GET['var_mode'])) {
	array_map('supprimer_fichier', preg_files(_FC_DIR_CACHE, '/fc_'));
}

?>
