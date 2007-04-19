<?php

	define('_SVN_COMMAND', 'svn');

	// la fonction qui fait le travail
	function update_svn($l) {
		$l = trim($l);

		if (!$l OR substr($l,0,1) == '#') return NULL; // commentaires

		@list($src, $dest, $rev, $user) = explode(' ',$l);

		if (!preg_match(',^(https?|svn)://,', $src))
			return $src; // erreur

		if (!is_dir($dest))
			mkdir($dest, 0777, 'recursive');

		if (!is_dir($dest)
		OR !is_writable($dest))
			return "Impossible d'ecrire dans ".$dest; // erreur

		// Checkout ?
		if (!file_exists($entries = "$dest/.svn/entries")) {
			$command = "checkout $src/ $dest/";
		}

		else {
			// nouveau format de .svn/entries
			$info = _SVN_COMMAND." $user info --xml $dest/";
			exec($info, $out);
			if (preg_match(',<url>(.*?)</url>,', join('',$out), $r)) {
				$old_src = $r[1];
			} else {
				// ancien format
				$entries = join("\n", file($entries));
				if (!preg_match(',\surl="([^"]+)",', $entries, $r))
					return "fichier .svn/entries non conforme ou illisible";
				$old_src = $r[1];
			}

			// Switch ?
			if ($old_src != $src) {
				$command = "switch --relocate $old_src/ $src/ $dest/";
			}
			
			// Update
			else {
				if ($rev)
					$command = "update --revision $rev $dest/";
				else
					$command = "update $dest/";
			}
		}

		if ($command) {
			$command = _SVN_COMMAND." $user ".$command;
			$out = array();
			exec($command,$out);
			array_unshift($out, $command);
			return $out;
		}

	}

	function traiter_config_svn($config = array()) {
		foreach($config as $l) {
			echo "<hr /><b>", htmlspecialchars($l), "</b>";
			$res = update_svn($l);
			if (is_string($res))
				echo "<br /><b>Erreur: ",
					htmlspecialchars($res),
					"</b>";
			if (is_array($res))
				echo "<br />".nl2br(htmlspecialchars(join("\n", $res)));
			echo "<br />\n";
		}
	}

?>
