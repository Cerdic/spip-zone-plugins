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

include_spip('inc/salvatore_git');
include_spip('inc/salvatore_svn');

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

	$url_gestionnaire = salvatore_get_self_url();

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
					$message_commit = trim($commit_infos['.message']) . "\n";
					unset($commit_infos['.message']);
				}
				$dir_depot = $dir_depots . $source['dir_checkout'];
				$subdir = '';
				if (isset($source['dir'])) {
					$subdir = $source['dir'] . DIRECTORY_SEPARATOR;
				}

				// reorganiser les fichiers a commit et preparer les messages de commit
				// - ignorer les fichiers non modifies, ou non versionnes et qui ne doivent pas etre ajoutes
				// - regrouper par auteur

				$commits_todo = array();
				$salvatore_status_file = "salvatore_" . $source['methode'] . "_status_file";
				$salvatore_commit_files = "salvatore_" . $source['methode'] . "_commit_files";
				$salvatore_push_repository = "salvatore_" . $source['methode'] . "_push_repository";

				foreach ($commit_infos as $what => $commit_info) {

					$file = $commit_info['file_name'];

					if ($commit_info['lastmodified'] or $commit_info['must_add']) {
						$status = $salvatore_status_file($dir_depots . $source['dir_checkout'], $subdir . $file);

						// fichier nouveau ou modifie (sinon on l'ignore)
						if ($status) {
							$author = 0;
							if (!empty($commit_info['author'])) {
								$author = $commit_info['author'];
							}
							// si c'est le xml et qu'on a un seul auteur de commit, on lui fait commit aussi le xml
							elseif($what === '.xml' and count($commits_todo)===1) {
								$author = array_keys($commits_todo);
								$author = reset($author);
							}

							if (!isset($commits_todo[$author])) {
								$commits_todo[$author] = array(
									'files' => array(),
									'message' => []
								);
								if ($message_commit) {
									$commits_todo[$author]['message'][] = $message_commit;
								}
							}
							$commits_todo[$author]['files'][] = $subdir . $file;
							if ($what === '.xml') {
								$message = "[Salvatore] [source:$subdir $module] Mise a jour du bilan depuis $url_gestionnaire";
							}
							else {
								$message = "[Salvatore] [source:$subdir $module] Export depuis $url_gestionnaire";
							}
							if (!empty($commit_info['lang'])) {
								$message .= " de la langue " . $commit_info['lang'];
							}
							if (!empty($commit_info['message'])) {
								$message .= "\n            " . $commit_info['message'];
							}
							$commits_todo[$author]['message'][] = $message;
						}

					}

				}

				// on peut maintenant lancer les commits
				// ajoutons les credentials dans la source pour pouvoir commit ou push
				$url_with_credentials = salvatore_set_credentials($source['methode'], $source['url'], $source['module']);
				$parts = parse_url($url_with_credentials);
				if (!empty($parts['user']) and !empty($parts['pass'])){
					$source['user'] = $parts['user'];
					$source['pass'] = $parts['pass'];
				}

				foreach ($commits_todo as $author => $commit_todo) {
					if (!$author) {
						$author = _SALVATORE_AUTHOR_COMMITS;
					}
					$message = implode("\n", $commit_todo['message']);
					salvatore_log("Commit de <info>$author</info> :" . implode(', ', $commit_todo['files']));
					salvatore_log("\t" . str_replace("\n", "\n\t", $message));

					list($res,$out) = $salvatore_commit_files($dir_depots . $source['dir_checkout'], $commit_todo['files'], $message, $author, empty($source['user']) ? null : $source['user'], empty($source['pass']) ? null : $source['pass']);
					salvatore_log($out);
					if (!$res) {
						salvatore_fail("[Pousseur] Erreur sur $module", "Erreur lors du commit :\n$out");
					}
				}

				// TODO : push
				// ne fera rien en svn (deja pushe)

			}
		}

	}

	return true;
}

return;

/*
foreach ($liste_sources as $source){
	$credentials = false;
	$module = $source[1];
	salvatore_log("===== Module $module ======================================\n");


	$f = _DIR_SALVATORE_TMP . $module . '/';

	/**
	 * On ajoute les .xml
	 * /
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
					 * /
					if (count($commiteurs)>1 || in_array(substr(exec('svn status ' . _DIR_SALVATORE_TMP . $source[1] . '/' . $source[1] . '_' . $source[2] . '.php'), 0, 1), array('A', 'M'))){
						$path = $f . $module . '_' . $lang . '.php';
					} else {
						/**
						 * Sinon on ne s'embarasse pas, on balance tout avec cet utilisateur
						 * /
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
		 * /
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
