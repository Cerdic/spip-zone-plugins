<?php

	define('_SVN_COMMAND', 'svn');

	// la fonction qui fait le travail
	function update_svn($l) {
		$l = trim($l);

		if (!$l OR substr($l,0,1) == '#') return NULL; // commentaires

		$l = explode(' ',$l);
		$src = array_shift($l);
		$dest = array_shift($l);

		// une revision est numerique ou parmi quelques valeurs connues
		// sinon c'est une clause user
		$rev = array_shift($l);
		if (!is_numeric($rev) AND !in_array($rev,array('HEAD','BASE','COMMITED','PREV')))
			array_unshift($l,$rev);

		// la clause user est le reste
		$user = join(' ',$l);

		if (!preg_match(',^(https?|svn)://,', $src))
			return $src; // erreur

		if (!is_dir($dest))
			mkdir($dest, 0777, 'recursive');

		if (!is_dir($dest)
		OR !is_writable($dest))
			return "Impossible d'ecrire dans ".$dest; // erreur

		// Checkout ?
		if (!file_exists($entries = "$dest/.svn/entries")) {
		    $command[] = "export --force $src/ $dest/";
			$command[] = "checkout $src/ $dest/";
		}

		else {
			// nouveau format de .svn/entries
			$info = _SVN_COMMAND." info --xml $dest/";
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
				$command[] = "switch --relocate $old_src/ $src/ $dest/";
			}
			
			// Update
			else {
				if ($rev)
					$command[] = "update --revision $rev $dest/";
				else
					$command[] = "update $dest/";
			}
		}

        	//execute les commandes svn
		if ($command) {
			//tableaux de résultat
        		$out = array();   
        		$out_local = array();
			//parcours les commandes demandées
    			foreach ($command as $cmd ) {
    				//redéfini la commande complétement    		 
    				$cmd_exec = _SVN_COMMAND." $user ".$cmd." 2>&1";
    				//execute la commande et sauve le resultat dans local_out
				exec($cmd_exec,$local_out);
				//rappelle la commande executée
    				$cmd_aff = _SVN_COMMAND.$cmd." 2>&1";
				array_unshift($local_out, $cmd_aff);
				//empile le resultat local à la sortie finale
				$out = $out + $local_out;
        		}
			return $out;
		}

	}

	function traiter_config_svn($config = array()) {
		foreach($config as $l) {
			echo "<hr /><b>", htmlspecialchars($l), "</b>";
			$res = update_svn($l);
			if (is_string($res)){
				include_spip('inc/charsets');
				$res = importer_charset($res);
				echo "<br /><b>Erreur: ",
					htmlspecialchars($res),
					"</b>";
			}
			if (is_array($res)){
				include_spip('inc/charsets');
				$res = importer_charset(join("\n", $res),'iso-8859-1');
				echo "<br />".nl2br(htmlspecialchars($res));
			}
			echo "<br />\n";
		}
	}

?>
