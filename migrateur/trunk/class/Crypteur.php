<?php

namespace SPIP\Migrateur;


class Crypteur {

	private $aes_key;

	public function __construct($aes_key) {
		$this->aes_key = $aes_key;
		include_spip('lib/php-encryption/Crypto');
	}

	/**
	 * Crypte l'action demandée et les données
	 *
	 * @param string $action
	 * @param mixed $data
	 * @return string
	**/
	public function encrypt($action, $data) {
		$message = array(
			'action' => $action,
			'data'   => $data
		);
		$message = serialize($message);
		$message = $this->encrypt_binary($message);
		return bin2hex($message);
	}

	/**
	 * Décrypte le message 
	 *
	 * Si le serveur a bien fait son travail, on devrait retrouver un tableau 
	 * ```
	 * array(
	 *     'action' => string
	 *     'data' => mixed
	 * )
	 * ```
	 *
	 * @param string $message
	 * @return mixed
	**/
	public function decrypt($message) {
		$message = hex2bin($message);
		$message = $this->decrypt_binary($message);
		$message = unserialize($message);
		return $message;
	}



	/**
	 * Crypte les données quelconques
	 *
	 * @param mixed $data
	 * @return string
	**/
	public function encrypt_binary($data) {
		try {
			$message = \Crypto::Encrypt($data, hex2bin($this->aes_key));
		} catch (\CryptoTestFailedException $ex) {
			die('Cannot safely perform encryption');
		} catch (\CannotPerformOperationException $ex) {
			die('Cannot safely perform decryption');
		}
		return $message;
	}

	/**
	 * Décrypte des données quelquonques
	 *
	 * @param string $message
	 * @return mixed
	**/
	public function decrypt_binary($message) {
		try {
			$data = \Crypto::Decrypt($message, hex2bin($this->aes_key));
		} catch (\InvalidCiphertextException $ex) { // VERY IMPORTANT
			// Either:
			//   1. The ciphertext was modified by the attacker,
			//   2. The key is wrong, or
			//   3. $ciphertext is not a valid ciphertext or was corrupted.
			// Assume the worst.
			die('DANGER! DANGER! The ciphertext has been tampered with!');
		} catch (\CryptoTestFailedException $ex) {
			die('Cannot safely perform encryption');
		} catch (\CannotPerformOperationException $ex) {
			die('Cannot safely perform decryption');
		}
		return $data;
	}
}
