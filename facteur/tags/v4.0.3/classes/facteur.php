<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Classes\Facteur
 *
 * @deprecated voir inc/Facteur/
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/facteur');
include_spip('inc/Facteur/FacteurSMTP');

class Facteur extends SPIP\Facteur\FacteurSMTP {

	/**
	 * @param $email
	 * @param $objet
	 * @param $message_html
	 * @param $message_texte
	 * @param array $options
	 *
	 * @throws Exception
	 */
	public function __construct($email, $objet, $message_html, $message_texte, $options = array()) {

		// config eventuellement surchargeee lors de l'appel
		$config = facteur_config($options);

		// compat ancienne option smtp
		if (isset($options['smtp']) and empty($config['mailer'])) {
			$config['mailer'] = ($options['smtp'] === 'oui' ? 'smtp' : 'mail');
		}

		// toute autre config que smtp se degrade en mail()
		if ($config['mailer'] !== 'smtp') {
			unset($config['smtp_host']);
			unset($config['smtp_port']);
		}
		parent::__construct($config);

		$this->setObjet($objet);
		$this->setDest($email);
		$this->setMessage($message_html, $message_texte);
	}

	/**
	 * Transforme du HTML en texte brut, mais proprement
	 * utilise le filtre facteur_mail_html2text
	 * @uses facteur_mail_html2text()
	 *
	 * @param string $html Le HTML à transformer
	 * @param bool $advanced Inutilisé
	 * @return string Retourne un texte brut formaté correctement
	 * @deprecated
	 */
	public function html2text($html, $advanced = false){
		return facteur_mail_html2text($html);
	}

	/**
	 * Compat ascendante, obsolete
	 * @deprecated
	 */
	public function ConvertirStylesEnligne() {
		$this->Body = facteur_convertir_styles_inline($this->Body);
	}

	/**
	 * Transformer les urls des liens et des images en url absolues
	 * sans toucher aux images embarquees de la forme "cid:..."
	 */
	protected function UrlsAbsolues($base=null){
		return parent::urlsToAbsUrls($base);
	}

	/**
	 * Embed les images HTML dans l'email
	 */
	protected function JoindreImagesHTML() {
		return parent::embedReferencedImages();
	}


	/**
	 * Conversion safe d'un texte utf en isotruc
	 * @param string $text
	 * @param string $mode
	 * @return string
	 */
	protected function safe_utf8_decode($text,$mode='texte_brut') {
		return parent::safeUtf8Decode($text, $mode);
	}

	/**
	 * Convertir tout le mail utf en isotruc
	 */
	protected function ConvertirUtf8VersIso8859() {
		return parent::convertMessageFromUtf8ToIso8859();
	}

	/**
	 * Convertir les accents du body en entites html
	 * @deprecated
	 */
	protected function ConvertirAccents() {
		// tableau à compléter au fur et à mesure
		$cor = array(
						'à' => '&agrave;',
						'â' => '&acirc;',
						'ä' => '&auml;',
						'ç' => '&ccedil;',
						'é' => '&eacute;',
						'è' => '&egrave;',
						'ê' => '&ecirc;',
						'ë' => '&euml;',
						'î' => '&icirc;',
						'ï' => '&iuml;',
						'ò' => '&ograve;',
						'ô' => '&ocirc;',
						'ö' => '&ouml;',
						'ù' => '&ugrave;',
						'û' => '&ucirc;',
						'œ' => '&oelig;',
						'€' => '&euro;'
					);

		$this->Body = strtr($this->Body, $cor);
	}
}
