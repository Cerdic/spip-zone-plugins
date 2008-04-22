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
		if(!$deja) {
			$r = spip_query("SELECT id_auteur FROM spip_auteurs_articles WHERE id_article=$id_article");
			while($row = spip_fetch_array($r))
				$auteurs[] = intval($row['id_auteur']);
			$deja = true;
		}
		return in_array($id_auteur, $auteurs);
	}

// lister les themes présents dans plugins/spipclear/themes  
  function lister_themes() {
    $dir = _DIR_PLUGIN_SPIPCLEAR.'themes/';
    $Treps_themes = array();
    $htm = '';
    if (is_dir($dir) AND $t = @opendir($dir)) {
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
                    $htm .= '<div style="line-height: 52px;">';
                    if ($capture) {
                        $htm .= '<a href="#" class="mini_capture" title="<:spipclear:voir_capture:>"><img src="'._DIR_PLUGIN_SPIPCLEAR.'themes/'.$rt.'/screenshot.jpg" style="width: 48px; height: 42px; vertical-align: middle;" /></a> ';
                    }
                    $htm .= '<a id="'.$nom_theme.'" class="theme" href="#" title="<:spipclear:selectionner_theme:>">'.$nom_theme.'</a></div>'."\r\n";
                }
            }
        }
    }
    return $htm;
  }
?>
