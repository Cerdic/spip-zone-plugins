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
 * Lire la date de derniere modif d'un fichier versionne
 * (retourne 0 si le fichier n'est pas versionne)
 * @param string $dir_repo
 * @param string $file
 * @return false|int
 */
function salvatore_vcs_svn_lastmodified_file_dist($dir_repo, $file) {

	$d = getcwd();
	chdir($dir_repo);
	$file = escapeshellarg($file);
	$lastmodified = exec('env LC_MESSAGES=en_US.UTF-8 svn info ' . $file . "| awk '/^Last Changed Date/ { print $4 \" \" $5 }'");
	$lastmodified = strtotime($lastmodified);
	chdir($d);
	return $lastmodified;
}

/**
 * Afficher le status d'un ou plusieurs fichiers
 * @param string $dir_repo
 * @param string|array $file_or_files
 * @return string
 */
function salvatore_vcs_svn_status_file_dist($dir_repo, $file_or_files) {

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
	exec("svn status $file_or_files 2>&1", $output);
	chdir($d);
	return implode("\n", $output);
}


/**
 * Commit une liste de fichiers avec un message et auteur fourni
 *
 * @param string $dir_repo
 * @param array $files
 * @param string $message
 * @param string $author
 * @param string $user
 * @param string $pass
 * @return array
 */
function salvatore_vcs_svn_commit_files_dist($dir_repo, $files, $message, $author, $user=null, $pass=null) {

	// lister deja les fichiers qui necessitent un svn add (fichiers ajoutes qui ne sont pas dans le repo)
	$files_to_add = array();
	foreach ($files as $file) {
		if (!salvatore_vcs_svn_lastmodified_file($dir_repo, $file)) {
			$files_to_add[] = $file;
		}
	}

	$files = array_map('escapeshellarg', $files);
	$files = implode(' ', $files);

	$files_to_add = array_map('escapeshellarg', $files_to_add);
	$files_to_add = implode(' ', $files_to_add);

	$d = getcwd();
	chdir($dir_repo);
	$output = array();
	$res = true;

	$auth = "";
	$auth_disp = "";
	if ($user) {
		$auth .= " --username=".escapeshellarg($user);
		$auth_disp .= " --username=".escapeshellarg('xxxxx');
	}
	if ($user) {
		$auth .= " --password=".escapeshellarg($pass);
		$auth_disp .= " --password=".escapeshellarg('xxxxx');
	}

	$commands = [];
	if ($files_to_add) {
		$commands[] = "svn add --quiet $files_to_add 2>&1";
	}
	// TODO : activer le commit quand on sera en prod
	// $commands[] = "svn commit $files{$auth} --no-auth-cache --non-interactive --trust-server-cert -m " . escapeshellarg($message) . " 2>&1";

	foreach ($commands as $command) {
		$output[] = "> " . ($auth ? str_replace($auth, $auth_disp, $command) : $command);
		$return_var = 0;
		exec($command, $output, $return_var);
		// si une erreur a eu lieu le signaler dans le retour
		if ($return_var) {
			$res = false;
		}
	}
	if ($res and $author and _SALVATORE_SVN_PROPSET) {
		if ($revision = exec("svn up . && env LC_MESSAGES=en_US.UTF-8 svn info . |awk '/^Last Changed Rev/ { print $4 }'")) {
			$command = "svn propset --revprop -r $revision svn:author ".escapeshellarg($author). " .{$auth} --no-auth-cache --non-interactive --trust-server-cert";
			$output[] = "> " . ($auth ? str_replace($auth, $auth_disp, $command) : $command);
			exec($command, $output, $return_var);
			if ($return_var) {
				$res = false;
			}
		}
	}
	chdir($d);

	return array($res, implode("\n", $output));
}



/**
 * Rien a faire : en svn le commit push, mais fonction symetrique de git
 *
 * @param string $dir_repo
 * @param null $user
 * @param null $pass
 * @return array
 */
function salvatore_vcs_svn_push_repository_dist($dir_repo, $user=null, $pass=null) {
	return array(true, '');
}