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
 *     Couples [hash => paragraphe]
 */
function TT_decouper_texte($texte, $maxlen = 0, $html = true){
	$liste = array();
	$texte = trim($texte);

	if (strlen($texte)==0){
		return $liste;
	}

	$prep = html2unicode($texte);
	$options = $html ? (PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) : PREG_SPLIT_NO_EMPTY;
	$prep = preg_split(",(<p\b[^>]*>),i", $prep, -1, $options);

	$last = ''; // remettre les <p> en début de ligne.
	foreach ($prep as $line){
		if ($html){
			if (preg_match(",^<p\b[^>]*>$,i", $line)){
				$last .= $line;
				continue;
			} else {
				$line = $last . $line;
				$last = '';
			}
		} else {
			$line = preg_replace(",<[^>]*>,i", " ", $line);
		}

		if ($maxlen){
			// max line = XXX chars
			$a = array();
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
		}
		$a[] = trim($line);

		foreach ($a as $l){
			$liste[md5($l)] = $l;
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
 *         Retourne un tableau des couples [ hash => [source, trad, new(bool)] ]
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

	$hashes = TT_decouper_texte($texte, $traducteur->maxlen, true);
	$traductions = array_fill_keys(array_keys($hashes), null);
	$deja_traduits = sql_allfetsel(
		array('hash', 'texte'),
		"spip_traductions",
		array(
			sql_in('hash', array_keys($hashes)),
			'langue = ' . sql_quote($destLang),
		)
	);

	if ($deja_traduits){
		$deja_traduits = array_column($deja_traduits, 'texte', 'hash');
		foreach ($deja_traduits as $hash => $trad){
			$traductions[$hash] = $trad;
		}
	}

	$todo = array_filter($traductions, 'is_null');
	$inserts = array();
	$fail = false;
	foreach ($todo as $hash => $dummy){
		$paragraphe = $hashes[$hash];
		$trad = $traducteur->traduire($paragraphe, $destLang, $srcLang, $throw);
		if ($trad !== false){
			$traductions[$hash] = $trad;
			$inserts[] = array(
				"hash" => $hash,
				"texte" => $trad,
				"langue" => $destLang
			);
		} else {
			spip_log('[' . $destLang . "] ECHEC $paragraphe", 'translate');
			$fail = true;
			break;
		}
	}
	if ($inserts){
		sql_insertq_multi("spip_traductions", $inserts);
	}

	if ($fail) {
		return "";
	}

	// retour brut
	if (!empty($options['raw'])){
		$res = array();
		foreach ($hashes as $hash => $paragraphe){
			$res[$hash] = array(
				'source' => $paragraphe,
				'trad' => $traductions[$hash],
				'new' => array_key_exists($hash, $todo)
			);
		}
		return $res;
	}

	$traductions = implode(" ", $traductions);
	$ltr = lang_dir($destLang, 'ltr', 'rtl');
	return "<div dir='$ltr' lang='$destLang'>$traductions</div>";
}

