<?php

class LibreOffice {
	const APP_DEFAULT_PARAMS = '--headless';

	/** @var string */
	protected $fichier;

	/** @var string[] */
	protected $command = array();

	/** @var string[] */
	protected $errors = array();

	/** @var string */
	protected $outputDir = '';

	/** @var string */
	protected $convertTo = 'odt';

	public function __construct($fichier) {
		$this->fichier = $fichier;
		return $this;
	}

	public function setConvertTo($valeur) {
		return $this->addCommandParam('convert-to', $valeur);
	}

	public function setOutputDir($valeur) {
		return $this->addCommandParam('outdir', $valeur);
	}

	public function addCommandParam($name, $valeur = null) {
		switch ($name) {
			case 'convert-to':
				$this->convertTo = $valeur;
				break;
			case 'outdir':
				$valeur = rtrim($valeur, DIRECTORY_SEPARATOR);
				$this->outputDir = $valeur;
				break;
		}
		if (is_null($valeur)) {
			$this->command[] = ' --' . $name;
		} else {
			$this->command[] = ' --' . $name . ' ' . addslashes($valeur);
		}
		return $this;
	}

	public function addCommandArgument($valeur) {
		$this->command[] = $valeur;
		return $this;
	}

	public function execute() {
		$command = $this->createCommand();

		spip_log("Commande exécutée : '$command'", 'odtspip.' . _LOG_DEBUG);
		exec($command, $output, $err);

		// $output[0] :
		// convert [...]tmp/odt2spip/1/simple.docx -> [...]tmp/odt2spip/1/simple.odt using filter : writer8

		if ($err) {
			spip_log($err, 'odtspip.' . _LOG_DEBUG);
			$this->addError('Erreur dans l’exécution de la commande de conversion de document');
		}

		spip_log($output, 'odtspip.' . _LOG_DEBUG);

		return $this;
	}

	/**
	 * Crée la commande à exécuter sur le serveur
	 * @return string
	 */
	public function createCommand() {
		if (defined('_LIBREOFFICE_PATH') and _LIBREOFFICE_PATH) {
			$command = _LIBREOFFICE_PATH;
		} else {
			include_spip('inc/odt2spip');
			$command = odt2spip_obtenir_commande_serveur('libreoffice');
		}
		$params = defined('_LIBREOFFICE_DEFAULT_PARAMS') ? _LIBREOFFICE_DEFAULT_PARAMS : static::APP_DEFAULT_PARAMS;
		if ($params) {
			$command .= ' ' . $params;
		}
		$params = implode(' ', $this->command);
		if ($params) {
			$command .= ' ' . $params;
		}
		$command .= ' ' . str_replace(' ', '\ ', $this->fichier);

		// il doit pouvoir écrire quelque part
		if (defined('_LIBREOFFICE_HOME') and _LIBREOFFICE_HOME) {
			$home = _LIBREOFFICE_HOME;
		} else {
			$home = $this->outputDir ? $this->outputDir : dirname($this->fichier);
		}

		$command = 'export HOME=' . realpath($home) . '; ' . $command;

		return $command;
	}

	/**
	 * Ajoute une erreur
	 * @return string[]
	 */
	public function addError($erreur) {
		$this->errors[] = $erreur;
	}

	/**
	 * Retourne les erreurs
	 * @return string[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Retourne le chemin du fichier créé.
	 * @return bool|string
	 */
	public function getConvertedFile() {
		$file =
			$this->outputDir
			. DIRECTORY_SEPARATOR
			. pathinfo($this->fichier, \PATHINFO_FILENAME)
			. '.' . strtolower($this->convertTo);

		if (file_exists($file)) {
			return $file;
		}
		return false;
	}
}