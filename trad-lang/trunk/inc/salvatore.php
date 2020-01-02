<?php

/*
    This file is part of Salvatore, the translation robot of Trad-lang (SPIP)

    Salvatore is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003-2013
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
 		kent1 <kent1@arscenic.info>
*/


/**
 * initialiser salvatore si besoin
 * peut etre appelle plusieurs fois
 * @throws Exception
 */
function salvatore_init(){
	static $initialized;

	if (is_null($initialized)) {
		@ini_set('memory_limit', '50M');
		if (!defined('_DEBUG_TRAD_LANG')) {
			define('_DEBUG_TRAD_LANG', 1); // undef si on ne veut pas de messages
		}

		if (!defined('_DIR_SALVATORE')) {
			define('_DIR_SALVATORE', _DIR_RACINE . 'salvatore/');
		}

		if (!defined('_DIR_SALVATORE_TRADUCTION')) {
			define('_DIR_SALVATORE_TRADUCTION', _DIR_SALVATORE . 'traductions/');
		}

		if (!defined('_DIR_SALVATORE_TMP')) {
			define('_DIR_SALVATORE_TMP', _DIR_SALVATORE . 'tmp/');
		}

		if (!isset($GLOBALS['idx_lang'])) {
			$GLOBALS['idx_lang'] = 0;
		}

		// verifications des repertoires
		foreach ([_DIR_SALVATORE, _DIR_SALVATORE_TRADUCTION, _DIR_SALVATORE_TMP] as $dir){
			salvatore_check_dir($dir);
		}
		$initialized = true;
	}
}

/**
 * Verifier qu'un repertoire existe
 * @param $dir
 * @throws Exception
 */
function salvatore_check_dir($dir) {
	if (!is_dir($dir)){
		throw new Exception("Erreur : le répertoire $dir n'existe pas");
	}
}

/**
 * Verifier qu'un fichier existe
 * @param $file
 * @throws Exception
 */
function salvatore_check_file($file) {
	if (!file_exists($file)){
		throw new Exception("Erreur : Le fichier $file est introuvable");
	}
}

/**
 * chargement du fichier traductions.txt
 * Construit une liste de modules avec pour chacun un tableau compose de : 0 chemin, 1 nom, 2 langue principale
 *
 * @param string $chemin
 * @param string $trad_list
 * @return array
 * @throws Exception
 */
function salvatore_charger_fichier_traductions($fichier_traductions = null){

	salvatore_init();
	if (is_null($fichier_traductions)) {
		$fichier_traductions = _DIR_SALVATORE_TRADUCTION . 'traductions.txt';
	}
	salvatore_check_file($fichier_traductions);

	$contenu = file_get_contents($fichier_traductions);
	$contenu = preg_replace('/#.*/', '', $contenu); // supprimer les commentaires

	$tab = preg_split("/\r\n|\n\r|\n|\r/", $contenu);

	$liste_trad = array();
	foreach ($tab as $ligne){
		$liste = explode(';', trim($ligne));
		if (!empty($liste[0])){
			if (!isset($liste[1]) or empty($liste[1])){
				$liste[1] = preg_replace('#.*/(.*)$#', '$1', $liste[0]);
			}
			if (!isset($liste[2]) or empty($liste[2])){
				$liste[2] = 'fr';
			}
			if (!count($GLOBALS['modules']) or in_array($liste[1], $GLOBALS['modules'])){
				$liste_trad[] = $liste;
			}
		}
	}
	reset($liste_trad);
	return $liste_trad;
}


/**
 * Loger
 * @param string $msg
 */
function salvatore_log($msg = ''){
	static $cnt;
	if (defined('_DEBUG_TRAD_LANG')){
		echo $msg;
		$cnt++;
	}
	if ($cnt>10){
		$cnt = 0;
		flush();
	}
}

/**
 * Echec sur erreur : on envoie un mail si possible et on echoue en lançant une exception
 * @param $sujet
 * @param $corps
 * @throws Exception
 */
function salvatore_fail($sujet, $corps) {
	$sujet = 'Tireur : Erreur';
	$corps = "! Erreur pas de fichier de langue conforme dans le module : $tmp" . $source[1] . "\n";
	salvatore_envoyer_mail($sujet, $corps);
	throw new Exception($corps);
}

/**
 * @param string $sujet
 * @param string $corps
 */
function salvatore_envoyer_mail($sujet = 'Erreur', $corps = ''){
	if (defined('_EMAIL_ERREURS') and defined('_EMAIL_SALVATORE')){
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		$destinataire = _EMAIL_ERREURS;
		$from = _EMAIL_SALVATORE;
		$envoyer_mail($destinataire, $sujet, $corps, $from);
		salvatore_log("Un email a été envoyé à l'adresse : " . _EMAIL_ERREURS . "\n");
	}
}
