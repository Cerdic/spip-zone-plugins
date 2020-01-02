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

    Copyright 2003-2018
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
 		kent1 <kent1@arscenic.info>
*/


/**
 * Ce script va chercher les fichiers définis dans le fichier traductions/traductions.txt
 *
 */
require_once(dirname(__FILE__) . '/inc_tradlang.php');
require_once(_DIR_ETC . 'salvatore_passwd.inc');
$tmp = _DIR_SALVATORE_TMP;

trad_log("\n=======================================\nTIREUR\nVa chercher les fichiers dans SVN et les depose dans sa copie locale\n=======================================\n");

$liste_sources = charger_fichier_traductions(); // chargement du fichier traductions.txt

$ret = 0;
$cmd = false;

foreach ($liste_sources as $source){
	if (isset($domaines_exceptions) && is_array($domaines_exceptions) && in_array($domaine_svn, $domaines_exceptions)){
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
	trad_log("\n===== Module " . $source[1] . " =====\n");
	if (is_dir($tmp . $source[1] . '/.svn')){
		$depot = "env LANG=en_US svn info --non-interactive --trust-server-cert --username $user --password $pass " . $tmp . $source[1] . " | awk '/^URL:/ { print $2 }'";
		$depot = exec($depot, $depot);
		$depot = $depot . '/';
		if ($depot!=$source[0]){
			$domaine_depot = parse_url($depot);
			$domaine_depot = $domaine_depot['host'];
			$domaine_source = parse_url($source[0]);
			$domaine_source = $domaine_source['host'];
			/**
			 * Simple switch si même serveur sinon on supprime le répertoire et on refait un checkout
			 */
			if ($domaine_depot==$domaine_source){
				$cmd = "svn switch --non-interactive --ignore-ancestry --trust-server-cert --username $user --password $pass " . $source[0] . ' ' . $tmp . $source[1] . '/';
			} else {
				$cmd = 'rm -Rvf ' . $tmp . $source[1] . "/ && svn checkout  --non-interactive --trust-server-cert --username $user --password $pass --non-recursive " . $source[0] . '/ ' . $tmp . $source[1] . '/';
			}
		} else {
			$revision_actuelle = "env LANG=en_US svn info  --non-interactive --trust-server-cert --username $user --password $pass " . $tmp . $source[1] . " | awk '/^Revision:/ { print $2 }'";
			$revision_actuelle = exec($revision_actuelle, $revision_actuelle);
			$last_revision = "env LANG=en_US svn info  --non-interactive --trust-server-cert --username $user --password $pass " . $source[0] . " | awk '/^Last\ Changed\ Rev:/ { print $4 }'";
			$last_revision = exec($last_revision, $last_revision);
			if ($revision_actuelle>=$last_revision){
				trad_log("Pas besoin de mettre à jour\n");
			} else {
				$cmd = "svn update  --non-interactive --trust-server-cert --username $user --password $pass --non-recursive --accept theirs-full " . $tmp . $source[1] . '/';
			}
		}
	} else {
		$cmd = "svn checkout  --non-interactive --trust-server-cert --username $user --password $pass --non-recursive " . $source[0] . '/ ' . $tmp . $source[1] . '/';
	}

	if ($cmd){
		exec("$cmd 2> /dev/null", $out, $int);
		if ($int==0){
			trad_log(end($out) . "\n");
		} else {
			$sujet = 'Tireur : Erreur';
			$corps = $source[0] . '/ ' . $source[1] . "\n\n";
			$corps .= "L'adresse distante de ce module n'est certainement plus valide\n\n";
			trad_sendmail($sujet, $corps);
			die("L'adresse distante de ce module n'est certainement plus valide\n\n");
		}
	}

	// controle des erreurs : requiert au moins 1 fichier par module !
	if (!file_exists($tmp . $source[1] . '/' . $source[1] . '_' . $source[2] . '.php')){
		$ret = 1;
		$sujet = 'Tireur : Erreur';
		$corps = "! Erreur pas de fichier de langue conforme dans le module : $tmp" . $source[1] . "\n";
		trad_sendmail($sujet, $corps);
		die("! Erreur pas de fichier de langue conforme dans le module : $tmp" . $source[1] . "\n");
	}
	$cmd = false;
}

return $ret;
