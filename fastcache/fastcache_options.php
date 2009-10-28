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

		if (defined('_FC_KEY')) {

			// preparer les entetes
			$head = #'<'."?php\n".
				"// ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n" .
				"header('Vary: Cookie, Accept-Encoding');\n";

			foreach ($page['entetes'] as $x => $v)
				if ($x !== 'X-Spip-Cache')
					$head .= "header('$x: ".addslashes($v)."');\n";

			// entetes pour les stats
			foreach(array('id_article', 'id_breve', 'id_rubrique') as $id)
				if (isset($GLOBALS[$id]))
					$head .= "\$GLOBALS['$id'] = "
					.var_export(intval($GLOBALS[$id]),true).";\n";

			// version MSIE
			$ie = (_FC_IE_PNGHACK AND $html)
				? Fastcache_versionie($texte)
				: null;

			// stocker les caches
			$ok = cache_set(_FC_KEY,
				array(
					'head' => $head,
					'body' => $texte
						.(_FC_DEBUG?"\n<!-- read "._FC_KEY." -->\n":''),
					'ie' => $ie
						.(_FC_DEBUG?"\n<!-- read "._FC_KEY." -->\n":''),
					'time' => @filemtime(_FILE_META)
				),
				_FC_PERIODE
			);

			return $texte
				. (_FC_DEBUG
					? ($ok
						? "\n<!-- stored "._FC_KEY." -->\n"
						: "\n<!-- error "._FC_KEY." -->\n"
						)
					: ''
				);
		}

	}

	// else ...
	return $texte;
}

# s'inserer au *debut* du pipeline affichage_final pour etre avant f_surligne etc
# mais de preference apres mutualisation_url_img_courtes pour qu'il s'applique
$GLOBALS['spip_pipeline']['affichage_final'] = preg_replace(',\|mutualisation_url_img_courtes|^,','\0|Fastcache_affichage_final', $GLOBALS['spip_pipeline']['affichage_final']);

# Un recalcul provoque l'invalidation, par l'astuce du touch
if (isset($_GET['var_mode'])) @touch(_FILE_META);

?>
