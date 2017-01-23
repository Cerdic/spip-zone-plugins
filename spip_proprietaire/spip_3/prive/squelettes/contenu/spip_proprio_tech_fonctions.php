<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
//ini_set('display_errors', 1);error_reporting(E_ALL);

function spip_proprio_exporter() {
	$ok_export = _request('do_proprio_export');
	if (empty($ok_export) || $ok_export != 'oui') {
		return false;
	}

	$data = array(
		'configuration' => _request('configuration'),
		'languages' => _request('languages'),
	);

	$code = '';
	if (isset($data['configuration']) && $data['configuration'] == 'oui') {
		$conf = spip_proprio_recuperer_config();
		$code .= "\n".'$proprio_config = '.var_export($conf, true).";\n";
	}
	if (isset($data['languages']) && $data['languages'] == 'oui') {
		$langues_du_site = array('fr');
		foreach (array('langues_utilisees', 'langues_multilingue', 'langue_site') as $ln_meta) {
			if (isset($GLOBALS['meta'][$ln_meta])) {
				$langues_du_site = array_merge($langues_du_site, explode(',', $GLOBALS['meta'][$ln_meta]));
			}
		}
		$langues_du_site = array_unique($langues_du_site);
		foreach ($langues_du_site as $ln) {
			spip_proprio_proprietaire_texte('', '', $ln);
			$code .= "\n".'$proprio_i18n_proprietaire_'.$ln.' = '.var_export($GLOBALS['i18n_proprietaire_'.$ln], true).";\n";
		}
	}

	$code = "// Exportation config SPIP Proprio\n// Site d'origine : ".$GLOBALS['meta']['nom_site']."\n// Cree le : ".date('Y-m-d H:i:s')."\n".$code;
	$fichier_dump = 'spiproprio_export_'.date('Ymd').'.php.gz';
	$log = ecrire_fichier(_DIR_DUMP.$fichier_dump, '<'."?php\n$code\n?".'>', true);

	if ($log) {
		return _T('spipproprio:ok_export', array('fichier' => _DIR_DUMP.$fichier_dump));
	}

	return _T('spipproprio:erreur_export');
}

function spip_proprio_importer() {
	$ok_import = _request('do_proprio_import');
	if (empty($ok_import) || $ok_import != 'oui') {
		return false;
	}

	$file = _request('import_archive');
	if (is_null($file)) {
		return;
	}
	$ok = false;

	$archive = _DIR_DUMP.$file;
	if (@file_exists($archive) and $gz = gzopen($archive, 'rb')) {
		$php = '';
		while (!gzeof($gz)) {
			$text = gzgets($gz, 1024);
			if (!substr_count($text, '<?php') && !substr_count($text, '?>')) {
				$php .= $text;
			}
		}
//	    var_export($php); exit;
		eval("$php");
		if (isset($proprio_config)) {
			include_spip('inc/meta');
			$ok = ecrire_meta(_META_SPIP_PROPRIO, serialize($proprio_config), 'non');
			ecrire_metas();
		}
		foreach (explode(',', $GLOBALS['meta']['langues_proposees']) as $ln_spip) {
			$ln_glb = "proprio_i18n_proprietaire_$ln_spip";
			if (isset($$ln_glb)) {
				$ok = creer_fichier_textes_proprietaire($ln_glb, $ln_spip);
			}
		}
	}

	if ($ok) {
		return _T('spipproprio:ok_import');
	} else {
		return _T('spipproprio:erreur_import');
	}
}

function liste_proprio_dump() {
	$str = '';
	$liste_dump = preg_files(_DIR_DUMP, '\.php\.gz?$', 50, false);
	if ($liste_dump && count($liste_dump)) {
		foreach ($liste_dump as $i => $file) {
			$filename = substr($file, strrpos($file, '/') + 1);
			$filename_short = str_replace('.php.gz', '', $filename);
			$str .= "<option value='$filename'>$filename_short</option>";
		}
	}

	return strlen($str) ? $str : 0;
}
