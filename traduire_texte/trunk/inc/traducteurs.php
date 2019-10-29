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

	/**
	 * @param $texte
	 * @param string $destLang
	 * @param string $srcLang
	 * @param bool $throw
	 * @return string
	 * @throws Exception
	 */
	public function traduire($texte, $destLang = 'fr', $srcLang = 'en', $throw = false){
		if (strlen(trim($texte))==0){
			return '';
		}
		$len = mb_strlen($texte);
		$extrait = mb_substr($texte, 0, 40);
		spip_log('Trad:' . $this->type . ' ' . $len . 'c. : ' . $extrait . ($len>40 ? '...' : ''), 'translate' . _LOG_DEBUG);
		$erreur = false;
		$res = $this->_traduire($texte, $destLang, $srcLang, $erreur);
		if ($erreur) {
			spip_log($erreur, 'translate' . _LOG_ERREUR);
			if ($throw) {
				throw new \Exception($erreur);
			}
		}

		return $res;
	}

	abstract protected function _traduire($texte, $destLang, $srcLang, &$erreur);
}

/**
 * Traduire avec Bing
 */
class TT_Traducteur_Bing extends TT_Traducteur {
	public $type = 'bing';
	public $maxlen = 10000;

	protected function _traduire($texte, $destLang, $srcLang, &$erreur){
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
			$erreur = $e->getMessage();
			return false;
		}

		return $translation->TranslateResult;
	}
}

/**
 * Traduire avec DeepL
 */
class TT_Traducteur_DeepL extends TT_Traducteur {
	public $type = 'deepl';
	public $maxlen = 29000; // The request size should not exceed 30kbytes
	protected $apiVersion = 2;

	protected function _traduire($texte, $destLang, $srcLang, &$erreur){

		include_spip('lib/deepl-php-lib/autoload');
		try {
			$deepl   = new BabyMarkt\DeepL\DeepL($this->apikey, $this->apiVersion);
			$tagHandling = [];
			if (strpos($texte, "</") !== false and preg_match(",</\w+>,ms", $texte)) {
				$tagHandling = ['xml'];
			}
			$traduction = $deepl->translate($texte, $srcLang, $destLang, $tagHandling);
		} catch (Exception $e) {
			$erreur = $e->getMessage();
			return false;
		}

		return $traduction;
	}
}

/**
 * Traduire avec Google Translate
 */
class TT_Traducteur_GGTranslate extends TT_Traducteur {
	public $type = 'google';
	public $maxlen = 4500;

	protected function _traduire($texte, $destLang = 'fr', $srcLang = 'en', &$erreur){
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
			spip_log($json, 'translate' . _LOG_DEBUG);
			$erreur = _T('traduiretexte:erreur') . " " . $json["error"]['code'] . ": " . $json["error"]['message'];
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

	protected function _traduire($texte, $destLang = 'fr', $srcLang, &$erreur){
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

		if (isset($json['code']) && $json['code']>200){
			spip_log($json, 'translate' . _LOG_DEBUG);
			$erreur = _T('traduiretexte:erreur') . " " . $json['code'] . ": " . $json['message'];
			return false;
		}

		return urldecode($json["text"][0]);
	}
}


class TT_Traducteur_Shell extends TT_Traducteur {
	public $type = 'shell';
	public $maxlen = 1000;

	public function _traduire($texte, $destLang = 'fr', $srcLang = 'en', &$erreur){
		if (!defined('_TRANSLATESHELL_CMD')){
			$erreur = "chemin de Translate shell non défini";
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