<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\FacteurSMTP
 */

namespace SPIP\Facteur;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!defined("_ECRIRE_INC_VERSION")){
	return;
}

include_spip('inc/Facteur/FacteurMail');

class FacteurSMTP extends FacteurMail {

	/**
	 * Facteur constructor.
	 * @param array $options
	 * @throws Exception
	 */
	public function __construct($options = array()){
		parent::__construct($options);

		// il faut quand meme avoir un host et un port, sinon on reste sur le mailer par defaut
		if (!empty($options['smtp_host']) and !empty($options['smtp_port'])){
			$this->Mailer = 'smtp';
			$this->Host = $options['smtp_host'];
			$this->Port = $options['smtp_port'];

			// SMTP authentifié ?
			$this->SMTPAuth = false;
			if (isset($options['smtp_auth'])
				and ($options['smtp_auth']==='oui' or $options['smtp_auth']===true)
				and !empty($options['smtp_username'])
				and !empty($options['smtp_password'])){
				$this->SMTPAuth = true;
				$this->Username = $options['smtp_username'];
				$this->Password = $options['smtp_password'];
			}

			if (!empty($options['smtp_secure'])
				and in_array($options['smtp_secure'], ['ssl', 'tls'])){
				$this->SMTPSecure = $options['smtp_secure'];
			}

			if ($options['smtp_tls_allow_self_signed']=='oui'){
				$this->SMTPOptions = array(
					'ssl' => array('allow_self_signed' => true)
				);
			}

			// Pour le moment on remet l'ancien fonctionnement :
			// on ne doit pas tester les certificats si pas demandé explicitement avec l'option TLS !
			$this->SMTPAutoTLS = false;
		}

	}

}
