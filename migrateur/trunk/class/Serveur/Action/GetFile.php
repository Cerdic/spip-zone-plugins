<?php

namespace SPIP\Migrateur\Serveur\Action;


class GetFile extends ActionBase {

	public function run($data = null) {

		$file = $data;
		if (!$file OR !is_string($file) OR false !== strpos($file, '..')) {
			return "Format du fichier erroné";
		}

		$this->log_run("Get File <code>$file</code>");

		$basedir = $this->source->dir;

		if (!is_readable($chemin = $basedir . DIRECTORY_SEPARATOR . $file)) {
			return "Fichier non lisible";
		}

		if (!in_array('crypteur.encrypt', stream_get_filters())) {
			if (!stream_filter_register('crypteur.encrypt', '\SPIP\Migrateur\Crypteur\EncryptFilter')) {
				return "Filtre de cryptage introuvable";
			}
		}

		//output file with generic binary mime type
		header('Content-type: application/octet-stream');
		$fp = fopen($chemin, 'rb');
		stream_filter_append($fp, 'crypteur.encrypt', STREAM_FILTER_READ, array('crypteur' => $this->serveur->getCrypteur()));
		fpassthru($fp);

		exit;

	}
}
