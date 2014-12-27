<?php
/**
 * Fonctions utiles au plugin Lister les logos
 *
 * @plugin     Lister les logos
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Lister_logos\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');

/**
 * Récupérer les infos à partir du fichier de logo
 *
 * @param  string  $fichier
 *         Chemin ou nom du fichier de logo
 * @param  int     $index
 *         - Si l'index est `null`, on affichera un tableau représentant le résultat de preg_match()
 *         - `$index = 0` : retourne `$fichier` ;
 *         - `$index = 1` : le type de logo de l'objet. cf. art, rub, mot, etc.
 *         - `$index = 2` : l'état du logo. cf. `on` pour normal, `off` pour survol.
 *         - `$index = 3` : l'id de l'objet.
 *         - `$index = 4` : l'extension du fichier.
 * @return array|string
 *         Si l'index est `null`, on retournera le résultat que `preg_match()`, soit un tableau.
 *         Si l'index est une valeur numérique (<4), on retourne la valeur du tableau correspondant à l'index.
 */
function logo_infos($fichier, $index = null)
{
    // Fonction one ne peut plus simple.
    preg_match("/\/(\w+)(on|off)(\d+).(\w+)$/", $fichier, $r);
    if (isset($index) and intval($index)) {
        return $r[$index];
    }
    return $r;
}

/**
 * Avoir l'état du logo
 *
 * @uses   logo_infos()
 * @param  string $fichier
 *         Le fichier du logo.
 * @return string
 *         Retourne l'état du logo :
 *         - Logo normal (on) ;
 *         - Logo de survol (off).
 */
function logo_etat($fichier)
{
    $infos = logo_infos($fichier);

    if ($infos[2] == 'on') {
        return _T('lister_logos:logo_on');
    } else {
        return _T('lister_logos:logo_off');
    }

}

/**
 * Lister les logos des objets éditoriaux
 * Prend en compte les cas particuliers suivants :
 * - articles (art)
 * - rubriques (rub)
 * - sites syndiqués (site)
 * - auteurs (aut)
 * Cette fonction est reprise du plugin Nettoyer la médiathèque.
 *
 * @uses lister_tables_principales()
 *       liste en spip 3 les tables principales reconnues par SPIP
 * @uses id_table_objet()
 *       retourne la clé primaire de l'objet
 * @uses type_du_logo()
 *       retourne le type de logo tel que `art` depuis le nom de la clé primaire de l'objet
 *
 * @param null|string $table
 *        Si `null`, on liste tous les logos des objets éditoriaux
 *        Si `string`, on prend le nom de la table renseignée.
 * @param null|string $mode
 *        + `null` : stockera dans le tableau tous les logos,
 *        quelque soit le mode du logo
 *        + `on` : stockera dans le tableau tous les logos du mode "on"
 *        + `off` : stockera dans le tableau tous les logos du mode "off"
 * @param string $repertoire_img
 *        On peut passer un nom de répertoire/chemin en paramètre.
 *        Par défaut, on prend le répertoire IMG/
 * @return array
 */
function lister_logos_fichiers ($table = null, $mode = null, $constante = null, $repertoire_img = _DIR_IMG)
{

    include_spip('inc/chercher_logo');
    include_spip('base/abstract_sql');
    include_spip('base/objets');

    if (!is_null($table)) {
        $tables_objets = array($table);
    } else {
        $tables_objets = array_keys(lister_tables_principales());
    }
    sort($tables_objets);

    global $formats_logos;
    $docs_fichiers_on   = array();
    $docs_fichiers_off  = array();

    // On va chercher toutes les tables principales connues de SPIP
    foreach ($tables_objets as $table) {
        // On cherche son type d'objet.
        // Il y a aussi dans ces objets la référence à `article`,
        // `rubrique` et `auteur`
        // Grâce à la fonction `id_table_objet()`, on retrouve le nom de la clé primaire de l'objet.
        // `type_du_logo()` retourne le type de logo tel que `art` depuis le nom de la clé primaire de l'objet
        $type_du_logo = type_du_logo(id_table_objet($table));

        // On va chercher dans IMG/$type_du_logo(on|off)*.*
        // On fait un foreach pour ne pas avoir de
        // "Pattern exceeds the maximum allowed length of 260 characters"
        // sur glob()
        $liste = glob($repertoire_img . "{" . $type_du_logo ."}{on,off}*.*", GLOB_BRACE);

        // Il faut avoir au moins un élément dans le tableau de fichiers.
        if (is_array($liste) and count($liste) > 0) {
            foreach ($liste as $fichier) {
                // ... Donc on fait une regex plus poussée avec un preg_match
                if (
                    preg_match(
                        "/("
                        . $type_du_logo
                        .")on(\d+).("
                        . join("|", $formats_logos)
                        .")$/",
                        $fichier,
                        $r
                    )
                ) {
                    $docs_fichiers_on[] = preg_replace("/\/\//", "/", $fichier);
                }
                if (
                    preg_match(
                        "/("
                        . $type_du_logo
                        .")off(\d+).("
                        . join("|", $formats_logos)
                        .")$/",
                        $fichier,
                        $r
                    )
                ) {
                    $docs_fichiers_off[] = preg_replace("/\/\//", "/", $fichier);
                }
            }
        }
    }

    // Si on a un mode
    switch ($mode) {
        case 'on':
            $docs_fichiers_on = array_unique($docs_fichiers_on);
            sort($docs_fichiers_on); // On trie dans l'ordre alphabétique
            $docs_fichiers = $docs_fichiers_on;
            break;
        case 'off':
            $docs_fichiers_off = array_unique($docs_fichiers_off);
            sort($docs_fichiers_off); // On trie dans l'ordre alphabétique
            $docs_fichiers = $docs_fichiers_off;
            break;
        default:
            $docs_fichiers = array_unique(array_merge($docs_fichiers_on, $docs_fichiers_off));
            sort($docs_fichiers); // On trie dans l'ordre alphabétique
            break;
    }

    // On s'occupe de la constante
    switch ($constante) {
        case 'max_size':
            foreach ($docs_fichiers as $key => $fichier) {
                $poids = filesize($fichier);
                if (defined('_LOGO_MAX_SIZE') and ($poids/1024) < _LOGO_MAX_SIZE) {
                    unset($docs_fichiers[$key]);
                }
            }
            break;
        case 'max_width':
            foreach ($docs_fichiers as $key => $fichier) {
                if (defined('_LOGO_MAX_WIDTH') and ($size = getimagesize($fichier) and $size[0] < _LOGO_MAX_WIDTH)) {
                    unset($docs_fichiers[$key]);
                }
            }
            break;
        case 'max_height':
            foreach ($docs_fichiers as $key => $fichier) {
                if (defined('_LOGO_MAX_HEIGHT') and ($size = getimagesize($fichier) and $size[1] < _LOGO_MAX_HEIGHT)) {
                    unset($docs_fichiers[$key]);
                }
            }
            break;
        default:
            break;
    }

    return $docs_fichiers;
}
?>