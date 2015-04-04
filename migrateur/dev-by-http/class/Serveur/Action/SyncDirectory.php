<?php

namespace SPIP\Migrateur\Serveur\Action;


class SyncDirectory extends ActionBase {


	public function run($data = null) {

		if (empty($data['directory'])) {
			return "Pas de répertoire indiqué";
		}

		$directory = $data['directory'];
		$this->log_run("Sync Directory : <em>$directory</em>");

		// liste des fichiers déjà présents sur le site de destination
		$remoteFiles = array();
		if (isset($data['files']) and is_array($data['files'])) {
			$remoteFiles = $data['files'];
		}

		$n = count($remoteFiles);
		$this->log("$n fichier(s) sur le site destination");

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

		// calcul des différences !
		spip_timer('diff');
		$newFiles = $updatedFiles = $deletedFiles = array();
		$totalSize = 0;

		//compare local and remote file list to get updated files
		foreach ($localFiles as $filePath => $info) {
			if (empty($remoteFiles[$filePath])) {
				$newFiles[$filePath] = $info;
				$totalSize += $info[0];
			} elseif ($remoteFiles[$filePath] != $info) {
				$updatedFiles[$filePath] = $info;
				$totalSize += $info[0];
			}
			unset($remoteFiles[$filePath]);
		}
		// logiquement, ce qui reste, c'est les fichiers supprimés
		$deletedFiles = $remoteFiles;

		$t = spip_timer('diff');
		$this->log("- " . count($newFiles) . " nouveaux fichiers");
		$this->log("- " . count($updatedFiles) . " à mettre à jour");
		$this->log("- " . count($deletedFiles) . " à supprimer");

		include_spip('inc/filtres');
		$this->log("Estimation des transferts : " . taille_en_octets($totalSize));

		return array(
			'directory' => $directory,
			'files' => array(
				'new' => $newFiles,
				'updated' => $updatedFiles,
				'deleted' => $deletedFiles,
			),
		);
	}

}
