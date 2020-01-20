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

    Copyright 2003-2020
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
        kent1 <kent1@arscenic.info>
        Cerdic <cedric@yterium.com>
*/


// il commit et push les fichiers modifies


/**
 * @param array $liste_sources
 * @param string|null $tmp
 * @return bool
 * @throws Exception
 */
function salvatore_pousser($liste_sources, $dir_modules=null, $dir_depots=null) {
	include_spip('inc/salvatore');
	salvatore_init();

	if (is_null($dir_modules)) {
		$dir_modules = _DIR_SALVATORE_MODULES;
	}
	salvatore_check_dir($dir_modules);

	if (is_null($dir_depots)) {
		$dir_depots = _DIR_SALVATORE_DEPOTS;
	}
	salvatore_check_dir($dir_depots);

	$done = array();

	foreach ($liste_sources as $source){
		salvatore_log("\n<info>--- Module " . $source['module'] . " | " . $source['dir_module'] . " | " . $source['url']."</info>");

		$dir_module = $dir_modules . $source['dir_module'];
		$module = $source['module'];

		// on peut poser un .salvatore.ignore.{module} manuellement pour forcer salvatore a ne jamais pousser certains modules
		// (gestion de tensions sur certains plugins/modules)

		$file_commit = $dir_module . '/' . $module . '.commit.json';

		if (file_exists($dir_module . '/.salvatore.ignore.' . $module)) {
			salvatore_log("<info>Module $module ignoré</info>");
		}
		else {
			if (!file_exists($file_commit)
			  or !$commit_infos = file_get_contents($file_commit)
			  or !$commit_infos = json_decode($commit_infos, true)) {
				salvatore_log("<info>Module $module rien à faire (pas de fichier $file_commit ou fichier invalide)</info>");
			}
			else {
				// on a la liste des fichiers a commit
				$message_commit = '';
				if (isset($commit_infos['.message'])) {
					$message_commit = $commit_infos['.message'];
					unset($commit_infos['.message']);
				}




			}
		}

		/*
		$url_with_credentials = salvatore_set_credentials($source['methode'], $source['url'], $source['module']);

		$dir_checkout = $dir_depots . $source['dir_checkout'];
		$dir_module = $dir_modules . $source['dir_module'];
		$dir_target = $dir_checkout;
		if ($source['dir']) {
			$dir_target .= "/" . $source['dir'];
		}

		$return = 0;
		if (empty($done[$dir_checkout])) {
			$cmd = "checkout.php"
			  . ' ' . $source['methode']
				. ($source['branche'] ? ' -b'.$source['branche'] : '')
				. ' ' . $url_with_credentials
				. ' ' . $dir_checkout;

			echo "$cmd\n";
			passthru("export FORCE_RM_AND_CHECKOUT_AGAIN_BAD_DEST=1 && $cmd 2>/dev/null", $return);
			$done[$dir_checkout] = true;
		}

		if ($return !== 0 or !is_dir($dir_checkout) or !is_dir($dir_target)) {
			$corps = $source['url'] . ' | ' . $source['module'] . "\n" . "Erreur lors du checkout";
			salvatore_fail('[Tireur] : Erreur', $corps);
		}

		if (file_exists($dir_module) and !is_link($dir_module)) {
			$corps = $source['url'] . ' | ' . $source['module'] . "\n" . "Il y a deja un repertoire $dir_module";
			salvatore_fail('[Tireur] : Erreur', $corps);
		}

		$dir_target = realpath($dir_target);
		if (is_link($dir_module) and readlink($dir_module) !== $dir_target) {
			@unlink($dir_module);
		}
		if (!file_exists($dir_module)) {
			symlink($dir_target, $dir_module);
		}

		$fichier_lang_master = $dir_module . '/' . $source['module'] . '_' . $source['lang'] . '.php';
		// controle des erreurs : requiert au moins 1 fichier par module !
		if (!file_exists($fichier_lang_master)){
			salvatore_fail('[Tireur] : Erreur', "! Erreur pas de fichier de langue maitre $fichier_lang_master");
		}
		*/
	}

	return true;
}


$propset = true;
if (isset($NO_PROPSET)){
	$propset = false;
}

$tmp = _DIR_SALVATORE_TMP;

/* MAIN ***********************************************************************/

salvatore_log("\n=======================================\nPOUSSEUR\nPrend les fichiers langue dans sa copie locale et les commite SVN\n=======================================\n");

$liste_sources = salvatore_charger_fichier_traductions(); // chargement du fichier traductions.txt

foreach ($liste_sources as $source){
	$credentials = false;
	$module = $source[1];
	salvatore_log("===== Module $module ======================================\n");

	$domaine_svn = parse_url($source[0]);
	$domaine_svn = $domaine_svn['host'];
	if (isset($domaines_exceptions) and is_array($domaines_exceptions) && in_array($domaine_svn, $domaines_exceptions)){
		/**
		 * On est dans une exception (Github?)
		 */
		if (is_array($domaines_exceptions_credentials) and isset($domaines_exceptions_credentials[$domaine_svn])){
			$user = $domaines_exceptions_credentials[$domaine_svn]['user'];
			$pass = $domaines_exceptions_credentials[$domaine_svn]['pass'];
			$credentials = true;
		}
	}
	if (isset(${$module . '_user'})){
		$user = ${$module . '_user'};
		$pass = ${$module . '_passwd'};
	} elseif (!$credentials) {
		$user = $SVNUSER;
		$pass = $SVNPASSWD;
	}

	$f = _DIR_SALVATORE_TMP . $module . '/';

	/**
	 * On ajoute les .xml
	 */
	salvatore_log(exec("svn add --quiet $f*xml 2>/dev/null") . "\n");
	$ignore = array(
		//	'spip','ecrire','public'
		'couteau', 'couteauprive', 'paquet-couteau_suisse'// désactivés suite à scandale, je ne sais pas comment le gérer correctement
	);

	if (in_array($module, $ignore)){
		salvatore_log("$module ignore'\n");
	} else {
		$depot = exec("env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^Repository Root/ { print $3 }'") . '/';
		$svn_url = exec("env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^URL/ { print $2 }'") . '/';
		$path_svn = str_replace($depot, '', $svn_url);
		$message = $message_commit = $commiteurs = false;
		if (file_exists($f . 'message_commit.inc')){
			$message = true;
			require_once(dirname(__FILE__) . '/' . str_replace('./', '', $f) . 'message_commit.inc');
		}

		if (is_array($commiteurs) && count($commiteurs)>0){
			foreach ($commiteurs as $lang => $email){
				if (strlen($email)>1){
					$message_commit_unique = "[Salvatore] [source:$path_svn $module] Export depuis http://trad.spip.net de la langue $lang";
					/**
					 * Si plusieurs commiteurs (veut dire que plusieurs fichiers sont à commiter)
					 * ou si le fichier original est modifié, on ne commit que fichier par fichier
					 */
					if (count($commiteurs)>1 || in_array(substr(exec('svn status ' . _DIR_SALVATORE_TMP . $source[1] . '/' . $source[1] . '_' . $source[2] . '.php'), 0, 1), array('A', 'M'))){
						$path = $f . $module . '_' . $lang . '.php';
					} else {
						/**
						 * Sinon on ne s'embarasse pas, on balance tout avec cet utilisateur
						 */
						$path = $f;
					}
					salvatore_log("On devrait commiter $path avec comme message '$message_commit_unique' avec l'email $email\n");
					salvatore_log(exec("svn commit $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert -m " . escapeshellarg($message_commit_unique)) . "\n");
					$revision = exec("svn up $path && env LC_MESSAGES=en_US.UTF-8 svn info $path |awk '/^Last Changed Rev/ { print $4 }'");
					if ($propset){
						salvatore_log(exec("svn propset --revprop -r $revision svn:author '$email' $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert") . "\n");
						salvatore_log("svn propset --revprop -r $revision svn:author '$email' $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert\n");
					}
				}
			}
		}

		/**
		 * Si on a encore un fichier ajouté ou modifié
		 * On commite le tout avec salvatore
		 */
		if (strlen(trim(exec("svn status $f |awk /^[MA]/")))>1){
			$commit_message = "[Salvatore] [source:$path_svn $module] Export depuis http://trad.spip.net\n\n";
			$commit_message .= $message_commit . "\n";
			salvatore_log("On commit $f car il reste des fichiers\n");
			salvatore_log(exec("svn commit $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert -m " . escapeshellarg($commit_message)) . "\n");
			$revision_fin = exec("svn up $f && env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^Last Changed Rev/ { print $4 }'");
			if (!$credentials && $propset){
				salvatore_log(exec("svn propset --revprop -r $revision_fin svn:author 'salvatore@rezo.net' $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert") . "\n");
				salvatore_log("svn propset --revprop -r $revision_fin svn:author 'salvatore@rezo.net' $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert\n");
			}
		}

		if (file_exists($f . 'message_commit.inc')){
			unlink($f . 'message_commit.inc');
		}
	}
}

return 0;
/* MAIN ***********************************************************************/
