<?php

namespace SPIP\Migrateur\Serveur\Action;


class HashFiles extends ActionBase {


	public function run($data = null) {

		if (empty($data['directory'])) {
			return "Pas de répertoire indiqué";
		}

		if (empty($data['files'])) {
			return "Pas de fichiers indiqués";
		}

		$directory = $data['directory'];
		$this->log_run("Hash Files : <em>$directory</em>");

		// liste des fichiers dont le hash est demandé
		$files = array();
		if (isset($data['files']) and is_array($data['files'])) {
			$files = $data['files'];
		}

		$n = count($files);
		$this->log("$n fichier(s) à calculer");

		if (!$n) {
			return "Aucun fichier indiqué";
		}

		// calculer les hash des fichiers, afin de controler le bon déroulement des futurs téléchargements
		$path = rtrim($directory, '/') . DIRECTORY_SEPARATOR;
		$path = $this->source->dir . DIRECTORY_SEPARATOR . $path;

		spip_timer('hash');
		foreach ($files as $filePath => $info) {
			$info[2] = hash_file('sha256', $path . $filePath);
			$files[$filePath] = $info;
		}
		$t = spip_timer('hash');
		$this->log("Calcul des hash en $t");

		return array(
			'directory' => $directory,
			'files' => $files,
		);
	}

}
