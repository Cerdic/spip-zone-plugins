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

/**
 * Formate l'auteur en Nom <email> si jamais seul l'email est fourni
 * @param string $author
 * @return string
 */
function salvatore_git_format_author($author) {
	if (strpos($author, '<') !== false and strpos($author, '>') !== false) {
		return $author;
	}
	else {
		$name = explode('@', $author);
		$name = reset($name);
		return "$name <$author>";
	}
}

/**
 * Lire la date de derniere modif d'un fichier versionne
 * (retourne 0 si le fichier n'est pas versionne)
 * @param string $dir_repo
 * @param string $file
 * @return false|int
 */
function salvatore_git_lastmodified_file($dir_repo, $file) {

	$d = getcwd();
	chdir($dir_repo);
	$file = escapeshellarg($file);
	$lastmodified = exec("git log -1 -c --pretty=tformat:'%ct' $file | head -1");
	$lastmodified = intval(trim($lastmodified));
	chdir($d);
	return $lastmodified;
}

/**
 * Afficher le status d'un ou plusieurs fichiers
 * @param string $dir_repo
 * @param string|array $file_or_files
 * @return string
 */
function salvatore_git_status_file($dir_repo, $file_or_files) {

	if (is_array($file_or_files)) {
		$file_or_files = array_map('escapeshellarg', $file_or_files);
		$file_or_files = implode(' ', $file_or_files);
	}
	else {
		$file_or_files = escapeshellarg($file_or_files);
	}

	$d = getcwd();
	chdir($dir_repo);
	$output = array();
	exec("git status --short $file_or_files 2>&1", $output);
	//exec("svn status $files_list 2>&1", $output);
	chdir($d);
	return implode("\n", $output);
}

/**
 * Commit une liste de fichiers avec un message et auteur fourni
 * on utilise pas $user et $pass en git pour commit
 * @param string $dir_repo
 * @param array $files
 * @param string $message
 * @param string $author
 * @param string $user
 * @param string $pass
 * @return array
 */
function salvatore_git_commit_files($dir_repo, $files, $message, $author, $user=null, $pass=null) {
	$files = array_map('escapeshellarg', $files);
	$files = implode(' ', $files);

	$d = getcwd();
	chdir($dir_repo);
	$output = array();
	$res = true;
	// on ajoute tous les fichiers pour commit
	$commands = [
		"git add $files 2>&1",
		"git commit -m " . escapeshellarg($message)." --author=".escapeshellarg(salvatore_git_format_author($author)) . " 2>&1",
	];

	foreach ($commands as $command) {
		$output[] = "> $command";
		$return_var = 0;
		exec($command, $output, $return_var);
		// si une erreur a eu lieu le signaler dans le retour
		if ($return_var) {
			$res = false;
		}
	}
	chdir($d);

	return array($res, implode("\n", $output));
}

/**
 * on utilise pas $user et $pass en git pour push car ils sont dans le remote si c'est un https
 * et si c'est ssh il faut une cle pour le user www-data
 *
 * @param string $dir_repo
 * @param null $user
 * @param null $pass
 * @return array
 */
function salvatore_git_push_repository($dir_repo, $user=null, $pass=null) {
	$d = getcwd();
	chdir($dir_repo);
	$output = array();
	$res = true;
	// on ajoute tous les fichiers pour commit
	$commands = [
		"git pull --rebase 2>&1",
		// TODO : activer le push quand on sera en prod
		//"git push 2>&1",
	];

	foreach ($commands as $command) {
		$output[] = "> $command";
		$return_var = 0;
		exec($command, $output, $return_var);
		// si une erreur a eu lieu le signaler dans le retour
		if ($return_var) {
			$res = false;
		}
	}
	chdir($d);

	return array($res, implode("\n", $output));
}