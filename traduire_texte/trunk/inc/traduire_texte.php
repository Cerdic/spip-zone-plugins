<?php


/** Description de l’outil de traduction */
abstract class TT_Traducteur {
	/** @var string Nom de l’outil */
	public $type;
	/** @var string Clé d’api */
	public $apikey;
	/** @var string Chemin éventuel */
	public $path;
	/** @var int Maximum de caractères traitables en un coup */
	public $maxlen;

	/**
	 * TT_Traducteur constructor.
	 * @param string $apikey
	 */
	public function __construct($apikey = null) {
		$this->apikey = $apikey;
	}

	abstract public function traduire($texte, $destLang = 'fr', $srcLang = 'en');
}

class TT_Traducteur_Bing extends TT_Traducteur {
	public $type = 'bing';
	public $maxlen = 10000;
	function traduire($texte, $destLang = 'fr', $srcLang = 'en') {
		return translate_requestCurl_bing($this->apikey, $texte, $srcLang, $destLang);
	}
}

class TT_Traducteur_GGTranslate extends TT_Traducteur {
	public $type = 'google';
	public $maxlen = 4500;
	function traduire($texte, $destLang = 'fr', $srcLang = 'en') {
		$destLang = urlencode($destLang);
		$srcLang = urlencode($srcLang);
		return translate_requestCurl("key=" . $this->apikey . "&source=$srcLang&target=$destLang&q=" . rawurlencode($texte));
	}
}


class TT_Traducteur_Shell extends TT_Traducteur {
	public $type = 'shell';
	public $maxlen = 1000;
	function traduire($texte, $destLang = 'fr', $srcLang = 'en') {
		return translate_shell($texte, $destLang);
	}
}

/**
 * Retourne un traducteur disponible
 * @return \TT_Traducteur|false
 */
function TT_traducteur() {
	static $traducteur = null;
	if (is_null($traducteur)) {
		include_spip('inc/config');
		if (defined('_BING_APIKEY')) {
			$traducteur = new TT_Traducteur_Bing(_BING_APIKEY, 10000);
		} elseif (defined('_GOOGLETRANSLATE_APIKEY')) {
			$traducteur = new TT_Traducteur_GGTranslate(_GOOGLETRANSLATE_APIKEY);
		} elseif (defined('_TRANSLATESHELL_CMD')) {
			$traducteur = new TT_Traducteur_Shell();
			$traducteur->path = _TRANSLATESHELL_CMD;
		} elseif ($k = lire_config('traduiretexte/cle_bing')) {
			$traducteur = new TT_Traducteur_Bing($k);
		} elseif ($k = lire_config('traduiretexte/cle_google')) {
			$traducteur = new TT_Traducteur_GGTranslate($k);
		} else {
			$traducteur = false;
		}
	}
	return $traducteur;
}

function translate_requestCurl($parameters) {
	# $url_page = "https://ajax.googleapis.com/ajax/services/language/translate?";
	$url_page = "https://www.googleapis.com/language/translate/v2?";

	# $parameters_explode = explode("&", $parameters);
	# $nombre_param = count($parameters_explode);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url_page);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, !empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "");
	#curl_setopt($ch, CURLOPT_POST, nombre_param);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$body = curl_exec($ch);
	curl_close($ch);

	$json = json_decode($body, true);

	if (isset($json["error"])) {
		spip_log($json, 'translate');
		return false;
	}
	return urldecode($json["data"]["translations"][0]["translatedText"]);
}

function translate_requestCurl_bing($apikey, $text, $srcLang, $destLang) {
	// Bon sang, si tu n'utilises pas .NET, ce truc est documenté par les corbeaux
	// attaquer le machin en SOAP (la méthode HTTP ne convient que pour des textes très courts (GET, pas POST)

	if (strlen(trim($text)) == 0) return '';
	$client = new SoapClient("http://api.microsofttranslator.com/V2/Soap.svc");

	$params = array(
		'appId' => $apikey,
		'text' => $text,
		'from' => $srcLang,
		'to' => $destLang);
	try {
		$translation = $client->translate($params);
	} catch (Exception $e) {
		return false;
	}

	return $translation->TranslateResult;
}


function translate_shell($text, $destLang = 'fr') {
	if (strlen(trim($text)) == 0) return '';
	$prep = str_replace("\n", " ", html2unicode($text));
	$prep = preg_split(",<p\b[^>]*>,i", $prep);
	$trans = array();
	foreach ($prep as $k => $line) {
		if ($k > 0) $trans[] = '<p>';
		$line = preg_replace(",<[^>]*>,i", " ", $line);
		// max line = 1000 chars
		$a = array();
		while (mb_strlen($line) > 1000) {
			$debut = mb_substr($line, 0, 600);
			$suite = mb_substr($line, 600);
			$point = strpos($suite, '.');

			// chercher une fin de phrase pas trop loin
			// ou a defaut, une virgule ; au pire un espace
			if ($point === false) {
				$point = strpos(preg_replace('/[,;?:!]/', ' ', $suite), ' ');
			}
			if ($point === false) {
				$point = strpos($suite, ' ');
			}
			if ($point === false) {
				$point = 0;
			}
			$a[] = trim($debut . mb_substr($suite, 0, 1 + $point));
			$line = mb_substr($line, 600 + 1 + $point);
		}
		$a[] = trim($line);
		foreach ($a as $l) {
			spip_log("IN: " . $l, 'translate');
			$trad = translate_line($l, $destLang);
			spip_log("OUT: " . $trad, 'translate');
			$trans[] = $trad;
		}
	}

	return join("\n", $trans);
}

function translate_line($text, $destLang) {
	if (strlen(trim($text)) == 0) return '';
	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w")
	);
	$cmd = _TRANSLATESHELL_CMD . ' -b ' . ':' . escapeshellarg($destLang);
	$cmdr = proc_open($cmd, $descriptorspec, $pipes);
	if (is_resource($cmdr)) {
		fwrite($pipes[0], $text) && fclose($pipes[0]);
		$trad = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
	}
	return $trad;
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
function TT_decouper_texte($texte, $maxlen = 0, $html = true) {
	$liste = array();
	$texte = trim($texte);

	if (strlen($texte) == 0) {
		return $liste;
	}

	$prep = html2unicode($texte);
	$options = $html ? (PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) : PREG_SPLIT_NO_EMPTY;
	$prep = preg_split(",(<p\b[^>]*>),i", $prep, -1, $options);

	$last = ''; // remettre les <p> en début de ligne.
	foreach ($prep as $line) {
		if ($html) {
			if (preg_match(",^<p\b[^>]*>$,i", $line)) {
				$last .= $line;
				continue;
			} else {
				$line = $last . $line;
				$last = '';
			}
		} else {
			$line = preg_replace(",<[^>]*>,i", " ", $line);
		}

		if ($maxlen) {
			// max line = XXX chars
			$a = array();
			while (mb_strlen($line) > $maxlen) {
				$len = intval($maxlen * 0.6); // 60% de la longueur
				$debut = mb_substr($line, 0, $len);
				$suite = mb_substr($line, $len);
				$point = strpos($suite, '.');

				// chercher une fin de phrase pas trop loin
				// ou a defaut, une virgule ; au pire un espace
				if ($point === false) {
					$point = strpos(preg_replace('/[,;?:!]/', ' ', $suite), ' ');
				}
				if ($point === false) {
					$point = strpos($suite, ' ');
				}
				if ($point === false) {
					$point = 0;
				}
				$a[] = trim($debut . mb_substr($suite, 0, 1 + $point));
				$line = mb_substr($line, $len + 1 + $point);
			}
		}
		$a[] = trim($line);

		foreach ($a as $l) {
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
function traduire_texte($texte, $destLang = 'fr', $srcLang = 'en') {
	if (strlen(trim($texte)) == 0) {
		return '';
	}

	//$text = rawurlencode( $text );
	$destLang = urlencode($destLang);
	$srcLang = urlencode($srcLang);

	$traducteur = TT_traducteur();
	if (!$traducteur) {
		return false;
	}

	$trans = $traducteur->traduire($texte, $destLang, $srcLang);

	if (strlen($trans)) {
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
 *     @var bool $raw
 *         Retourne un tableau des couples [ hash => [source, trad, new(bool)] ]
 * }
 * @return string|false|array
 */
function traduire($texte, $destLang = 'fr', $srcLang = 'en', $options = array()) {
	if (strlen(trim($texte)) == 0) {
		return '';
	}

	$traducteur = TT_traducteur();
	if (!$traducteur) {
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

	if ($deja_traduits) {
		$deja_traduits = array_column($deja_traduits, 'texte', 'hash');
		foreach ($deja_traduits as $hash => $trad) {
			$traductions[$hash] = $trad;
		}
	}

	$todo = array_filter($traductions, 'is_null');
	$inserts = array();
	foreach ($todo as $hash => $dummy) {
		$paragraphe = $hashes[$hash];
		$trad = $traducteur->traduire($paragraphe, $destLang, $srcLang);
		if ($trad) {
			spip_log('[' . $destLang . "] $paragraphe \n === $trad", 'translate');
			$traductions[$hash] = $trad;
			$inserts[] = array(
				"hash" => $hash,
				"texte" => $trad,
				"langue" => $destLang
			);
		} else {
			spip_log('[' . $destLang . "] ECHEC $paragraphe", 'translate');
		}
	}
	if ($inserts) {
		sql_insertq_multi("spip_traductions", $inserts);
	}

	// retour brut
	if (!empty($options['raw'])) {
		$res = array();
		foreach ($hashes as $hash => $paragraphe) {
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

