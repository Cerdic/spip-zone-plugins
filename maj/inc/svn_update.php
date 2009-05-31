<?php

	// la fonction qui fait le travail
	function update_svn($l) {
		$l = trim($l);

		if (!$l OR substr($l,0,1) == '#') return NULL; // commentaires

		@list($src, $dest, $rev, $user) = explode(' ',$l);

		if (!preg_match(',^(https?|svn)://,', $src))
			return $src; // erreur

		if (!is_dir($dest))
			mkdir($dest, 0777, 'recursive'); //PHP 5.0.0 &

		if (!is_dir($dest))
			mkdir_r($dest); //PHP 4.0.8 & SPIP 1.9.2

		if (!is_dir($dest)
		OR !is_writable($dest))
			return "Impossible d'ecrire dans ".$dest; // erreur

		// Checkout ?
		if (!file_exists($entries = "$dest/.svn/entries")) {
			$command = "checkout $src/ $dest/";
		}

		else {
			$entries = join("\n", file($entries));
			if (!preg_match(',\surl="([^"]+)",', $entries, $r))
				return "fichier .svn/entries non conforme ou illisible";
			$old_src = $r[1];

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
			$test = exec($command,$out, $return);
			array_unshift($out, $return, $command);
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
