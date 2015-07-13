<?php

namespace SPIP\Migrateur\Serveur\Action;

use Ifsnop\Mysqldump\Mysqldump;


class DumpDatabase extends ActionBase {

	/** Doit-on gziper le dump si la commande est disponible sur le serveur ? */
	private $gzip_si_possible = true;

	/** Doit-on utiliser mysqldump si disponible sur le serveur ? */
	private $mysqldump_si_possible = true;

	/** Nom du fichier de sauvegarde (sans extension) */
	private $nom_sauvegarde = 'migrateur';

	/** Tables à exporter (toutes si vide) */
	private $tables = array();


	public function run($data = null) {
		$this->log_run("Get Database");

		if (isset($data['gzip_si_possible'])) {
			$this->gzip_si_possible = (bool)$data['gzip_si_possible']; 
		}

		if (isset($data['mysqldump_si_possible'])) {
			$this->mysqldump_si_possible = (bool)$data['mysqldump_si_possible']; 
		}

		if (isset($data['nom_sauvegarde'])) {
			$this->nom_sauvegarde = $data['nom_sauvegarde']; 
		}

		if (isset($data['tables'])) {
			$this->tables = $data['tables']; 
		}

		spip_timer('dump database');

		if (!$chemin = $this->makeDump()) {
			return "Echec de création du dump.";
		}

		$t = spip_timer('dump database');

		include_spip('inc/filtres');
		$taille = filesize($chemin);
		$to = taille_en_octets($taille);
		$file = substr($chemin, strlen($this->source->dir . DIRECTORY_SEPARATOR));

		$this->log("Dump dans <em>$file</em>");
		$this->log("  | $to en $t");

		return array(
			'fichier' => substr($chemin, strlen(rtrim(realpath(_DIR_RACINE), '/') . '/')),
			'taille'  => $taille,
			'taille_octets' => taille_en_octets($taille),
			'duree' => $t,
			'hash'  => hash_file('sha256', $chemin)
		);
	}

	/**
	 * Retourne le chemin absolu du fichier de sauvegarde
	 *
	 * Crée le sous répertoire tmp/dump au passage
	 * 
	 * @return string
	**/
	private function getBackupPath() {
		$file = 'tmp/dump/' . $this->nom_sauvegarde . '.sql';
		sous_repertoire(_DIR_TMP . 'dump');
		return $this->fichier = $this->source->dir . DIRECTORY_SEPARATOR . $file;
	}


	private function makeDump() {

		$sauvegarde = $this->getBackupPath();

		@unlink($sauvegarde);
		@unlink("$sauvegarde.gz");

		// mysqldump de préférences
		$cmd = '';
		if ($this->mysqldump_si_possible) {
			$cmd = $this->source->commande('mysqldump');
		}

		if ($cmd) {
		#if (false) {
			return $this->makeMysqlDump($sauvegarde);
		} else {
			return $this->makePhpDump($sauvegarde);
		}
	}

	/**
	 * Fait une sauvegarde avec mysqldump
	 *
	 * @param string $sauvegarde Chemin du fichier sql de sauvegarde
	 * @return bool
	**/
	private function makeMysqlDump($sauvegarde) {

		$cmd = $this->source->commande('mysqldump');
		if (!$cmd) {
			$this->log("mysqldump introuvable");
			return false;
		}

		$this->log("Exécution de mysqldump…");

		if ($this->tables) {
			$this->log("Sur tables : " . implode(", ", $this->tables));
		}

		$source = $this->source;

		if ($source->sql->login_path) {
			$this->log("Connexion avec login-path : {$source->sql->login_path}");
			$identifiants = "--login-path={$source->sql->login_path}";
		} elseif ($source->sql->user and $source->sql->pass) {
			$this->log("Connexion avec user (et mot de passe) : {$source->sql->user}");
			$identifiants = "-u {$source->sql->user} --password={$source->sql->pass}";
		} else {
			$this->log("/!\ Erreur de connexion : aucun 'login-path' ou couple 'user/password' trouvés pour se connecter à la base de données.");
			return;
		}

		$output = "";

		$gzip = false;
		if ($this->gzip_si_possible) {
			$gzip = $source->obtenir_commande_serveur('gzip');
		}

		if ($gzip) {
			migrateur_log("Compression gz");
			$compression = "| $gzip";
			$_sauvegarde = "$sauvegarde.gz";
		} else {
			migrateur_log("Sans compression");
			$compression = "";
			$_sauvegarde = "$sauvegarde";
		}

		$tables = implode(" ", $this->tables);
		exec($commande = "$cmd $identifiants {$source->sql->bdd} $tables $compression > $_sauvegarde 2>&1", $output, $err);
		# $this->log($commande);

		if ($err) {
			$this->log("! Erreurs survenues : $err");
			if ($output) $this->log( implode("\n", $output) );
			return false;
		} 

		return $_sauvegarde;
	}

	/**
	 * Fait une sauvegarde mysql avec php
	 *
	 * @param string $sauvegarde Chemin du fichier sql de sauvegarde
	 * @return bool
	**/
	private function makePhpDump($sauvegarde) {
		$this->log('makephpdump');
		if ($this->tables) {
			$this->log("Sur tables : " . implode(", ", $this->tables));
		}

		$gz = function_exists("gzopen");

		include_spip('lib/mysqldump-php/src/Ifsnop/Mysqldump/Mysqldump');

		$dump = new Mysqldump(
			$this->source->sql->bdd,
			$this->source->sql->user,
			$this->source->sql->pass,
			$this->source->sql->serveur,
			$this->source->sql->req,
			array(
				'compress' => $gz ? Mysqldump::GZIP : Mysqldump::NONE,
				'include-tables' => $this->tables,
			)
		);

		if ($gz) {
			$sauvegarde .= '.gz';
		}

		$dump->start($sauvegarde);

		return $sauvegarde;
	}
}
