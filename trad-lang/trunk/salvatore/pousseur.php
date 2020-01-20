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

include_spip('salvatore/vcs/git');
include_spip('salvatore/vcs/svn');

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


		if (file_exists($dir_module . '/.salvatore.ignore.' . $module)) {
			salvatore_log("<comment>Module $module ignoré</comment>");
		}
		else {
			$res = savatore_commit_and_push_module($source, $dir_modules, $dir_depots, $url_gestionnaire);
			if ($res) {
				salvatore_log("Module $module <info>OK</info>\n");
			}
		}
	}

	return true;
}

/**
 * Commiter et pusher les modifs sur un module
 * @param array $source
 * @param string $dir_modules
 * @param string $dir_depots
 * @param string $url_gestionnaire
 * @return bool
 * @throws Exception
 */
function savatore_commit_and_push_module($source, $dir_modules, $dir_depots, $url_gestionnaire) {

	$dir_module = $dir_modules . $source['dir_module'];
	$module = $source['module'];

	$file_commit = $dir_module . '/' . $module . '.commit.json';

	if (!file_exists($file_commit)
	  or !$commit_infos = file_get_contents($file_commit)
	  or !$commit_infos = json_decode($commit_infos, true)) {
		salvatore_log("<comment>Module $module rien à faire (pas de fichier $file_commit ou fichier invalide)</comment>");
		return false;
	}

	// on a la liste des fichiers a commit
	$message_commit = '';
	if (isset($commit_infos['.message'])) {
		$message_commit = trim($commit_infos['.message']) . "\n";
		unset($commit_infos['.message']);
	}

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

	// tous les commits sont faits
	// on peut supprimer le fichier qui liste les commits
	@unlink($file_commit);

	// et push si besoin
	// ne fera rien en svn (deja pushe)
	list($res,$out) = $salvatore_push_repository($dir_depots . $source['dir_checkout'], empty($source['user']) ? null : $source['user'], empty($source['pass']) ? null : $source['pass']);
	salvatore_log($out);
	if (!$res) {
		salvatore_fail("[Pousseur] Erreur sur $module", "Erreur lors du commit :\n$out");
	}

	return true;
}
