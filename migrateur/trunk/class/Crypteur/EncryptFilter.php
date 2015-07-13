<?php

namespace SPIP\Migrateur\Crypteur;

use SPIP\Migrateur\Serveur\Log;

class EncryptFilter extends \php_user_filter
{

	function filter($in, $out, &$consumed, $closing) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			$bucket->data = $this->params['crypteur']->encrypt_binary($bucket->data);
			$consumed += $bucket->datalen;
			stream_bucket_append($out, $bucket);
		}

		return PSFS_PASS_ON;
	}

	// debug !
	function log($message, $level='info') { 
		spip_log($message, 'serveur');
	}
}
