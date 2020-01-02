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

// il envoie les fichiers dans le svn


require_once(dirname(__FILE__).'/inc_tradlang.php');
require_once(_DIR_ETC.'salvatore_passwd.inc');

if (!isset($SVNUSER) or !isset($SVNPASSWD)) {
	die('Veuillez indiquer $SVNUSER et $SVNPASSWD dans le fichier '._DIR_ETC.'salvatore_passwd.inc');
}

$propset = true;
if (isset($NO_PROPSET)) {
	$propset = false;
}

$tmp=_SALVATORE_TMP;

/* MAIN ***********************************************************************/

trad_log("\n=======================================\nPOUSSEUR\nPrend les fichiers langue dans sa copie locale et les commite SVN\n=======================================\n");

$liste_sources=charger_fichier_traductions(); // chargement du fichier traductions.txt

foreach ($liste_sources as $source) {
	$credentials = false;
	$module = $source[1];
	trad_log("===== Module $module ======================================\n");

	$domaine_svn = parse_url($source[0]);
	$domaine_svn = $domaine_svn['host'];
	if (isset($domaines_exceptions) and is_array($domaines_exceptions) && in_array($domaine_svn, $domaines_exceptions)) {
		/**
		 * On est dans une exception (Github?)
		 */
		if (is_array($domaines_exceptions_credentials) and isset($domaines_exceptions_credentials[$domaine_svn])) {
			$user = $domaines_exceptions_credentials[$domaine_svn]['user'];
			$pass = $domaines_exceptions_credentials[$domaine_svn]['pass'];
			$credentials = true;
		}
	}
	if (isset(${$module.'_user'})) {
		$user = ${$module.'_user'};
		$pass = ${$module.'_passwd'};
	} elseif (!$credentials) {
		$user = $SVNUSER;
		$pass = $SVNPASSWD;
	}

	$f = _SALVATORE_TMP.$module.'/';

	/**
	 * On ajoute les .xml
	 */
	trad_log(exec("svn add --quiet $f*xml 2>/dev/null")."\n");
	$ignore = array(
	//	'spip','ecrire','public'
		'couteau','couteauprive','paquet-couteau_suisse'// désactivés suite à scandale, je ne sais pas comment le gérer correctement
	);

	if (in_array($module, $ignore)) {
		trad_log("$module ignore'\n");
	} else {
		$depot = exec("env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^Repository Root/ { print $3 }'").'/';
		$svn_url = exec("env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^URL/ { print $2 }'").'/';
		$path_svn = str_replace($depot, '', $svn_url);
		$message = $message_commit = $commiteurs = false;
		if (file_exists($f.'message_commit.inc')) {
			$message = true;
			require_once(dirname(__FILE__).'/'.str_replace('./', '', $f).'message_commit.inc');
		}

		if (is_array($commiteurs) && count($commiteurs) > 0) {
			foreach ($commiteurs as $lang => $email) {
				if (strlen($email) > 1) {
					$message_commit_unique = "[Salvatore] [source:$path_svn $module] Export depuis http://trad.spip.net de la langue $lang";
					/**
					 * Si plusieurs commiteurs (veut dire que plusieurs fichiers sont à commiter)
					 * ou si le fichier original est modifié, on ne commit que fichier par fichier
					 */
					if (count($commiteurs) > 1 || in_array(substr(exec('svn status '._SALVATORE_TMP.$source[1].'/' . $source[1] . '_' . $source[2] . '.php'), 0, 1), array('A', 'M'))) {
						$path = $f.$module.'_'.$lang.'.php';
					} else {
						/**
						 * Sinon on ne s'embarasse pas, on balance tout avec cet utilisateur
						 */
						$path = $f;
					}
					trad_log("On devrait commiter $path avec comme message '$message_commit_unique' avec l'email $email\n");
					trad_log(exec("svn commit $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert -m ".escapeshellarg($message_commit_unique))."\n");
					$revision = exec("svn up $path && env LC_MESSAGES=en_US.UTF-8 svn info $path |awk '/^Last Changed Rev/ { print $4 }'");
					if ($propset) {
						trad_log(exec("svn propset --revprop -r $revision svn:author '$email' $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert")."\n");
						trad_log("svn propset --revprop -r $revision svn:author '$email' $path --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert\n");
					}
				}
			}
		}

		/**
		 * Si on a encore un fichier ajouté ou modifié
		 * On commite le tout avec salvatore
		 */
		if (strlen(trim(exec("svn status $f |awk /^[MA]/"))) > 1) {
			$commit_message = "[Salvatore] [source:$path_svn $module] Export depuis http://trad.spip.net\n\n";
			$commit_message .= $message_commit."\n";
			trad_log("On commit $f car il reste des fichiers\n");
			trad_log(exec("svn commit $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert -m ".escapeshellarg($commit_message))."\n");
			$revision_fin = exec("svn up $f && env LC_MESSAGES=en_US.UTF-8 svn info $f |awk '/^Last Changed Rev/ { print $4 }'");
			if (!$credentials && $propset) {
				trad_log(exec("svn propset --revprop -r $revision_fin svn:author 'salvatore@rezo.net' $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert")."\n");
				trad_log("svn propset --revprop -r $revision_fin svn:author 'salvatore@rezo.net' $f --username $user --password $pass --no-auth-cache --non-interactive --trust-server-cert\n");
			}
		}

		if (file_exists($f.'message_commit.inc')) {
			unlink($f.'message_commit.inc');
		}
	}
}

return 0;
/* MAIN ***********************************************************************/
