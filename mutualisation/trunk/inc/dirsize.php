<?php
// Ce programme appelé en ajax renvoie une chaine de la forme XXX##erreur
//			où XXX vaut -1 si erreur, -2 si taille max atteinte et sinon la taille du répertoire en Mo
//			où erreur explicite le type d'arreur
// Param dir : répertoire à explorer
//       taille_max : taille max au delà de laquelle la recherche s'arrête et renvoie -2

$retour = dirsize($_GET['dir'],$_GET['taille_max']) ;
if ( is_numeric($retour) )
	echo round($retour/1024/1024,2);
else
	echo $retour ;


/**
 * Calculate the size of a directory by iterating its contents
 *
 * @author      Aidan Lister <aidan@php.net>, modifié par Yffic pour SPIP
 * @version     1.2.0
 * @link        http://aidanlister.com/2004/04/calculating-a-directories-size-in-php/
 * @param       string   $directory    Path to directory
 *              integer  $taille_max   Taille_max d'exploration
 */
function dirsize($path,$taille_max=0)
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
        return ("-1##".$path);
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
					 if($taille_max>0 && $size>$taille_max*1024*1024) return ("-2##".$queue[$i]) ;
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

?>