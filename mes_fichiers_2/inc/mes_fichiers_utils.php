<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Renvoie la liste des fichiers et repertoires a sauver
function mes_fichiers_a_sauver() {
	if(defined('_DIR_SITE')){
		$dir_racine = _DIR_SITE;
	}else{
		$dir_racine = _DIR_RACINE;
	}

	$htaccess = defined('_ACCESS_FILE_NAME') ? $dir_racine._ACCESS_FILE_NAME : $dir_racine.'.htaccess';
	$IMG = defined('_DIR_IMG') ? _DIR_IMG: $dir_racine.'IMG/';
	$tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP: $dir_racine.'tmp/dump/';

	$liste = array();

	// le fichier d'options si il existe
	if (@is_readable($f = $dir_racine . _NOM_PERMANENTS_INACCESSIBLES . _NOM_CONFIG . '.php')
	OR (!defined('_DIR_SITE') && @is_readable($f = _FILE_OPTIONS))){
		$liste[] = $f;
	}
	// le fichier .htaccess a la racine qui peut contenir des persos
	if (@is_readable($htaccess))
		$liste[] = $htaccess;
	// le fameux repertoire des documents et images
	if (@is_dir($IMG))
		$liste[] = $IMG;
	// le(s) dossier(s) des squelettes nommes
	if (strlen($GLOBALS['dossier_squelettes']))
		foreach (explode(':', $GLOBALS['dossier_squelettes']) as $_dir) {
			$dir = ($_dir[0] == '/' ? '' : $dir_racine) . $_dir . '/';
			if (@is_dir($dir))
				$liste[] = $dir;
		}
	else
		if (@is_dir($dir_racine.'squelettes/'))
			$liste[] = $dir_racine.'squelettes/';
	// le dernier fichier de dump de la base
	$dump = preg_files($tmp_dump);
	$fichier_dump = '';
	$mtime = 0;
	foreach ($dump as $_fichier_dump) {
		if (($_mtime = filemtime($_fichier_dump)) > $mtime) {
			$fichier_dump = $_fichier_dump;
			$mtime = $_mtime;
		}
	}
	if ($fichier_dump)
		$liste[] = $fichier_dump;
	// On ajoute via un pipeline des fichiers specifiques a d'autres plugins
	$liste_en_plus = array();
	$liste_en_plus = pipeline('mes_fichiers_a_sauver', $liste_en_plus);
	$liste = array_merge(array_unique(array_merge($liste, $liste_en_plus)));

	return $liste;
}

// Renvoie la liste des fichiers et repertoires a sauver classee par date inverse (max 20)
function mes_fichiers_a_telecharger() {
	$prefixe = lire_config('mes_fichiers/prefixe','mf2');
	$liste = preg_files(_DIR_MES_FICHIERS . $prefixe.'_*.zip', 20);
	return array_reverse($liste);
}

// Convertit un mtime en date
function filemtime_2_date($mtime) {
	return date('Y-m-d H:i:s',$mtime);
}

// Renvoie la liste des repertoires et fichiers de base archives (la liste de choix)
function mes_fichiers_resumer_zip($zip) {
	include_spip('inc/pclzip');
	$fichier_zip = new PclZip($zip);
	$proprietes = $fichier_zip->properties();
	$resume = NULL;
	if ($proprietes == 0) {
		$resume .= _T('mes_fichiers:message_zip_propriete_nok');
		spip_log('*** MES_FICHIERS (mes_fichiers_resumer_zip) ERREUR '.$fichier_zip->errorInfo(true));
	}
	else {
		$comment = unserialize($proprietes['comment']);
		$liste = $comment['contenu'];
		$id_auteur = $comment['auteur'];

		// On gere la compatibilite avec la structure des commentaires des versions < 0.2
		$auteur = _T('mes_fichiers:message_zip_auteur_indetermine');
		if ((!id_auteur) && (!$liste))
			$liste = $comment;
		else
			if (intval($id_auteur)) {
				$auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
			}else{
				$auteur = $id_auteur;
			}
		$resume .= _T('mes_fichiers:resume_zip_statut').' : '.$proprietes['status'].'<br />';
		$resume .= _T('mes_fichiers:resume_zip_auteur').' : '.$auteur.'<br />';
		$resume .= _T('mes_fichiers:resume_zip_compteur').' : '.$proprietes['nb'].'<br />';
		$resume .= _T('mes_fichiers:resume_zip_contenu').' : '.'<br />';
		$resume .= '<ul>';
		if ($liste)
			foreach ($liste as $_fichier) {
				$resume .= '<li>' . $_fichier . '</li>';
			}
		else
			$resume .= '<li>' . _T('mes_fichiers:message_zip_sans_contenu') . '</li>';
		$resume .= '</ul>';
	}
	return $resume;
}

// Renvoie la liste des fichiers et repertoires a sauver
function mes_fichiers_voir_zip($zip) {
	include_spip('inc/pclzip');
	$fichier_zip = new PclZip($zip);

	if (($list = $fichier_zip->listContent()) == 0) {
		spip_log('*** MES_FICHIERS (mes_fichiers_voir_zip) ERREUR '.$fichier_zip->errorInfo(true));
	}

	for ($i=0; $i<sizeof($list); $i++) {
		for(reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
			echo "File $i / [$key] = ".$list[$i][$key]."<br>";
		}
		echo "<br>";
	}
}

/**
 * On vérifie si on est dans une mutu, si oui on affiche un chemin plus propre
 *
 * @param string $rep
 */
function mes_fichiers_joli_repertoire($rep){
	if(defined('_DIR_SITE') && preg_match(','._DIR_SITE.',',$rep)){
		$rep = str_replace(_DIR_SITE,'',$rep);
		return $rep;
	}else{
		return joli_repertoire($rep);
	}
}

/**
 * Calculate the size of a directory by iterating its contents
 * http://aidanlister.com/2004/04/calculating-a-directories-size-in-php/
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.2.0
 * @link        http://aidanlister.com/repos/v/function.dirsize.php
 * @param       string   $directory    Path to directory
 */
function mes_fichiers_dirsize($path)
{
    // Init
    $size = 0;

    // Trailing slash
    if (substr($path, -1, 1) !== DIRECTORY_SEPARATOR) {
        $path .= DIRECTORY_SEPARATOR;
    }

    // Sanity check
    if (is_file($path)) {
        return filesize($path);
    } elseif (!is_dir($path)) {
        return false;
    }

    // Iterate queue
    $queue = array($path);
    for ($i = 0, $j = count($queue); $i < $j; ++$i)
    {
        // Open directory
        $parent = $i;
        if (is_dir($queue[$i]) && $dir = @dir($queue[$i])) {
            $subdirs = array();
            while (false !== ($entry = $dir->read())) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                // Get list of directories or filesizes
                $path = $queue[$i] . $entry;
                if (is_dir($path)) {
                    $path .= DIRECTORY_SEPARATOR;
                    $subdirs[] = $path;
                } elseif (is_file($path)) {
                    $size += filesize($path);
                }
            }

            // Add subdirectories to start of queue
            unset($queue[0]);
            $queue = array_merge($subdirs, $queue);

            // Recalculate stack size
            $i = -1;
            $j = count($queue);

            // Clean up
            $dir->close();
            unset($dir);
        }
    }
    return $size;
}

/**
 * Return human readable sizes
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.3.0
 * @link        http://aidanlister.com/repos/v/function.size_readable.php
 * @param       int     $size        size in bytes
 * @param       string  $max         maximum unit
 * @param       string  $system      'si' for SI, 'bi' for binary prefixes
 * @param       string  $retstring   return string format
 */
function mes_fichiers_size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
{
    // Pick units
    $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
    $systems['si']['size']   = 1000;
    $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
    $systems['bi']['size']   = 1024;
    $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

    // Max unit to display
    $depth = count($sys['prefix']) - 1;
    if ($max && false !== $d = array_search($max, $sys['prefix'])) {
        $depth = $d;
    }

    // Loop
    $i = 0;
    while ($size >= $sys['size'] && $i < $depth) {
        $size /= $sys['size'];
        $i++;
    }

    return sprintf($retstring, $size, $sys['prefix'][$i]);
}

?>
