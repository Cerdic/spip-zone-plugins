<?php

namespace SPIP\Migrateur\Client\Action;



class GetFile extends ActionBase {

	public function run($data = null) {
		$file = $hash = '';

		if (is_array($data)) {
			$file = $data['fichier'];
			$hash = $data['hash'];
		} else {
			$file = $data;
		}

		if (!$file OR !is_string($file) OR false !== strpos($file, '..')) {
			return "Format du fichier erroné";
		}

		$reponse = $this->client->ask('GetFile', $file, 'file');
		if ($reponse) {
			if ($hash !== $reponse['message']['data']['hash']) {
				$this->log("Hash différents, suppression du fichier par sécurité");
				$chemin = $this->destination->dir . DIRECTORY_SEPARATOR . $file;
				#unlink($chemin);
				return false;
			}
		}
		return $reponse;
	}


}
