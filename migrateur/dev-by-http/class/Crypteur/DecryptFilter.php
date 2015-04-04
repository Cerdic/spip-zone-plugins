<?php

namespace SPIP\Migrateur\Crypteur;

use SPIP\Migrateur\Client\Log;

class DecryptFilter extends \php_user_filter
{
	/** données en stock */
	protected $buffer;

	/** bytes en stock */
	protected $bufferlen;

	/** stocke le dernier bucket reçu */
	protected $last_bucket;

	/** Longueur de la chaine encodéee */
	const LEN = 8256;

	function onCreate() {
		$this->buffer = "";
		$this->bufferlen = 0;
	}


	function filter($in, $out, &$consumed, $closing) {

		while ($bucket = stream_bucket_make_writeable($in)) {

			$this->buffer .= $bucket->data;
			$this->bufferlen += $bucket->datalen;
			$consumed += $bucket->datalen;

			if ($this->bufferlen < self::LEN) {
				return PSFS_FEED_ME;
			}

			$message = substr($this->buffer, 0, self::LEN);
			$this->buffer = substr($this->buffer, self::LEN);
			$this->bufferlen -= self::LEN;

			$bucket->data = $this->params['crypteur']->decrypt_binary($message);

			stream_bucket_append($out, $bucket);

			// hack pour closing…
			$this->last_bucket = $bucket;
		}

		if ($closing and $this->bufferlen) {
			$bucket = $this->last_bucket;
			$bucket->data = $this->params['crypteur']->decrypt_binary($this->buffer);
			#migrateur_log("Closing...");
			stream_bucket_append($out, $bucket);
		}

		return PSFS_PASS_ON;

	}
}
