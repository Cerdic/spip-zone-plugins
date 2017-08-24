<?php

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

/** Description de l’outil de traduction */
class TT_Traducteur {
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
	 * @param string $type
	 * @param string $apikey
	 * @param int $maxlen
	 */
	public function __construct($type, $apikey = null, $maxlen = 0) {
		$this->type = $type;
		$this->apikey = $apikey;
		$this->maxlen = $maxlen;
	}
}

/**
 * Retourne une description du traducteur disponible
 * @return \TT_Traducteur|false
 */
function traduire_texte_traducteur() {
	static $traducteur = null;
	if (is_null($traducteur)) {
		include_spip('inc/config');
		if (defined('_BING_APIKEY')) {
			$traducteur = new TT_Traducteur('bing', _BING_APIKEY, 10000);
		} elseif (defined('_GOOGLETRANSLATE_APIKEY')) {
			$traducteur = new TT_Traducteur('google', _GOOGLETRANSLATE_APIKEY, 5000);
		} elseif (defined('_TRANSLATESHELL_CMD')) {
			$traducteur = new TT_Traducteur('shell', null, 1000);
			$traducteur->path = _TRANSLATESHELL_CMD;
		} elseif ($v = lire_config('traduiretexte/cle_bing')) {
			$traducteur = new TT_Traducteur('bing', $v, 10000);
		} elseif ($v = lire_config('traduiretexte/cle_google')) {
			$traducteur = new TT_Traducteur('google', $v, 5000);
		} else {
			$traducteur = false;
		}
	}
	return $traducteur;
}


/**
 * Traduire sans utiliser le cache ni mettre en cache le resultat
 *
 * @param string $text
 * @param string $destLang
 * @param string $srcLang
 * @return string|false
 */
function traduire_texte($text, $destLang = 'fr', $srcLang = 'en') {
	if (strlen(trim($text)) == 0) return '';

	//$text = rawurlencode( $text );
	$destLang = urlencode($destLang);
	$srcLang = urlencode($srcLang);

	$traducteur = traduire_texte_traducteur();
	if (!$traducteur) {
		return false;
	}

	// dispatcher. On pourrait faire des fonctions dédiées par traducteur.
	switch ($traducteur->type) {
		case 'bing':
			$trans = translate_requestCurl_bing($traducteur->apikey, $text, $srcLang, $destLang);
			break;

		case 'google':
			$trans = translate_requestCurl("key=" . $traducteur->apikey . "&source=$srcLang&target=$destLang&q=" . rawurlencode($text));
			break;

		case 'shell':
			$trans = translate_shell($text, $destLang);
			break;
	}

	$ltr = lang_dir($destLang, 'ltr', 'rtl');

	if (strlen($trans)) {
		return "<div dir='$ltr' lang='$destLang'>$trans</div>";
	} else {
		return false;
	}
}


/**
 * Traduire avec un cache
 *
 * @param string $text
 * @param string $destLang
 * @param string $srcLang
 * @return string|false|array
 */
function traduire($text, $destLang = 'fr', $srcLang = 'en') {
	if (strlen(trim($text)) == 0) {
		return '';
	}

	$traducteur = traduire_texte_traducteur();
	if (!$traducteur) {
		return false;
	}

	switch ($traducteur->type) {
		case 'bing':
			$text = mb_substr($text, 0, 10000, "UTF-8");
			break;

		case 'google':
			$text = mb_substr($text, 0, 4500, "UTF-8");
			break;
	}

	$hash = md5($text);

	$row = sql_fetsel("texte", "spip_traductions", "hash='$hash' AND langue ='$destLang'");

	if ($row) {
		$trad = $row["texte"];
		# echo "EN BASE : ".$hash;
	} else {
		//echo "NOUVEAU";
		$trad = traduire_texte($text, $destLang, $srcLang);
		if ($trad) {
			spip_log('[' . $destLang . "] $text \n === $trad", 'translate');
			sql_insertq("spip_traductions",
				array(
					"hash" => $hash,
					"texte" => $trad,
					"langue" => $destLang
				)
			);
		} else {
			spip_log('[' . $destLang . "] ECHEC $text", 'translate');
			$trad = false;
		}
	}

	return $trad;
}
