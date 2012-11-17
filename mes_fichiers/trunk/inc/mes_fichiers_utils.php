<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Renvoie la liste des fichiers et repertoires a sauver
function mes_fichiers_a_sauver() {
	// Dans le cas de la mutu, les fichiers de personnalisation sont places
	// - soit dans le site d'une sous-domaine, il faut donc aller les chercher a la racine de ce sous-domaine donc
	//   en utilisant _DIR_SITE
	// - soit dans le le site principal soit _DIR_RACINE
	if (defined('_DIR_SITE')) {
		// C'est une mutu
		$dir_site = _DIR_SITE;
	}
	else {
		$dir_site = _DIR_RACINE;
	}

	$htaccess = defined('_ACCESS_FILE_NAME') ? _DIR_RACINE._ACCESS_FILE_NAME : _DIR_RACINE.'.htaccess';
	$IMG = defined('_DIR_IMG') ? _DIR_IMG : $dir_site.'IMG/';
	$tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP : $dir_site.'tmp/dump/';
	$tmp_db = defined('_DIR_DB') ? _DIR_DB : $dir_site.'config/bases/';

	$liste = array();


	//le fichier sqlite depuis config/bases si le site utilise sqlite
	$db = preg_files($tmp_db);
	$fichier_db = '';
	$mtime = 0;
	foreach ($db as $_fichier_db) {
		if (($_mtime = filemtime($_fichier_db)) > $mtime) {
			$fichier_db = $_fichier_db;
			$mtime = $_mtime;
		}
	}
	if ($fichier_db)
		$liste[] = $fichier_db;

	// le fichier d'options si il existe
	if (@is_readable($f = $dir_site . _NOM_PERMANENTS_INACCESSIBLES . _NOM_CONFIG . '.php')
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
		// Dans le cas d'une mutu, la globale est toujours mise à jour sous la forme sites/domaine/squelettes
		// Cela revient donc au même que pour une install non mutualisée, il faut donc utiliser _DIR_RACINE
		foreach (explode(':', $GLOBALS['dossier_squelettes']) as $_dir) {
			$dir = ($_dir[0] == '/' ? '' : _DIR_RACINE) . $_dir . '/';
			if (@is_dir($dir))
				$liste[] = $dir;
		}
	else
		if (@is_dir($dir_site.'squelettes/'))
			$liste[] = $dir_site.'squelettes/';

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

function mes_fichiers_preparer_destinataires($quoi, $id, $options) {
	include_spip('inc/config');

	// Recuperation des destinataires configurés
	$mails = lire_config('mes_fichiers/notif_mail');
	$tous = ($mails) ? explode(',', $mails) : array();
	$tous[] = $GLOBALS['meta']['email_webmaster'];
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id,'options'=>$options),
			'data'=>$tous)
	);

	 // Nettoyage de la liste d'emails en vérifiant les doublons
	 // et la validité des emails
	notifications_nettoyer_emails($destinataires);

	return $destinataires;
}

?>
