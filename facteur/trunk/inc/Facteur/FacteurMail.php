<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\FacteurMail
 */

namespace SPIP\Facteur;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!defined("_ECRIRE_INC_VERSION")){
	return;
}

include_spip('inc/charsets');
include_spip('inc/texte');
include_spip('inc/filtres');
include_spip('facteur_fonctions');
include_spip('lib/PHPMailer-6/autoload');

class FacteurMail extends PHPMailer {
	/**
	 * From force si From pas dans le bon domaine
	 * @var string
	 */
	public $ForceFrom = '';

	/**
	 * FromName force si From pas dans le bon domaine
	 * @var string
	 */
	public $ForceFromName = '';

	/**
	 * Faut il embarquer dans le mail les images referencees ?
	 * @var bool
	 */
	protected $embedReferencedImages = false;

	/**
	 * Faut il convertir le message en Isotruc (obsolete ?)
	 * @var bool
	 */
	protected $convertMessageToIso8859 = false;

	/**
	 * Les URLs du site
	 * @var array
	 */
	protected $urlsBase = array();

	/**
	 * CC Auto a remettre quand on clear les recipients
	 * @var mixed|null
	 */
	protected $autoCc = null;

	/**
	 * Bcc Auto a remettre quand on clear les recipients
	 * @var mixed|null
	 */
	protected $autoBcc = null;

	/**
	 * @var bool
	 */
	protected $important = false;

	protected $sendFailFunction = null;

	/**
	 * Wrapper de spip_log pour par PHPMailer
	 * @param $message
	 * @param $level
	 */
	public static function logDebug($message, $level){
		spip_log("$level: " . trim($message), "facteur" . _LOG_DEBUG);
	}

	/**
	 * Fonction de log interne aux Facteurs pour prefixer avec la class qui genere le log
	 * @param string|array $message
	 * @param null|int $level
	 */
	protected function log($message, $level = null) {
		$class = get_class($this);
		if (empty($level)) {
			$level = _LOG_INFO;
		}
		spip_log("$class: " . (is_scalar($message) ? $message : json_encode($message, true)), "facteur" . $level);
	}

	/**
	 * Facteur constructor.
	 * @param array $options
	 * @throws Exception
	 */
	public function __construct($options = array()){
		// par defaut on log rien car tres verbeux
		// on utilise facteur_log_debug qui filtre log SPIP en _LOG_DEBUG
		$this->SMTPDebug = 0;
		$this->Debugoutput = __NAMESPACE__ . '\FacteurMail::logDebug';
		// Il est possible d'avoir beaucoup plus de logs avec 2, 3 ou 4, ce qui logs les échanges complets avec le serveur
		// utiliser avec un define('_MAX_LOG',1000); car sinon on est limite a 100 lignes par hit et phpMailer est tres verbeux
		if (defined('_FACTEUR_DEBUG_SMTP')){
			$this->SMTPDebug = _FACTEUR_DEBUG_SMTP;
		}
		$this->exceptions = false;
		if (!empty($options['exceptions'])) {
			$this->exceptions = ($options['exceptions'] ? true : false);
		}

		if (!empty($options['adresse_envoi_email'])){
			$this->From = $options['adresse_envoi_email'];
		}

		// Si plusieurs emails dans le from, pas de Name !
		if (strpos($this->From, ",")===false){
			if (!empty($options['adresse_envoi_nom'])){
				$this->FromName = $options['adresse_envoi_nom'];
			}
		}

		// si forcer_from, on sauvegarde le From et FromName par defaut, qui seront utilises
		// si From n'est pas dans le meme domaine
		// (utiliser le facteur avec un service externe qui necessite la validation des domaines d'envoi)
		if (isset($options['forcer_from']) and ($options['forcer_from'] === 'oui' or $options['forcer_from'] === true)){
			$this->ForceFrom = $this->From;
			$this->ForceFromName = $this->FromName;
		}

		$this->CharSet = "utf-8";
		$this->Mailer = 'mail';

		// Retour des erreurs
		if (!empty($options['smtp_sender'])){
			$this->Sender = $options['smtp_sender'];
			$this->AddCustomHeader("Errors-To: " . $this->Sender);
		}

		// Destinataires en copie, seulement s'il n'y a pas de destinataire de test
		if (!defined('_TEST_EMAIL_DEST')){
			if (!empty($options['cc'])){
				$this->autoCc = $options['cc'];
				$this->AddCC($this->autoCc);
			}
			if (!empty($options['bcc'])){
				$this->autoBcc = $options['bcc'];
				$this->AddBCC($this->autoBcc);
			}
		}

		if (!empty($options['filtre_images']) and $options['filtre_images']) {
			$this->embedReferencedImages = true;
		}

		if (!empty($options['filtre_iso_8859']) and $options['filtre_iso_8859']) {
			$this->convertMessageToIso8859 = true;
		}

		if (!empty($options['adresses_site'])) {
			$this->urlsBase = $options['adresses_site'];
		}

	}

	/**
	 * Auto-configuration du mailer si besoin
	 * (rien a faire ici dans le cas par defaut)
	 * @return bool
	 */
	public function configure(){
		return true;
	}

	/**
	 * Definir l'objet du mail
	 * @param $objet
	 * @param $charset
	 */
	public function setObjet($objet, $charset = null){
		if (is_null($charset)) {
			$charset = $GLOBALS['meta']['charset'];
		}
		$this->Subject = unicode_to_utf_8(charset2unicode($objet, $charset));
	}

	/**
	 * Definir le ou les Destinataire(s) du mail
	 * clear tous les destinataires precedemment definis
	 *
	 * @param string | array $email
	 * @throws Exception
	 */
	public function setDest($email) {
		$this->clearAllRecipients();

		//Pour un envoi multiple de mail, $email doit être un tableau avec les adresses.
		if (is_array($email)){
			foreach ($email as $cle => $adresseMail){
				if (!$this->AddAddress($adresseMail)){
					$this->log("Erreur AddAddress $adresseMail : " . print_r($this->ErrorInfo, true), _LOG_ERREUR);
				}
			}
		} elseif (!$this->AddAddress($email)) {
			$this->log("Erreur AddAddress $email : " . print_r($this->ErrorInfo, true), _LOG_ERREUR);
		}
	}

	/**
	 * Definir le message, en HTML et/ou en texte (si seulement un message texte fourni
	 * @param string|null $message_html
	 * @param string $message_texte
	 * @param string $charset
	 * @throws Exception
	 */
	public function setMessage($message_html, $message_texte = null, $charset = null) {
		if (is_null($charset)) {
			$charset = $GLOBALS['meta']['charset'];
		}

		// S'il y a un contenu HTML
		if (!empty($message_html)){
			$message_html = unicode_to_utf_8(charset2unicode($message_html, $charset));

			$this->Body = $message_html;
			$this->IsHTML(true);
			if ($this->embedReferencedImages) {
				$this->embedReferencedImages();
			}

			$this->urlsToAbsUrls();
		}

		// S'il y a un contenu texte brut
		if (!empty($message_texte)){
			$message_texte = unicode_to_utf_8(charset2unicode($message_texte, $charset));

			// Si pas de HTML on le remplace en tant que contenu principal
			if (!$this->Body){
				$this->IsHTML(false);
				$this->Body = $message_texte;
			} // Sinon on met le texte brut en contenu alternatif
			else {
				$this->AltBody = $message_texte;
			}
		}

		if ($this->convertMessageToIso8859){
			$this->convertMessageFromUtf8ToIso8859();
		}
	}

	/**
	 * Set the important flag more or less supported by client mails
	 */
	public function setImportant($important = true) {
		if ($important) {
			$this->addCustomHeader("X-Priority", "1 (High)");
			$this->addCustomHeader("X-MSMail-Priority", "High");
			$this->addCustomHeader("Importance", "High");
		}
		$this->important = $important;
	}

	/**
	 * Set the fail function to call if an important mail was not sent
	 * @param $function
	 * @param $args
	 * @param $include
	 */
	public function setSendFailFunction($function, $args, $include) {
		$this->sendFailFunction = array(
			'function' => $function,
			'args' => $args,
			'include' => $include,
		);
	}

	/**
	 * Generer le log informatif sur le mail qui va etre envoye
	 * @return string
	 */
	public function getMessageLog(){
		$this->forceFromIfNeeded();
		$header = $this->CreateHeader();
		$trace = trim($header) . "\n";

		// completer le header avec les infos essentielles qu'on veut dans les logs
		if (!empty($this->to) and strpos($trace, "To:") === false) {
			$trace .= $this->addrAppend('To', $this->to);
		}
		if (!empty($this->cc) and strpos($trace, "Cc:") === false) {
			$trace .= $this->addrAppend('Cc', $this->cc);
		}
		if (!empty($this->bcc) and strpos($trace, "Bcc:") === false) {
			$trace .= $this->addrAppend('Bcc', $this->bcc);
		}
		if (strpos($trace, 'Subject:') === false) {
			$trace .= "Subject: " . $this->Subject . "\n";
		}

		$message_desc = [];
		if (!empty($this->Body)) {
			$message_desc[] = "Body(".strlen($this->Body)."c)";
		}
		if (!empty($this->AltBody)) {
			$message_desc[] = "AltBody(".strlen($this->AltBody)."c)";
		}
		if (!empty($this->attachment)) {
			$message_desc[] = "Files(".count($this->attachment).")";
		}
		$trace .= "Message: " . implode(' ', $message_desc)."\n";


		return "Sent by " . get_class($this) . "\n" . rtrim($trace);
	}


	/**
	 * @param bool $exceptions
	 */
	public function setExceptions($exceptions){
		$this->exceptions = ($exceptions ? true : false);
	}

	/**
	 * Transformer les urls des liens et des images en url absolues
	 * sans toucher aux images embarquees de la forme "cid:..."
	 *
	 * @param string|null $baseUrl
	 */
	protected function urlsToAbsUrls($baseUrl = null){
		if (preg_match_all(',(<(a|link)[[:space:]]+[^<>]*href=["\']?)([^"\' ><[:space:]]+)([^<>]*>),imsS',
			$this->Body, $liens, PREG_SET_ORDER)){
			foreach ($liens as $lien){
				if (strncmp($lien[3], "cid:", 4)!==0){
					$abs = url_absolue($lien[3], $baseUrl);
					if ($abs<>$lien[3] and !preg_match('/^#/', $lien[3])){
						$this->Body = str_replace($lien[0], $lien[1] . $abs . $lien[4], $this->Body);
					}
				}
			}
		}
		if (preg_match_all(',(<(img|script)[[:space:]]+[^<>]*src=["\']?)([^"\' ><[:space:]]+)([^<>]*>),imsS',
			$this->Body, $liens, PREG_SET_ORDER)){
			foreach ($liens as $lien){
				if (strncmp($lien[3], "cid:", 4)!==0){
					$abs = url_absolue($lien[3], $baseUrl);
					if ($abs<>$lien[3]){
						$this->Body = str_replace($lien[0], $lien[1] . $abs . $lien[4], $this->Body);
					}
				}
			}
		}
	}

	/**
	 * Embed les images HTML dans l'email
	 * @throws Exception
	 */
	protected function embedReferencedImages(){
		$image_types = array(
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'bmp' => 'image/bmp',
			'png' => 'image/png',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			//'swf' => 'application/x-shockwave-flash' // on elever pour des raisons de securite et deprecie
		);

		$src_found = array();
		$images_embeded = array();
		if (preg_match_all(
			'/["\'](([^"\']+)\.(' . implode('|', array_keys($image_types)) . '))([?][^"\']+)?([#][^"\']+)?["\']/Uims',
			$this->Body, $images, PREG_SET_ORDER)){

			$adresse_site = $GLOBALS['meta']['adresse_site'] . '/';
			foreach ($images as $im){
				$im = array_pad($im, 6, null);
				$src_orig = $im[1] . $im[4] . $im[5];

				if (!isset($src_found[$src_orig])){ // deja remplace ? rien a faire (ie la meme image presente plusieurs fois)
					// examiner le src et voir si embedable
					$src = $im[1];
					foreach ($this->urlsBase as $base) {
						if ($src AND strncmp($src, $base, strlen($base))==0){
							$src = _DIR_RACINE . substr($src, strlen($base));
						}
					}

					if ($src
						AND !preg_match(",^([a-z0-9]+:)?//,i", $src)
						AND (
							file_exists($f = $src) // l'image a ete generee depuis le meme cote que l'envoi
							OR (_DIR_RACINE AND file_exists($f = _DIR_RACINE . $src)) // l'image a ete generee dans le public et on est dans le prive
							OR (!_DIR_RACINE AND file_exists($f = _DIR_RESTREINT . $src)) // l'image a ete generee dans le prive et on est dans le public
						)
					){
						if (!isset($images_embeded[$f])){
							$extension = strtolower($im[3]);
							$header_extension = $image_types[$extension];
							$cid = md5($f); // un id unique pour un meme fichier
							$images_embeded[$f] = $cid; // marquer l'image comme traitee, inutile d'y revenir
							$this->AddEmbeddedImage($f, $cid, basename($f), 'base64', $header_extension);
						}

						$this->Body = str_replace($src_orig, "cid:" . $images_embeded[$f], $this->Body);
						$src_found[$src_orig] = $f;
					}
				}
			}
		}
	}


	/**
	 * Conversion safe d'un texte utf en isotruc
	 * @param string $text
	 * @param string $mode
	 * @return string
	 */
	protected function safeUtf8Decode($text, $mode = 'texte_brut'){
		if (!is_utf8($text)){
			return ($text);
		}

		if (function_exists('iconv') && $mode=='texte_brut'){
			$text = str_replace('’', "'", $text);
			$text = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $text);
			return str_replace('&#8217;', "'", $text);
		} else {
			if ($mode=='texte_brut'){
				$text = str_replace('’', "'", $text);
			}
			$text = unicode2charset(utf_8_to_unicode($text), 'iso-8859-1');
			return str_replace('&#8217;', "'", $text);
		}
	}

	/**
	 * Convertir tout le mail utf en isotruc
	 */
	protected function convertMessageFromUtf8ToIso8859(){
		$this->CharSet = 'iso-8859-1';
		$this->Body = str_ireplace('charset=utf-8', 'charset=iso-8859-1', $this->Body);
		$this->Body = $this->safeUtf8Decode($this->Body, 'html');
		$this->AltBody = $this->safeUtf8Decode($this->AltBody);
		$this->Subject = $this->safeUtf8Decode($this->Subject);
		$this->FromName = $this->safeUtf8Decode($this->FromName);
	}

	/**
	 * Forcer le from avant envoi si il n'est pas sur le bon domaine
	 * @throws Exception
	 */
	protected function forceFromIfNeeded() {
		if ($this->ForceFrom
			AND $this->From!==$this->ForceFrom){

			$forcedomain = explode('@', $this->ForceFrom);
			$forcedomain = end($forcedomain);
			$domain = explode('@', $this->From);
			$domain = end($domain);

			if ($domain!==$forcedomain){
				// le From passe en ReplyTo
				$this->AddReplyTo($this->From, $this->FromName);
				// on force le From
				$this->From = $this->ForceFrom;
				$this->FromName = $this->ForceFromName;
			}
		}
	}

	/**
	 * Clear all recipients
	 */
	public function clearAllRecipients(){
		parent::clearAllRecipients();
		if (!empty($this->autoCc)) {
			$this->AddCC($this->autoCc);
		}
		if (!empty($this->autoBcc)){
			$this->AddBCC($this->autoBcc);
		}
	}


	/**
	 * Verifier si il faut envoyer le mail d'alerte
	 * @param mixed $res
	 * @return mixed
	 */
	protected function sendAlertIfNeeded($res) {
		if ($res === false) {
			if ($this->important and !empty($this->sendFailFunction)) {
				$facteur_envoyer_alerte_fail = charger_fonction('facteur_envoyer_alerte_fail','inc');
				$facteur_envoyer_alerte_fail($this->sendFailFunction['function'], $this->sendFailFunction['args'], $this->sendFailFunction['include']);
			}
		}
		return $res;
	}

	/**
	 * Une fonction wrapper pour appeler une methode de phpMailer
	 * en recuperant l'erreur eventuelle, en la loguant via SPIP et en lancant une exception si demandee
	 * @param string $function
	 * @param array $args
	 * @return bool
	 * @throws phpmailerException
	 */
	protected function callWrapper($function, $args){
		$exceptions = $this->exceptions;
		$this->exceptions = true;
		try {
			$retour = call_user_func_array($function, $args);
			$this->exceptions = $exceptions;
		} catch (Exception $exc) {
			$this->log((is_array($function) ? implode('::', $function) : $function) . "() : " . $exc->getMessage(), _LOG_ERREUR);
			$this->exceptions = $exceptions;
			if ($this->exceptions){
				throw $exc;
			}
			return false;
		}
		if ($this->ErrorInfo){
			$this->log((is_array($function) ? implode('::', $function) : $function) . "() : " . $this->ErrorInfo, _LOG_ERREUR);
		}

		return $retour;
	}

	/*
	 * Appel des fonctions parents via le callWrapper qui se charge de loger les erreurs
	 */

	/**
	 * Avant le Send() on force le From si besoin
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function Send(){
		$this->forceFromIfNeeded();
		$args = func_get_args();
		$res = $this->callWrapper(array('parent', 'Send'), $args);
		return $this->sendAlertIfNeeded($res);
	}

	public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment'){
		$args = func_get_args();
		return $this->callWrapper(array('parent', 'AddAttachment'), $args);
	}

	public function AddReplyTo($address, $name = ''){
		$args = func_get_args();
		return $this->callWrapper(array('parent', 'AddReplyTo'), $args);
	}

	public function AddBCC($address, $name = ''){
		$args = func_get_args();
		return $this->callWrapper(array('parent', 'AddBCC'), $args);
	}

	public function AddCC($address, $name = ''){
		$args = func_get_args();
		return $this->callWrapper(array('parent', 'AddCC'), $args);
	}
}
