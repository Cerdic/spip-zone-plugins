<?php

	function affdate_long($date) {
		return affdate_base($date, 'nom_jour').' '.affdate_base($date, 'entier');
	}

	/***
	* |me compare un id_auteur avec les auteurs d'un article
	* et renvoie la valeur booleenne true (vrai) si on trouve une correspondance
    * utilisation: <div id="forum#ID_FORUM"[(#ID_ARTICLE|me{#ID_AUTEUR}|?{' ', ''})class="me"]>
	***/
	function me($id_article, $id_auteur = 0) {
		static $deja = false;
		static $auteurs = array();

	/* en spip 3 auteur_articles est remplacé - adapter la requete */
		$table="spip_auteurs_articles";
		$where="id_article";
		if ($GLOBALS['meta']['version_installee'] >= '19268') {
			$table="spip_auteurs_liens";
			$where="objet='article' AND id_objet";
		}
		if(!$deja) {
			$r = spip_query("SELECT id_auteur FROM ".$table." WHERE ".$where."=$id_article");
			while($row = spip_fetch_array($r))
				$auteurs[] = intval($row['id_auteur']);
			$deja = true;
		}
		return in_array($id_auteur, $auteurs);
	}

// lister les themes présents dans plugins/spipclear/themes  
  function lister_themes() {
    $dir = _DIR_PLUGIN_SPIPCLEAR.'themes/';
    $dir_perso = find_in_path('squelettes/themes/');
    $Treps_themes = array();
    $htm = '';
    if (is_dir($dir) AND $t = @opendir($dir)) {
		$htm .= '<ul style="height: 350px; overflow: auto; margin: 10px 0; border: 1px solid #ccc; background: #fff;">';
        while (($rt = readdir($t)) !== false) {
            if (is_dir($dir.$rt) AND $r = @opendir($dir.$rt) AND $rt != '..') {
                $capture = false;
                $nom_theme = false;
                while (($f = readdir($r)) !== false) {
                  // à minima un theme doit avoir un fichier style.css
                    if ($f == 'style.css') $nom_theme = $rt;
                    if ($f == 'screenshot.jpg') $capture = true;
                }
                if ($nom_theme) {
					$htm .= '<li style="padding-left: 10px; border-bottom: 2px solid #ccc;"><p><a id="'. $nom_theme .'" class="theme" href="#" title="'. _T(selectionner_theme) .'">'. $nom_theme .'</p>';
                    if ($capture) {
                        $htm .= '<img src="'._DIR_PLUGIN_SPIPCLEAR.'themes/'.$rt.'/screenshot.jpg" />';
                    }
                    $htm .= "</a></li>\r\n";
                }
            }
        }
		if (is_dir($dir_perso) AND $t = @opendir($dir_perso)) {
			while (($rt = readdir($t)) !== false) {
				if (is_dir($dir_perso.$rt) AND $r = @opendir($dir_perso.$rt) AND $rt != '..') {
					$capture = false;
					$nom_theme = false;
					while (($f = readdir($r)) !== false) {
					  // à minima un theme doit avoir un fichier style.css
						if ($f == 'style.css') $nom_theme = $rt;
						if ($f == 'screenshot.jpg') $capture = true;
					}
					if ($nom_theme) {
						$htm .= '<li style="padding-left: 10px; border-bottom: 2px solid #ccc;"><p><a id="'. $nom_theme .'" class="theme" href="#" title="'. _T(selectionner_theme) .'">'. $nom_theme .'</p>';
						if ($capture) {
							$htm .= '<img src="'.$dir_perso.$rt.'/screenshot.jpg" />';
						}
						$htm .= "</a></li>\r\n";
					}
				}
			}
		}
		$htm .= '</ul>';
    }
    return $htm;
  }
?>
