<?php

function precode_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/precode.css') . '" type="text/css" media="all" />' . "\n";
	$flux .= '<script type="text/javascript" src="' . find_in_path('js/clipboard.min.js') . '"></script>' . "\n";

	// produire le js depuis un squelette pour pouvoir traduire les libellés
	$js   = produire_fond_statique('js/precode.js');
	$flux .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";

	return $flux;
}

function precode_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/precode.css') . '" type="text/css" media="all" />' . "\n";

	return $flux;
}

if(!function_exists('produire_fond_statique')) {
	/**
	 * Produire un fichier statique à partir d'un squelette dynamique
	 *
	 * Permet ensuite à Apache de le servir en statique sans repasser
	 * par spip.php à chaque hit sur le fichier.
	 *
	 * Si le format (css ou js) est passe dans `contexte['format']`, on l'utilise
	 * sinon on regarde si le fond finit par .css ou .js, sinon on utilie "html"
	 *
	 * @uses urls_absolues_css()
	 *
	 * @param string $fond
	 * @param array  $contexte
	 * @param array  $options
	 * @param string $connect
	 *
	 * @return string
	 */
	function produire_fond_statique($fond, $contexte = array(), $options = array(), $connect = '') {
		if (isset($contexte['format'])) {
			$extension = $contexte['format'];
			unset($contexte['format']);
		} else {
			$extension = "html";
			if (preg_match(',[.](css|js|json)$,', $fond, $m)) {
				$extension = $m[1];
			}
		}
		// recuperer le contenu produit par le squelette
		$options['raw'] = true;
		$cache          = recuperer_fond($fond, $contexte, $options, $connect);

		// calculer le nom de la css
		$dir_var  = sous_repertoire(_DIR_VAR, 'cache-' . $extension);
		$nom_safe = preg_replace(",\W,", '_', str_replace('.', '_', $fond));
		$filename = $dir_var . $extension . "dyn-$nom_safe-" . substr(md5($fond . serialize($contexte) . $connect), 0,
				8) . ".$extension";

		// mettre a jour le fichier si il n'existe pas
		// ou trop ancien
		// le dernier fichier produit est toujours suffixe par .last
		// et recopie sur le fichier cible uniquement si il change
		if (!file_exists($filename)
			or !file_exists($filename . ".last")
			or (isset($cache['lastmodified']) and $cache['lastmodified'] and filemtime($filename . ".last") < $cache['lastmodified'])
			or (defined('_VAR_MODE') and _VAR_MODE == 'recalcul')
		) {
			$contenu = $cache['texte'];
			// passer les urls en absolu si c'est une css
			if ($extension == "css") {
				$contenu = urls_absolues_css($contenu,
					test_espace_prive() ? generer_url_ecrire('accueil') : generer_url_public($fond));
			}

			// ne pas insérer de commentaire si c'est du json
			if ($extension != "json") {
				$comment = "/* #PRODUIRE{fond=$fond";
				foreach ($contexte as $k => $v) {
					$comment .= ",$k=$v";
				}
				// pas de date dans le commentaire car sinon ca invalide le md5 et force la maj
				// mais on peut mettre un md5 du contenu, ce qui donne un aperu rapide si la feuille a change ou non
				$comment .= "}\n   md5:" . md5($contenu) . " */\n";
			}
			// et ecrire le fichier
			ecrire_fichier($filename . ".last", $comment . $contenu);
			// regarder si on recopie
			if (!file_exists($filename)
				or md5_file($filename) !== md5_file($filename . ".last")
			) {
				@copy($filename . ".last", $filename);
				spip_clearstatcache(true, $filename); // eviter que PHP ne reserve le vieux timestamp
			}
		}

		return $filename;
	}
	
}

if(!function_exists('spip_clearstatcache')) {
	/**
	 * clearstatcache adapte a la version PHP
	 *
	 * @param bool $clear_realpath_cache
	 * @param null $filename
	 */
	function spip_clearstatcache($clear_realpath_cache = false, $filename = null) {
		if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
			// Below PHP 5.3, clearstatcache does not accept any function parameters.
			return clearstatcache();
		} else {
			return clearstatcache($clear_realpath_cache, $filename);
		}

	}
}