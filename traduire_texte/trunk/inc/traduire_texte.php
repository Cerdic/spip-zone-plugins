<?php


/** Description de l’outil de traduction */
abstract class TT_Traducteur {
	/** @var string Nom de l’outil */
	public $type;
	/** @var string Clé d’api */
	public $apikey;
	/** @var int Maximum de caractères traitables en un coup */
	public $maxlen;

	/**
	 * TT_Traducteur constructor.
	 * @param string $apikey
	 */
	public function __construct($apikey = null){
		$this->apikey = $apikey;
	}

	public function traduire($texte, $destLang = 'fr', $srcLang = 'en'){
		if (strlen(trim($texte))==0){
			return '';
		}
		$len = mb_strlen($texte);
		$extrait = mb_substr($texte, 0, 40);
		spip_log('Trad:' . $this->type . ' ' . $len . 'c. : ' . $extrait . ($len>40 ? '...' : ''), 'translate');
		return $this->_traduire($texte, $destLang, $srcLang);
	}

	abstract protected function _traduire($texte, $destLang, $srcLang);
}

/**
 * Traduire avec Bing
 */
class TT_Traducteur_Bing extends TT_Traducteur {
	public $type = 'bing';
	public $maxlen = 10000;

	protected function _traduire($texte, $destLang, $srcLang){
		// Bon sang, si tu n'utilises pas .NET, ce truc est documenté par les corbeaux
		// attaquer le machin en SOAP (la méthode HTTP ne convient que pour des textes très courts (GET, pas POST)
		try {
			$client = new \SoapClient("http://api.microsofttranslator.com/V2/Soap.svc");
			$params = array(
				'appId' => $this->apikey,
				'text' => $texte,
				'from' => $srcLang,
				'to' => $destLang
			);
			$translation = $client->translate($params);
		} catch (Exception $e) {
			spip_log($e->getMessage(), 'translate');
			return false;
		}

		return $translation->TranslateResult;
	}
}

/**
 * Traduire avec Google Translate
 */
class TT_Traducteur_GGTranslate extends TT_Traducteur {
	public $type = 'google';
	public $maxlen = 4500;

	protected function _traduire($texte, $destLang = 'fr', $srcLang = 'en'){
		$destLang = urlencode($destLang);
		$srcLang = urlencode($srcLang);

		$url_page = "https://www.googleapis.com/language/translate/v2?";
		$parameters = "key=" . $this->apikey . "&source=$srcLang&target=$destLang&q=" . rawurlencode($texte);

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

		if (isset($json["error"])){
			spip_log($json, 'translate');
			return false;
		}

		return urldecode($json["data"]["translations"][0]["translatedText"]);
	}
}

/**
 * Traduire avec Yandex
 */
class TT_Traducteur_Yandex extends TT_Traducteur {
	public $type = 'yandex';
	public $maxlen = 10000;

	protected function _traduire($texte, $destLang = 'fr', $srcLang){
		$destLang = urlencode($destLang);
		//yandex peut deviner la langue source
		if (isset($srcLang)){
			$srcLang = urlencode($srcLang);
			$lang = "$srcLang-$destLang";
		} else {
			$lang = $destLang;
		}

		$url_page = "https://translate.yandex.net/api/v1.5/tr.json/translate?";
		//  & [format=<text format>] & [options=<translation options>] & [callback=<name of the callback function>]
		$parameters = "key=" . $this->apikey . "&text=" . rawurlencode($texte) . "&lang=$lang";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_page);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, !empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$body = curl_exec($ch);
		curl_close($ch);

		//{"code":200,"lang":"fr-en","text":["hello"]}
		$json = json_decode($body, true);

		if (isset($result['code']) && $result['code']>200){
			spip_log($json, 'translate');
			return false;
		}

		return urldecode($json["text"][0]);
	}
}


class TT_Traducteur_Shell extends TT_Traducteur {
	public $type = 'shell';
	public $maxlen = 1000;

	public function _traduire($texte, $destLang = 'fr', $srcLang = 'en'){
		if (!defined('_TRANSLATESHELL_CMD')){
			spip_log('chemin de Translate shell non défini', 'translate.' . _LOG_ERREUR);
			return false;
		}
		return $this->translate_line($texte, $destLang);

		/*
		// Équivalent ~ de l’ancien fonctionnement. (qui supprimait les tags html)
		$liste = TT_decouper_texte($texte, $this->maxlen);
		foreach ($liste as $l) {
			spip_log("IN: " . $l, 'translate');
			$trad = $this->translate_line($l, $destLang);
			spip_log("OUT: " . $trad, 'translate');
			$trans[] = $trad;
		}
		return join(" ", $trans);
		*/
	}

	public function translate_line($texte, $destLang){
		if (strlen(trim($texte))==0){
			return '';
		}
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w")
		);
		$cmd = _TRANSLATESHELL_CMD . ' -b ' . ':' . escapeshellarg($destLang);
		$cmdr = proc_open($cmd, $descriptorspec, $pipes);
		if (is_resource($cmdr)){
			fwrite($pipes[0], $texte) && fclose($pipes[0]);
			$trad = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
		}
		return $trad;
	}

}

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
			'google' => '_GOOGLETRANSLATE_APIKEY',
			'yandex' => '_YANDEX_APIKEY',
		];
		$classes = [
			'bing' => 'TT_Traducteur_Bing',
			'google' => 'TT_Traducteur_GGTranslate',
			'yandex' => 'TT_Traducteur_Yandex',
		];

		foreach ($traducteurs_dispo as $traducteur_dispo => $nom_constante){
			if ((defined($nom_constante) and $key = constant($nom_constante))
				or $key = lire_config('traduiretexte/cle_' . $traducteur_dispo)){
				$class = $classes[$traducteur_dispo];
				$traducteur = new $class($key);
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
 * @return string|false|array
 * @var bool $raw
 *         Retourne un tableau des couples [ hash => [source, trad, new(bool)] ]
 * }
 */
function traduire($texte, $destLang = 'fr', $srcLang = 'en', $options = array()){
	if (strlen(trim($texte))==0){
		return '';
	}

	$traducteur = TT_traducteur();
	if (!$traducteur){
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
	foreach ($todo as $hash => $dummy){
		$paragraphe = $hashes[$hash];
		$trad = $traducteur->traduire($paragraphe, $destLang, $srcLang);
		if ($trad){
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
	if ($inserts){
		sql_insertq_multi("spip_traductions", $inserts);
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

