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
 * @param array $liste_sources
 * @param string|null $tmp
 * @return bool
 * @throws Exception
 */
function salvatore_tirer($liste_sources, $tmp=null) {
	include_spip('inc/salvatore');
	salvatore_init();

	if (is_null($tmp)) {
		$tmp = _DIR_SALVATORE_TMP;
	}

	salvatore_check_dir($tmp);
	$done = array();

	foreach ($liste_sources as $source){
		salvatore_log("\n--- Module " . $source['module'] . " | " . $source['url']);

		$url_with_credentials = salvatore_set_credentials($source['methode'], $source['url'], $source['module']);

		$dir_checkout = $tmp . $source['dir_checkout'];
		$dir_module = $tmp . $source['dir_module'];
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
			passthru("$cmd 2>/dev/null", $return);
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
	}

	return true;
}

