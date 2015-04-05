<?php

namespace SPIP\Migrateur\Client\Action;



class SyncDirectory extends ActionBase {


	private $directory = ''; // IMG
	private $path = '';      // chemin/vers/IMG


	public function run($data = null) {

		$this->directory = $data;

		if (!$this->directory) {
			return "Aucun répertoire indiqué.";
		}

		$this->log_run("Sync Répertoire <em>$this->directory</em>");

		// calcul du chemin complet 
		$path = rtrim($this->directory, '/') . DIRECTORY_SEPARATOR;
		$this->path = $this->destination->dir . DIRECTORY_SEPARATOR . $path;
		unset($path);


		spip_timer('list');
		$localFiles = $this->destination->getFileList($this->directory);
		$t = spip_timer('list');
		$n = count($localFiles);
		if ($n > 1) {
			$this->log("$n fichiers locaux avant synchro. ($t)");
		} else {
			$this->log("$n fichier local avant synchro. ($t)");
		}

		$data = array(
			'directory' => $this->directory,
			'files' => $localFiles,
		);

		spip_timer('list');
		$reponse = $this->client->ask('SyncDirectory', $data, 'json');
		$t = spip_timer('list');

		if (empty($reponse['message']['data']['files'])) {
			return $reponse;
		}

		$files = $reponse['message']['data']['files'];
		$this->log("Réception de la liste des fichiers ($t)");
		$this->log("- " . count($files['new']) . " nouveaux fichiers");
		$this->log("- " . count($files['updated']) . " à mettre à jour");
		$this->log("- " . count($files['deleted']) . " à supprimer");
		$this->log("Estimation des transferts : " . $reponse['message']['data']['downloadSize']);

		$this->delete($files['deleted']);
		$this->download($files['new'] + $files['updated']);

		return $reponse;
	}


	/**
	 * Supprime tous les fichiers indiqués
	 *
	 * @paraam array $files Liste des fichiers 
	**/
	private function delete($files) {
		if (count($files)) {
			$this->log_run("Suppression de " . count($files) . " fichier(s)");
			foreach ($files as $filePath => $info) {
				unlink($this->path . DIRECTORY_SEPARATOR . $filePath);
			}
		}
	}

	/**
	 * Télécharge tous les fichiers indiqués
	 *
	 * Comme on vérifie que le fichier reçu est correct, on demande
	 * à calculer les sha256 des fichiers à télécharger.
	 * 
	 * On en demande pour 100Mo ou 100 fichiers car ces calculs peuvent
	 * être un peu long
	 * 
	 * @paraam array $files Liste des fichiers 
	**/
	private function download($files) {
		if (count($files)) {
			$this->log_run("Téléchargement de " . count($files) . " fichier(s)");

			$nb = $size = 0;
			$slice = array();

			foreach ($files as $filePath => $info) {
				$nb++;
				$size += $info[0];
				$slice[$filePath] = $info;
				unset($files[$filePath]);

				if ($nb >= 100 OR $size >= 100*1000*1000) {
					if (!$this->downloadSlice($slice)) {
						return false;
					}
					$size = $nb = 0;
					$slice = array();
				}
			}

			// le reste
			if (count($slice)) {
				return $this->downloadSlice($slice);
			}
		}

		return true;
	}


	/**
	 * Télécharge tous les fichiers indiqués
	 *
	 * Demande la liste des hash des fichiers indiqués,
	 * puis les télécharge un par un et vérifie les hash.
	 * 
	 * @paraam array $files Liste des fichiers
	 * @return bool true si OK
	**/
	private function downloadSlice($files) {
		if (!$files) {
			return false;
		}
		if (!$files = $this->getHash($files)) {
			return false;
		}

		foreach ($files as $filePath => $info) {
			$reponse = $this->client->action('GetFile', array(
				'fichier' => $this->directory . DIRECTORY_SEPARATOR. $filePath,
				'hash' => $info[2],
			));

			if (!$reponse) {
				migrateur_log("Échec de récupération du fichier");
				return false;
			}

			// update modified time to match server
			touch($this->path . DIRECTORY_SEPARATOR. $filePath, $info[1]);
		}

		return true;
	}



	/**
	 * Récupère les hash de tous les fichiers indiqués
	 *
	 * @paraam array $files Liste des fichiers
	 * @return array|false
	 *    - Liste filePath => hash
	 *    - false si erreur.
	**/
	private function getHash($files) {
		$this->log_run("Demande de hash pour " . count($files) . " fichiers");
		$data = array(
			'directory' => $this->directory,
			'files' => $files
		);
		$reponse = $this->client->ask('HashFiles', $data, 'json');

		if (!is_array($reponse)) {
			return false;
		}

		return $reponse['message']['data']['files'];
	}

}
