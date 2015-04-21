<?php

namespace SPIP\Migrateur\Serveur\Action;


class ListFiles extends ActionBase {


	public function run($data = null) {

		if (empty($data['directory'])) {
			return "Pas de répertoire indiqué";
		}

		$directory = $data['directory'];
		$this->log_run("List Directory : <em>$directory</em>");

		// calcul de la liste des fichiers locaux
		spip_timer('list');
		$localFiles = $this->source->getFileList($directory);
		$t = spip_timer('list');
		$n = count($localFiles);
		if ($n > 1) {
			$this->log("$n fichiers locaux. ($t)");
		} else {
			$this->log("$n fichier local. ($t)");
		}

		return array(
			'directory' => $directory,
			'files' => $localFiles,
		);
	}

}
