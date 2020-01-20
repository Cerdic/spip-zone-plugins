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
function salvatore_svn_lastmodified_file($dir_repo, $file) {

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
function salvatore_svn_status_file($dir_repo, $file_or_files) {

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
