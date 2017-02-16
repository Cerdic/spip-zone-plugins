<?php

namespace SPIP\Migrateur\Crypteur;

use SPIP\Migrateur\Client\Log;

class DecryptFilter extends \php_user_filter
{
	/** données en stock */
	protected $buffer;

	/** bytes en stock */
	protected $bufferlen;

	/** Longueur de la chaine encodéee */
	const LEN = 8256;

	function onCreate() {
		$this->buffer = '';
		$this->bufferlen = 0;
	}

	function filter($in, $out, &$consumed, $closing) {

		// C'était le dernier morceau, et il nous reste du contenu ?
		if ($closing and $this->bufferlen) {
			$bucket = stream_bucket_new($this->stream, '');
			$bucket->data = $this->params['crypteur']->decrypt_binary($this->buffer);
			stream_bucket_append($out, $bucket);
			return PSFS_PASS_ON;
		}

		// On essaie de récupérer une longueur suffisante de message
		// pour avoir au moins la taille d'un block chiffré complet
		while ($bucket = stream_bucket_make_writeable($in)) {
			$this->buffer .= $bucket->data;
			$this->bufferlen += $bucket->datalen;
			$consumed += $bucket->datalen;

			// On a obtenu une longueur suffisante donc ?
			if ($this->bufferlen >= self::LEN) {
				$message = substr($this->buffer, 0, self::LEN);
				$this->buffer = substr($this->buffer, self::LEN);
				$this->bufferlen -= self::LEN;
				$bucket->data = $this->params['crypteur']->decrypt_binary($message);
				stream_bucket_append($out, $bucket);
				return PSFS_PASS_ON;
			}
		}

		return PSFS_FEED_ME;
	}
}
