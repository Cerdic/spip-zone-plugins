<?php

/**
 * Retourne un traducteur disponible
 * @return \TT_Traducteur|false
 */
function TT_traducteur(){
	static $traducteur = null;
	if (is_null($traducteur)){
		include_spip('inc/config');

		$traducteur = false;

		$traducteurs_dispo = [
			'bing' => '_BING_APIKEY',
			'deepl' => '_DEEPL_APIKEY',
			'google' => '_GOOGLETRANSLATE_APIKEY',
			'yandex' => '_YANDEX_APIKEY',
		];
		$classes = [
			'bing' => 'TT_Traducteur_Bing',
			'deepl' => 'TT_Traducteur_DeepL',
			'google' => 'TT_Traducteur_GGTranslate',
			'yandex' => 'TT_Traducteur_Yandex',
		];

		foreach ($traducteurs_dispo as $traducteur_dispo => $nom_constante){
			if ((defined($nom_constante) and $key = constant($nom_constante))
				or $key = lire_config('traduiretexte/cle_' . $traducteur_dispo)){
				$class = $classes[$traducteur_dispo];
				if (!class_exists($class)) {
					include_spip('inc/traducteurs');
				}
				$traducteur = new $class($key);
				break;
			}
		}

		if (!$traducteur and defined('_TRANSLATESHELL_CMD')){
			$traducteur = new TT_Traducteur_Shell();
		}
	}
	return $traducteur;
}


/**
 * Découpe un code HTML en paragraphes.
 *
 * Découpe un texte en autant de morceaux que de balises `<p>`.
 * Cependant, si la longueur du paragraphe dépasse `$maxlen` caractères,
 * il est aussi découpé.
 * @param string $texte
 *     Texte à découper
 * @param int $maxlen
 *     Nombre maximum de caractères.
 * @param bool $html
 *     True pour conserver les balises HTML ; false pour les enlever.
 * @return array
 *     Couples [[hash => '..',  'texte' => 'paragraphe'],...]
 */
function TT_decouper_texte($texte, $maxlen = 0, $html = true){
	$liste = array();
	$texte = trim($texte);

	if (strlen($texte)==0){
		return $liste;
	}

	$prep = html2unicode($texte);
	$prep = preg_split(",(<p\b[^>]*>),i", $prep, -1, PREG_SPLIT_DELIM_CAPTURE);
	// 0, 2, 4, 6... : le texte
	// 1, 3, 5, 7... : les separateurs <p..>

	for ($i=0;$i<count($prep);$i+=2) {

		// remettre le <p...> en debut de ligne si besoin
		$line = (($html and $i>0) ? $prep[$i-1] : '') . $prep[$i];

		if (strlen($line)) {
			if (!$html){
				$line = preg_replace(",<[^>]*>,i", " ", $line);
			}

			if ($maxlen){
				$a = array();
				// max line = XXX chars
				while (mb_strlen($line)>$maxlen){
					$len = intval($maxlen*0.6); // 60% de la longueur
					$debut = mb_substr($line, 0, $len);
					$suite = mb_substr($line, $len);
					$point = mb_strpos($suite, '.');

					// chercher une fin de phrase pas trop loin
					// ou a defaut, une virgule ; au pire un espace
					if ($point===false){
						$point = mb_strpos(preg_replace('/[,;?:!]/', ' ', $suite), ' ');
					}
					if ($point===false){
						$point = mb_strpos($suite, ' ');
					}
					if ($point===false){
						$point = 0;
					}
					$a[] = trim($debut . mb_substr($suite, 0, 1+$point));
					$line = mb_substr($line, $len+1+$point);
				}
				foreach ($a as $l){
					$liste[] = array('hash' => md5($l), 'texte' => $l);
				}
			}

			$line = trim($line);
			$liste[] = array('hash' => md5($line), 'texte' => $line);
		}
	}

	return $liste;
}


/**
 * Traduire sans utiliser le cache ni mettre en cache le resultat
 *
 * Retourne le texte traduit encadré d’une div indiquant la langue et sa direction.
 *
 * @param string $texte
 * @param string $destLang
 * @param string $srcLang
 * @param bool $raw
 * @return string|false
 */
function traduire_texte($texte, $destLang = 'fr', $srcLang = 'en'){
	if (strlen(trim($texte))==0){
		return '';
	}

	//$text = rawurlencode( $text );
	$destLang = urlencode($destLang);
	$srcLang = urlencode($srcLang);

	$traducteur = TT_traducteur();
	if (!$traducteur){
		return false;
	}

	$trans = $traducteur->traduire($texte, $destLang, $srcLang);

	if (strlen($trans)){
		$ltr = lang_dir($destLang, 'ltr', 'rtl');
		return "<div dir='$ltr' lang='$destLang'>$trans</div>";
	} else {
		return false;
	}
}


/**
 * Traduire avec un cache
 *
 * Le texte est découpé en paragraphe ; chaque paragraphe est traduit et mis en cache.
 * Si un paragraphe dépasse la taille maximale acceptée par le traducteur, il sera découpé
 * lui aussi en morceaux.
 *
 * @param string $texte
 * @param string $destLang
 * @param string $srcLang
 * @param array $options {
 *   @var bool $raw
 *         Retourne un tableau des blocs [ [hash, source, trad, new(bool)],... ]
 *   @var bool $throw
 *         Lancer une exception en cas d'erreur
 * }
 * @return string|false|array
 * @throws Exception
 */
function traduire($texte, $destLang = 'fr', $srcLang = 'en', $options = array()){
	if (strlen(trim($texte))==0){
		return '';
	}
	$throw = ((isset($options['throw']) and $options['throw']) ? true : false);

	$traducteur = TT_traducteur();
	if (!$traducteur){
		if ($throw) {
			throw new \Exception(_T('traduiretexte:erreur_aucun_traducteur_disponible'));
		}
		return false;
	}

	$parts = TT_decouper_texte($texte, $traducteur->maxlen, true);

	$deja_traduits = sql_allfetsel(
		array('hash', 'texte'),
		"spip_traductions",
		array(
			sql_in('hash', array_column($parts, 'hash')),
			'langue = ' . sql_quote($destLang),
		)
	);

	if ($deja_traduits){
		$deja_traduits = array_column($deja_traduits, 'texte', 'hash');
	}

	$inserts = array();
	$fail = false;
	foreach ($parts as $k=>$part){
		$hash = $part['hash'];
		if (!isset($deja_traduits[$hash])) {
			$paragraphe = $part['texte'];
			$trad = $traducteur->traduire($paragraphe, $destLang, $srcLang, $throw);
			if ($trad === false) {
				spip_log('[' . $destLang . "] ECHEC $paragraphe", 'translate' . _LOG_ERREUR);
				$fail = true;
				break;
			}
			else {
				$deja_traduits[$hash] = $trad;
				$inserts[] = array(
					"hash" => $hash,
					"texte" => $trad,
					"langue" => $destLang
				);
			}
		}
		// il faut garder les texte source si on demande un retour brut
		$parts[$k]['trad'] = $deja_traduits[$hash];
	}
	unset($deja_traduits);

	// fail ou pas, on mets en cache les traductions faites
	if ($inserts){
		sql_insertq_multi("spip_traductions", $inserts);
	}

	if ($fail) {
		return "";
	}

	// retour brut
	if (!empty($options['raw'])){
		$new_hashes = array_column($inserts, 'hash');
		$res = array();
		while(count($parts)) {
			$part = array_shift($parts);
			$res[] = array(
				'hash' => $part['hash'],
				'source' => $part['texte'],
				'trad' => $part['trad'],
				'new' => in_array($part['hash'], $new_hashes),
			);
		}
		return $res;
	}

	$traductions = array_column($parts, 'trad');
	unset($parts);

	$traductions = implode(" ", $traductions);
	$ltr = lang_dir($destLang, 'ltr', 'rtl');
	return "<div dir='$ltr' lang='$destLang'>$traductions</div>";
}

