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
 * @param string|array $log_function
 * @throws Exception
 */
function salvatore_init($log_function = null){
	static $initialized;

	// set log function if any
	if ($log_function){
		salvatore_log('', $log_function);
	}

	if (is_null($initialized)){
		@ini_set('memory_limit', '50M');
		if (!defined('_DEBUG_TRAD_LANG')){
			define('_DEBUG_TRAD_LANG', 1); // undef si on ne veut pas de messages
		}

		if (!defined('_DIR_SALVATORE')){
			define('_DIR_SALVATORE', _DIR_RACINE . 'salvatore/');
		}

		if (!defined('_DIR_SALVATORE_TRADUCTIONS')){
			define('_DIR_SALVATORE_TRADUCTIONS', _DIR_SALVATORE . 'traductions/');
		}

		if (!defined('_DIR_SALVATORE_TMP')){
			define('_DIR_SALVATORE_TMP', _DIR_SALVATORE . 'tmp/');
		}

		if (!defined('_DIR_SALVATORE_MODULES')){
			define('_DIR_SALVATORE_MODULES', _DIR_SALVATORE . 'modules/');
		}

		if (!defined('_DIR_SALVATORE_DEPOTS')){
			define('_DIR_SALVATORE_DEPOTS', _DIR_SALVATORE . 'depots/');
		}

		if (!isset($GLOBALS['idx_lang'])){
			$GLOBALS['idx_lang'] = 0;
		}

		// verifications des repertoires
		foreach ([_DIR_SALVATORE, _DIR_SALVATORE_TRADUCTIONS, _DIR_SALVATORE_MODULES, _DIR_SALVATORE_DEPOTS, _DIR_SALVATORE_TMP] as $dir){
			salvatore_check_dir($dir);
		}
		$initialized = true;
	}
}


/**
 * chargement du fichier traductions.txt
 * Construit une liste de modules avec pour chacun un tableau associatif
 *
 * @param string $fichier_traductions
 * @return array
 * @throws Exception
 */
function salvatore_charger_fichier_traductions($fichier_traductions = null){

	salvatore_init();
	if (is_null($fichier_traductions)){
		$fichier_traductions = _DIR_SALVATORE_TRADUCTIONS . 'traductions.txt';
	}
	salvatore_check_file($fichier_traductions);

	$lignes = file($fichier_traductions);
	$lignes = array_map('trim', $lignes);
	$lignes = array_filter($lignes);

	$liste_trad = array();
	foreach ($lignes as $ligne){
		if ($ligne[0]!=='#'){
			$liste = explode(';', trim($ligne));
			$methode = $url = $branche = $dir = $module = $lang = '';

			// deprecated ancien format, forcement en svn
			// liste courte de type
			// url;module;lang
			if (count($liste)<=3){
				$methode = 'svn';
				$branche = '';
				$url = $liste[0];
				if (empty($liste[1])){
					$module = preg_replace('#.*/(.*)$#', '$1', $url);
				} else {
					$module = $liste[1];
				}
				if (empty($liste[2])){
					$lang = 'fr';
				} else {
					$lang = $liste[2];
				}
			}
			// format complet et explicite de 6 valeurs
			// seule les valeurs pour branche et dir peuvent etre vide (branche master par defaut en git)
			// svn;url;;;module;lang
			// git;url;master;subdir/tolang;module;lang
			else {
				list($methode, $url, $branche, $dir, $module, $lang) = $liste;
			}
			$methode = trim($methode);
			$url = trim($url);
			$url = rtrim($url, '/'); // homogeneiser
			$dir = trim($dir);
			$dir = trim($dir, '/'); // homogeneiser
			$branche = trim($branche);
			$module = trim($module);
			$lang = trim($lang);

			if ($methode
				and $url
				and $module
				and $lang){
				// que fait la $GLOBALS['modules'] ?
				if (empty($GLOBALS['modules']) or in_array($module, $GLOBALS['modules'])){
					// definir un dir checkout unique meme si plusieurs modules de meme nom dans differents repos
					$d = explode('/', $url);
					while (count($d) and in_array(end($d), ['', 'lang', 'trunk', 'ecrire'])){
						array_pop($d);
					}
					$source = '';
					if (end($d)){
						$source = basename(end($d), '.git');
						$source = '--' . preg_replace(',[^\w-],', '_', $source);
					}
					$dir_module = "{$module}{$source}-" . substr(md5("$methode:$url:$branche"), 0, 5);
					$dir_checkout = preg_replace(",\W+,", "-", "$methode-$url") . ($branche ? "--$branche-" : "-") . substr(md5("$methode:$url:$branche"), 0, 5);

					$liste_trad[] = [
						'methode' => $methode,
						'url' => $url,
						'branche' => $branche,
						'dir' => $dir,
						'module' => $module,
						'lang' => $lang,
						'dir_module' => $dir_module,
						'dir_checkout' => $dir_checkout,
					];
				}
			} else {
				salvatore_log("Fichier $fichier_traductions, IGNORE ligne incomplete : $ligne");
			}
		}
	}
	return $liste_trad;
}

/**
 * Ajouter les credentials user/pass sur les urls de repo
 * @param string $methode
 * @param string $url_repository
 * @param string $module
 * @return string
 */
function salvatore_set_credentials($methode, $url_repository, $module){
	global $domaines_exceptions, $domaines_exceptions_credentials,
	       $SVNUSER, $SVNPASSWD,
	       $GITUSER, $GITPASSWD;

	// on ne sait pas mettre des credentials si c'est du ssh
	if (strpos($url_repository, '://')!==false){
		$user = $pass = false;
		$parts = parse_url($url_repository);
		if (empty($parts['user']) and empty($parts['pass'])){
			$host = $parts['host'];
			require_once(_DIR_ETC . 'salvatore_passwd.inc');

			if (!empty($domaines_exceptions)
				and is_array($domaines_exceptions)
				and in_array($host, $domaines_exceptions)){
				// on est dans une exception

				/**
				 * Est-ce que cette exception dispose de credentials (Github?)
				 */
				if (is_array($domaines_exceptions_credentials)
					and !empty($domaines_exceptions_credentials[$host])){
					$user = $domaines_exceptions_credentials[$host]['user'];
					$pass = $domaines_exceptions_credentials[$host]['pass'];
				}

			} else {
				// un truc perso pour un module en particulier ?
				if (isset(${$module . '_user'})){
					$user = ${$module . '_user'};
					$pass = ${$module . '_passwd'};
				} elseif ($methode==='svn' and isset($SVNUSER)) {
					$user = $SVNUSER;
					$pass = $SVNPASSWD;
				} elseif ($methode==='git' and isset($GITUSER)) {
					$user = $GITUSER;
					$pass = $GITPASSWD;
				}
			}

			if ($user and $pass){
				$url_repository = str_replace("://$host", "://" . urlencode($user) . ":" . urlencode($pass) . "@$host", $url_repository);
			}
		}

	}

	return $url_repository;
}


/**
 * Verifier qu'un repertoire existe
 * @param $dir
 * @throws Exception
 */
function salvatore_check_dir($dir){
	if (!is_dir($dir)){
		throw new Exception("Erreur : le répertoire $dir n'existe pas");
	}
}

/**
 * Verifier qu'un fichier existe
 * @param $file
 * @throws Exception
 */
function salvatore_check_file($file){
	if (!file_exists($file)){
		throw new Exception("Erreur : Le fichier $file est introuvable");
	}
}

/**
 * Loger
 * @param string $msg
 * @param string|array $display_function
 */
function salvatore_log($msg = '', $display_function = null){
	static $function = null;

	if ($display_function and is_callable($display_function)){
		$function = $display_function;
	}

	if (defined('_DEBUG_TRAD_LANG')
		and _DEBUG_TRAD_LANG
		and $msg){
		if ($function){
			call_user_func($function, rtrim($msg));
		} else {
			// fallback : utiliser echo mais enlever les balises de formatage symphony
			$msg = str_replace(["<info>", "</info>", "<error>", "</error>", "<comment>", "</comment>", "<question>", "</question>", "</>"], "", $msg);
			echo rtrim($msg) . "\n";
		}
	}
}

/**
 * Echec sur erreur : on envoie un mail si possible et on echoue en lançant une exception
 * @param $sujet
 * @param $corps
 * @throws Exception
 */
function salvatore_fail($sujet, $corps){
	salvatore_envoyer_mail($sujet, $corps);
	throw new Exception($corps);
}

/**
 * @param string $sujet
 * @param string $corps
 */
function salvatore_envoyer_mail($sujet = 'Erreur', $corps = ''){
	if (defined('_EMAIL_ERREURS') and _EMAIL_ERREURS
		and defined('_EMAIL_SALVATORE') and _EMAIL_SALVATORE){
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		$destinataire = _EMAIL_ERREURS;
		$from = _EMAIL_SALVATORE;
		$envoyer_mail($destinataire, $sujet, $corps, $from);
		salvatore_log("Un email a été envoyé à l'adresse : " . _EMAIL_ERREURS . "\n");
	}
}
